<?php
    require_once 'geometry.php';
    include 'env.php';

define("REFRESH_PERIOD", 6);
define("EXPIRY_TIME", 60);
define("RECOVER_EXPIRY_TIME", 3*60*60);
define("MAX_REQUEST_DISTANCE", 10000); // maximum distance from the taxi

function cleanup($string){
    if (get_magic_quotes_gpc()) {
        $string = stripslashes($string);
    }
    return $string;
}

function set_http_error($code, $text) {
    $protocol = isset($_SERVER['SERVER_PROTOCOL'])
            ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
    header($protocol . ' ' . $code . ' ' . $text);
}

function make_url($path) {
    if (isset($_SERVER['HTTPS'])) {
        $default_port = '443';
        $url = "https://";
    } else {
        $default_port = '80';
        $url = "http://";
    }
    $url .= $_SERVER['SERVER_NAME'];
    if ($_SERVER['SERVER_PORT'] !== $default_port) {
        $url .= ":" . $_SERVER['SERVER_PORT'];
    }
    $dir = dirname($_SERVER['SCRIPT_NAME']);
    while (true) {
        if (strpos($path,"./") === 0) {
            $path = substr($path, 2);
        } else if (strpos($path,"../") === 0) {
            $path = substr($path, 3);
            $dir = dirname($dir);
        } else {
            break;
        }
    }
    if ($dir === "/") {
        $dir = "";
    }
    return $url . $dir . "/" . $path;
}

function connect_db() {
    $mysql = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWD, DB_DBNAME,
            DB_PORT);
    if (mysqli_connect_errno()) {
        set_http_error(500, "Connection to database failed " .
                mysqli_connect_error());
        exit;
    }
    mysqli_autocommit($mysql, true);
    if (!mysqli_query($mysql, "SET NAMES 'utf8'")) {
	    mysqli_close($mysql);
        set_http_error(500, "Connection to database failed " .
                mysqli_connect_error());
        exit;
    }
    return $mysql;
}

function log_event($mysql, $user_id, $request_id, $event_type,
        $message, $latitude, $longitude) {
    $date = date("Y-m-d H:i:s");
    $stmt = mysqli_prepare($mysql, "INSERT INTO eventlog(user_id,request_id,"
            . "logtime,event_type,message,latitude,longitude)"
            . "VALUES(?,?,?,?,?,?,?)");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param($stmt, "iisssdd", $user_id, $request_id,
            $date, $event_type, $message, $latitude, $longitude);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error inserting request" . mysqli_error($mysql));
        exit;
    }
    mysqli_stmt_close($stmt);
}

function disposable_email_domains() {
    return file(__DIR__. "/disposable-email-domains.txt",
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

function register_request($mysql, $latitude, $longitude, $accuracy, $altitude,
        $address, $locality, $country, $state, $user_id) {
    $date = date("Y-m-d H:i:s");
    $stmt = mysqli_prepare($mysql, "INSERT INTO requests(status,creationtime,"
            . "latitude,longitude,accuracy,altitude,address,locality,country,"
            . "state,user_id) "
            . "VALUES('req',?,?,?,?,?,?,?,?,?,?)");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param($stmt, "sddddssssi", $date, $latitude,
            $longitude, $accuracy, $altitude, $address, $locality, $country,
            $state, $user_id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error inserting request" . mysqli_error($mysql));
        exit;
    }
    $id = mysqli_insert_id($mysql);
    mysqli_stmt_close($stmt);
    return array(
        "id" => $id,
        "sts" => 'req',
        "crt" => $date,
        "lat" => $latitude,
        "lng" => $longitude,
        "acc" => $accuracy,
        "alt" => $altitude,
        "adr" => $address,
        "loc" => $locality,
        "cny" => $country,
        "sta" => $state,
        "acd" => false
    );
}

function cancel_request($mysql, $id) {
    $stmt = mysqli_prepare($mysql,
            "UPDATE requests SET status='can' WHERE id=?");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param($stmt, "i", $id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    mysqli_stmt_close($stmt);
}

function release_request($mysql, $taxi, $request_id) {
    $taxi_id = $taxi["tid"];
    $stmt = mysqli_prepare($mysql,
            "UPDATE requests SET status='don' "
            . "WHERE id=? AND taxi_id=?");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param($stmt, "ii", $request_id, $taxi_id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $rows == 1;
}

function assign_request($mysql, $id, $taxi_id) {
    mysqli_autocommit($mysql, false);
    $stmt = mysqli_prepare($mysql,
            "UPDATE requests SET status='att', taxi_id=? "
            . "WHERE id=? AND status='req'");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param($stmt, "ii", $taxi_id, $id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    if ($rows == 1) {
        // disconnect taxi from other requests
        $stmt = mysqli_prepare($mysql,
                "DELETE FROM requests_taxis WHERE taxi_id=?");
        if (!$stmt) {
            set_http_error(500, "Error preparing statement"
                    . mysqli_error($mysql));
            mysqli_rollback($mysql);
            exit;
        }
        $res = mysqli_stmt_bind_param($stmt, "i", $taxi_id);
        if (!$res) {
            set_http_error(500, "Error binding parameters"
                    . mysqli_error($mysql));
            mysqli_rollback($mysql);
            exit;
        }
        $res2 = mysqli_stmt_execute($stmt);
        if (!$res2) {
            set_http_error(500, "Error executing query" . mysqli_error($mysql));
            mysqli_rollback($mysql);
            exit;
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_commit($mysql);
    mysqli_autocommit($mysql, true);
    return $rows == 1;
}

function select_taxi($mysql, $id) {
    $stmt = mysqli_prepare($mysql, "SELECT r.latitude,r.longitude,t.latitude,"
            . "t.longitude,t.id,t.plate_number,t.description,username,phone "
            . "FROM requests_taxis rt, requests r, taxis t, users u "
            . "WHERE rt.request_id=? AND r.id=rt.request_id "
            . "AND t.id=rt.taxi_id and u.id=t.user_id "
            . "AND r.status='req'");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param($stmt, "i", $id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $res3 = mysqli_stmt_bind_result($stmt, $latitude, $longitude, $t_latitude,
            $t_longitude, $taxi_id, $plate_number, $description, $username,
            $phone);
    if (!$res3) {
        set_http_error(500, "Error binding results" . mysqli_error($mysql));
        exit;
    }
    // select the closest taxi having accepted the request
    $taxi = null;
    $min_dist = PHP_INT_MAX;
    while (mysqli_stmt_fetch($stmt)) {
        $dist = distance($latitude, $longitude, $t_latitude, $t_longitude,
                ELEVATION);
        if ($dist < $min_dist) {
            $min_dist = $dist;
            $taxi = array(
                    "sts" => "ok",
                    "tid" => $taxi_id,
                    "pln" => $plate_number,
                    "dsc" => $description,
                    "usn" => $username,
                    "tel" => $phone,
                    "etm" => travel_time($dist),
                    "lat" => $t_latitude,
                    "lng" => $t_longitude
                );
        }
    }
    mysqli_stmt_close($stmt);
    if (!$taxi) {
        cancel_request($mysql, $id);
    } else if (!assign_request($mysql, $id, $taxi["tid"])) {
        $taxi = null;
    }
    if ($taxi == null) {
        return get_callcenter($mysql, $id);
    }
    return $taxi;
}

function recover_request($mysql, $id) {
    $now = time();
    $stmt = mysqli_prepare($mysql, "SELECT status,creationtime,r.latitude,"
            . "r.longitude,accuracy,altitude,address,locality,country,state,"
            . "t.id,t.latitude,t.longitude,plate_number,description,username,"
            . "phone "
            . "FROM requests r, taxis t, users u "
            . "WHERE r.id=? AND t.id=r.taxi_id AND u.id=t.user_id "
            . "AND status NOT IN ('req','can','don') "
            . "AND r.creationtime>=?");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $beginTime = date("Y-m-d H:i:s", $now - RECOVER_EXPIRY_TIME);
    $res = mysqli_stmt_bind_param($stmt, "is", $id, $beginTime);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $res3 = mysqli_stmt_bind_result($stmt, $status, $request_time, $latitude,
            $longitude, $accuracy, $altitude, $address, $locality, $country,
            $state, $taxi_id, $t_latitude, $t_longitude, $plate_number,
            $description, $username, $phone);
    if (!$res3) {
        set_http_error(500, "Error binding results" . mysqli_error($mysql));
        exit;
    }
    $ok = mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    if (!$ok) {
        return null;
    }
    $dist = distance($latitude, $longitude, $t_latitude, $t_longitude,
            ELEVATION);
    $req = array(
        "id" => $id,
        "sts" => $status,
        "crt" => $request_time,
        "lat" => $latitude,
        "lng" => $longitude,
        "acc" => $accuracy,
        "alt" => $altitude,
        "adr" => $address,
        "loc" => $locality,
        "cny" => $country,
        "sta" => $state,
        "dst" => $dist,
    );
    if ($taxi_id) {
        $req["taxi"] = array(
            "tid" => $taxi_id,
            "pln" => $plate_number,
            "dsc" => $description,
            "usn" => $username,
            "tel" => $phone,
            "etm" => travel_time($dist),
            "lat" => $t_latitude,
            "lng" => $t_longitude
        );
    }
    return $req;
}

function request_taxi($mysql, $id) {
    $stmt = mysqli_prepare($mysql, "SELECT r.latitude,r.longitude,t.latitude,"
            . "t.longitude,t.id,t.plate_number,t.description,username,phone "
            . "FROM requests r, taxis t, users u "
            . "WHERE r.id=? AND t.id=r.taxi_id AND u.id=t.user_id "
            . "AND r.status='att'");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param($stmt, "i", $id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $res3 = mysqli_stmt_bind_result($stmt, $latitude, $longitude, $t_latitude,
            $t_longitude, $taxi_id, $plate_number, $description, $username,
            $phone);
    if (!$res3) {
        set_http_error(500, "Error binding results" . mysqli_error($mysql));
        exit;
    }
    // select the closest taxi having accepted the request
    $taxi = null;
    if (mysqli_stmt_fetch($stmt)) {
        $dist = distance($latitude, $longitude, $t_latitude, $t_longitude,
                ELEVATION);
        $taxi = array(
                "sts" => "ok",
                "tid" => $taxi_id,
                "pln" => $plate_number,
                "dsc" => $description,
                "usn" => $username,
                "tel" => $phone,
                "etm" => travel_time($dist),
                "lat" => $t_latitude,
                "lng" => $t_longitude
            );
    }
    mysqli_stmt_close($stmt);
    return $taxi;
}

function get_callcenter($mysql, $id) {
    $stmt = mysqli_prepare($mysql, "SELECT c.title,c.phone "
            . "FROM callcenters c, requests r "
            . "WHERE r.id=? AND r.country=c.country AND r.state=c.state");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param($stmt, "i", $id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $res3 = mysqli_stmt_bind_result($stmt, $title, $phone);
    if (!$res3) {
        set_http_error(500, "Error binding results" . mysqli_error($mysql));
        exit;
    }
    $callcenter = null;
    while (mysqli_stmt_fetch($stmt)) {
        $callcenter = array(
            "sts" => "cc",
            "ttl" => $title,
            "tel" => $phone
        );
    }
    mysqli_stmt_close($stmt);
    return $callcenter;
}

function try_authenticate($mysql) {
    $realm = REALM;

    // Get the digest from the http header
    $digest = getDigest();

    // If there was no digest, show login
    if (is_null($digest)) {
        return null;
    }

    $digestParts = digestParse($digest);

    $user = find_user($mysql, $digestParts["username"]);
    if(!$user) {
        return null;
    }

    // Based on all the info we gathered we can figure out what the response should be
    $A1 = $user["pwd"];
    $A2 = md5($_SERVER['REQUEST_METHOD'] . ":" . $digestParts['uri']);

    $validResponse = md5($A1 . ":" . $digestParts['nonce'] . ":"
            . $digestParts['nc'] . ":" . $digestParts['cnonce'] . ":"
            . $digestParts['qop'] . ":" . $A2);

    if ($digestParts['response'] != $validResponse) {
        return null;
    }
    unset($user["pwd"]);
    return $user;
}

function authenticate($mysql) {
    $user = try_authenticate($mysql);
    if (!$user) {
        requireLogin(REALM);
        return null;
    }
    return $user;
}

function authenticate_admin($mysql) {
    $user = authenticate($mysql);
    if (!$user || !$user["adm"]) {
        requireLogin(REALM);
        return null;
    }
    return $user;
}

function authenticate_taxi($mysql) {
    $user = authenticate($mysql);
    if ($user && !$user["tid"]) {
        requireLogin(REALM);
        return null;
    }
    return $user;
}

// This function returns the digest string
function getDigest() {

    // mod_php
    if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
        $digest = $_SERVER['PHP_AUTH_DIGEST'];
    // most other servers
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {

            if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'digest')===0)
              $digest = substr($_SERVER['HTTP_AUTHORIZATION'], 7);
    }

    return $digest;
}

// This function forces a login prompt
function requireLogin($realm) {
    // Just a random id
    $nonce = uniqid();
    header('WWW-Authenticate: Digest realm="' . $realm . '",qop="auth",nonce="'
            . $nonce . '",opaque="' . md5($realm) . '"');
    header('HTTP/1.0 401 Unauthorized');
}

// This function extracts the separate values from the digest string
function digestParse($digest) {
    // protect against missing data
    $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1,
        'username'=>1, 'uri'=>1, 'response'=>1);
    $data = array();

    preg_match_all('@(\w+)=(?:(?:")([^"]+)"|([^\s,$]+))@', $digest, $matches,
            PREG_SET_ORDER);

    foreach ($matches as $m) {
        $data[$m[1]] = $m[2] ? $m[2] : $m[3];
        unset($needed_parts[$m[1]]);
    }

    return $needed_parts ? false : $data;
}

function find_user($mysql, $username) {
    $stmt = mysqli_prepare($mysql, "SELECT u.id,t.id,plate_number,description,"
            . "username,password_hash,email,phone,latitude,longitude,admin "
            . "FROM users u LEFT OUTER JOIN taxis t ON u.taxi_id=t.id "
            . "WHERE active=1 AND username=?");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param(
            $stmt, "s", $username);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $res3 = mysqli_stmt_bind_result($stmt, $id, $tid, $plate_number,
            $description, $username, $password_hash, $email, $phone, $latitude,
            $longitude, $admin);
    if (!$res3) {
        set_http_error(500, "Error binding results" . mysqli_error($mysql));
        exit;
    }
    $ok = mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    if (!ok) {
        return null;
    }
    return array(
        "id" => $id,
        "usn" => $username,
        "tid" => $tid,
        "pln" => $plate_number,
        "dsc" => $description,
        "usr" => $username,
        "pwd" => $password_hash,
        "mal" => $email,
        "tel" => $phone,
        "lat" => $latitude,
        "lng" => $longitude,
        "adm" => $admin
    );
}

function find_users_by_email($mysql, $email) {
    $stmt = mysqli_prepare($mysql, "SELECT id,username,email,phone "
            . "FROM users WHERE active=1 AND email=?");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param($stmt, "s", $email);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $res3 = mysqli_stmt_bind_result($stmt, $id, $username, $email, $phone);
    if (!$res3) {
        set_http_error(500, "Error binding results" . mysqli_error($mysql));
        exit;
    }
    $result = array();
    while (mysqli_stmt_fetch($stmt)) {
        array_push($result, array(
            "id" => $id,
            "usn" => $username,
            "usr" => $username,
            "mal" => $email,
            "tel" => $phone,
        ));
    }
    mysqli_stmt_close($stmt);
    return $result;
}
function reset_password($mysql, $usn, $rsk, $pwd, $con) {
    if ($new != $conf) {
        return FALSE;
    }
    $stmt = mysqli_prepare($mysql, "SELECT id,username,email,phone "
            . "FROM users WHERE active=1 AND username=?");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param($stmt, "s", $usn);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $res3 = mysqli_stmt_bind_result($stmt, $id, $username, $email, $phone);
    if (!$res3) {
        set_http_error(500, "Error binding results" . mysqli_error($mysql));
        exit;
    }
    $ok = mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    if (!$ok) {
        return FALSE;
    }
    $hash = md5($id . ":" . $username . ":" . $email . ":" . $phone);
    if ($hash != $rsk) {
        return FALSE;
    }

    $stmt = mysqli_prepare($mysql, "UPDATE users SET password_hash=? "
            . "WHERE id=?");
    if (!$stmt) {
        set_http_error(500,
                "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $pwd = md5($username . ":" . REALM . ":" . $pwd);
    $res = mysqli_stmt_bind_param($stmt, "si", $pwd, $id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $rows == 1;
}

function change_password($mysql, $username, $old, $new, $conf) {
    if ($new != $conf) {
        return array("sts" => "dif");
    }
    $stmt = mysqli_prepare($mysql, "UPDATE users SET password_hash=? "
            . "WHERE username=? AND password_hash=?");
    if (!$stmt) {
        set_http_error(500,
                "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $old = md5($username . ":" . REALM . ":" . $old);
    $new = md5($username . ":" . REALM . ":" . $new);
    $res = mysqli_stmt_bind_param($stmt, "sss", $new, $username, $old);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    if ($rows == 0) {
        return array("sts" => "ko");
    } else {
        return array("sts" => "ok");
    }
}

function is_username_available($mysql, $user) {
    $stmt = mysqli_prepare($mysql, "SELECT id FROM users WHERE username=?");
    if (!$stmt) {
        set_http_error(500,
                "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param($stmt, "s", $user);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $res3 = mysqli_stmt_bind_result($stmt, $dummy);
    if (!$res3) {
        set_http_error(500, "Error binding results" . mysqli_error($mysql));
        exit;
    }
    $result = true;
    while (mysqli_stmt_fetch($stmt)) {
        $result = false;
    }
    mysqli_stmt_close($stmt);
    return $result;
}

function create_user($mysql, $user) {
    if ($user["pwd"] != $user["con"]) {
        set_http_error(500, "Invalid password");
        exit;
    }
    $date = date("Y-m-d H:i:s");
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = mysqli_prepare($mysql, "INSERT INTO users(username,email,phone,"
            . "password_hash,creationtime,ipaddr,active)"
            . "VALUES(?,?,?,?,?,?,0)");
    if (!$stmt) {
        set_http_error(500,
                "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $id = $user["id"];
    $username = $user["usn"];
    $email = $user["mal"];
    $phone = $user["tel"];
    $pwd = $user["pwd"];
    $pwd = md5($username . ":" . REALM . ":" . $pwd);

    $res = mysqli_stmt_bind_param(
            $stmt, "ssssss", $username, $email, $phone, $pwd, $date, $ip);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $id = mysqli_insert_id($mysql);
    mysqli_stmt_close($stmt);
    return $id;
}

function active_users($mysql) {
    $stmt = mysqli_prepare($mysql, "SELECT id,username,password_hash,email,"
            . "phone,admin "
            . "FROM users u WHERE active=1");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $res3 = mysqli_stmt_bind_result($stmt, $id, $username, $password_hash,
            $email, $phone, $admin);
    if (!$res3) {
        set_http_error(500, "Error binding results" . mysqli_error($mysql));
        exit;
    }
    $result = array();
    while (mysqli_stmt_fetch($stmt)) {
        array_push($result, array(
            "id" => $id,
            "usn" => $username,
            "pwd" => $password_hash,
            "mal" => $email,
            "tel" => $phone,
            "adm" => $admin
        ));
    }
    mysqli_stmt_close($stmt);
    return $result;
}

function activate_user($mysql, $username, $key) {
    $stmt = mysqli_prepare($mysql, "SELECT id,username,email,phone "
            . "FROM users u WHERE active=0 AND username=?");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param(
            $stmt, "s", $username);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $res3 = mysqli_stmt_bind_result($stmt, $id, $username, $email, $phone);
    if (!$res3) {
        set_http_error(500, "Error binding results" . mysqli_error($mysql));
        exit;
    }
    $ok = mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    if (!ok) {
        return FALSE;
    }
    $hash = md5($id . ":" . $username . ":" . $email . ":" . $phone);
    if ($hash != $key) {
        return FALSE;
    }
    $stmt = mysqli_prepare($mysql, "UPDATE users SET active=1 "
            . "WHERE id=?");
    if (!$stmt) {
        set_http_error(500,
                "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param($stmt, "i", $id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    mysqli_stmt_close($stmt);
    return TRUE;
}

function put_driver_info($mysql, $user) {
    $stmt = mysqli_prepare($mysql, "UPDATE users "
            . "SET email=?,phone=? "
            . "WHERE id=?");
    if (!$stmt) {
        set_http_error(500,
                "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $id = $user["id"];
    $email = $user["mal"];
    $phone = $user["tel"];

    $res = mysqli_stmt_bind_param(
            $stmt, "ssi", $email, $phone, $id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    mysqli_stmt_close($stmt);
}

function remove_vehicle_info($mysql, $user_id) {
    $stmt = mysqli_prepare($mysql, "UPDATE users SET taxi_id=NULL WHERE id=?");
    if (!$stmt) {
        set_http_error(500,
                "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param($stmt, "i", $user_id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    mysqli_stmt_close($stmt);
}

function update_vehicle_info($mysql, $taxi) {
    $stmt = mysqli_prepare($mysql, "UPDATE taxis "
            . "SET plate_number=?,description=? WHERE id=?");
    if (!$stmt) {
        set_http_error(500,
                "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $taxi_id = $taxi["tid"];
    $plate_number = $taxi["pln"];
    $description = $taxi["dsc"];
    $res = mysqli_stmt_bind_param(
            $stmt, "ssi", $plate_number, $description, $taxi_id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    mysqli_stmt_close($stmt);
}

function insert_vehicle_info($mysql, $user_id, $taxi) {
    mysqli_autocommit($mysql, false);
    $stmt = mysqli_prepare($mysql,"INSERT INTO taxis(plate_number,description,"
            . "user_id) VALUES(?,?,?)");
    if (!$stmt) {
        set_http_error(500,
                "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $plate_number = $taxi["pln"];
    $description = $taxi["dsc"];

    $res = mysqli_stmt_bind_param(
            $stmt, "ssi", $plate_number, $description, $user_id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $taxi_id = mysqli_insert_id($mysql);
    mysqli_stmt_close($stmt);
    
    $stmt = mysqli_prepare($mysql, "UPDATE users SET taxi_id=? WHERE id=?");
    if (!$stmt) {
        set_http_error(500,
                "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param($stmt, "ii", $taxi_id, $user_id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    mysqli_commit($mysql);
    mysqli_autocommit($mysql, true);
}

function cmp_distance($a, $b) {
    $da = $a["dst"];
    $db = $b["dst"];
    if ($da < $db) {
        return -1;
    } else if ($da > $db) {
        return 1;
    } else {
        return 0;
    }
}

function get_compatible_requests($mysql, $taxi, $t_latitude, $t_longitude) {
    $now = time();
    update_taxi_position($mysql, $taxi["tid"], $t_latitude, $t_longitude);
    $stmt = mysqli_prepare($mysql, "SELECT r.id,status,r.creationtime,latitude,"
            . "longitude,accuracy,altitude,address,locality,country,state,"
            . "username,email,phone,"
            . "(SELECT 1 FROM requests_taxis rt "
            . "WHERE rt.request_id=r.id AND rt.taxi_id=?) accepted "
            . "FROM requests r, users u WHERE r.user_id=u.id "
            . "AND ((status='att' AND r.taxi_id=?) OR (status='req' "
            . "AND r.creationtime>=?))");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $beginTime = date("Y-m-d H:i:s", $now - EXPIRY_TIME);
    $t_id = $taxi["tid"];
    $res = mysqli_stmt_bind_param(
            $stmt, "iis", $t_id, $t_id, $beginTime);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $res3 = mysqli_stmt_bind_result($stmt, $id, $status, $creationtime,
            $latitude, $longitude, $accuracy, $altitude, $address, $locality,
            $country, $state, $username, $email, $phone, $accepted);
    if (!$res3) {
        set_http_error(500, "Error binding results" . mysqli_error($mysql));
        exit;
    }
    $result = array();
    while (mysqli_stmt_fetch($stmt)) {
        $request_time = strtotime($creationtime);
        $distance = distance($t_latitude, $t_longitude, $latitude, $longitude,
                ELEVATION);
        $corrected_time = $request_time + REFRESH_PERIOD;
        if ($corrected_time < $now + REFRESH_PERIOD
                && $distance <= MAX_REQUEST_DISTANCE) {
            array_push($result, array(
                "id" => $id,
                "sts" => $status,
                "crt" => $request_time,
                "lat" => $latitude,
                "lng" => $longitude,
                "acc" => $accuracy,
                "alt" => $altitude,
                "adr" => $address,
                "loc" => $locality,
                "cny" => $country,
                "sta" => $state,
                "usn" => $username,
                "mal" => $email,
                "tel" => $phone,
                "dst" => $distance,
                "ctm" => intval($corrected_time),
                "etm" => intval(travel_time($distance)),
                "acd" => $accepted ? true : false
            ));
        }
    }
    mysqli_stmt_close($stmt);
    usort($result, "cmp_distance");
    return $result;
}

function update_taxi_position($mysql, $taxi_id, $latitude, $longitude) {
    $stmt = mysqli_prepare($mysql,
            "UPDATE taxis SET update_time=?, latitude=?, longitude=? "
            . "WHERE id=?");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $date = date("Y-m-d H:i:s");
    $res = mysqli_stmt_bind_param($stmt, "sddi", $date, $latitude, $longitude,
            $taxi_id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing insert" . mysqli_error($mysql));
        exit;
    }
    $rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $rows == 1;
}

function accept_request($mysql, $taxi_id, $request_id) {
    $stmt = mysqli_prepare($mysql,
            "INSERT INTO requests_taxis(request_id,taxi_id)"
            . "SELECT DISTINCT r.id, t.id "
            . "FROM requests r, taxis t "
            . "WHERE NOT EXISTS("
                . "SELECT 1 FROM requests_taxis rt "
                . "WHERE rt.request_id=r.id AND rt.taxi_id=t.id)"
            . "AND r.id=? AND t.id=? AND r.status='req'");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param($stmt, "ii", $request_id, $taxi_id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing insert" . mysqli_error($mysql));
        exit;
    }
    $rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $rows == 1;
}

function unaccept_request($mysql, $taxi_id, $request_id) {
    $stmt = mysqli_prepare($mysql, "DELETE rt "
            . "FROM requests_taxis rt, requests r "
            . "WHERE rt.request_id=? AND rt.taxi_id=? "
            . "AND r.id=rt.request_id AND r.status='req'");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param($stmt, "ii", $request_id, $taxi_id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing insert" . mysqli_error($mysql));
        exit;
    }
    $rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $rows == 1;
}

function check_confirmation($mysql, $taxi_id, $request_id) {
    $stmt = mysqli_prepare($mysql,
            "SELECT id,status,r.creationtime,r.latitude,r.longitude,"
            . "accuracy,altitude,address,country,state "
            . "FROM requests_taxis rt, requests r "
            . "WHERE r.id=rt.request_id "
            . "AND rt.request_id=? AND rt.taxi_id=?");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param($stmt, "ii", $request_id, $taxi_id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing insert" . mysqli_error($mysql));
        exit;
    }
    $status = '';
    $res3 = mysqli_stmt_bind_result($stmt, $id, $status, $creationtime,
            $latitude, $longitude, $accuracy, $altitude, $address, $country,
            $state);
    if (!$res3) {
        set_http_error(500, "Error binding results" . mysqli_error($mysql));
        exit;
    }
    if (!mysqli_stmt_fetch($stmt)) {
        $result = array("sts" => "");
    } else {
        $request_time = strtotime($creationtime);
        $result = array(
            "id" => $id,
            "sts" => $status,
            "crt" => $request_time,
            "lat" => $latitude,
            "lng" => $longitude,
            "acc" => $accuracy,
            "alt" => $altitude,
            "adr" => $address,
            "cny" => $country,
            "sta" => $state,
        );
    }
    mysqli_stmt_close($stmt);
    return $result;
}

function get_monthly_results($mysql, $year, $month, $event_type) {
    $stmt = mysqli_prepare($mysql, "SELECT u.id,plate_number,description,"
            . "username,email,phone,latitude,longitude,total "
            . "FROM taxis t JOIN users u JOIN monthly_total mt ON id=taxi_id "
            . "WHERE year=? AND month=? AND event_type=? "
            . "ORDER BY total DESC");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param(
            $stmt, "iis", $year, $month, $event_type);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $res3 = mysqli_stmt_bind_result($stmt, $id, $plate_number, $description,
            $username, $email, $phone, $latitude, $longitude, $total);
    if (!$res3) {
        set_http_error(500, "Error binding results" . mysqli_error($mysql));
        exit;
    }
    $result = array();
    while (mysqli_stmt_fetch($stmt)) {
        array_push($result, array(
            "id" => $id,
            "pln" => $plate_number,
            "dsc" => $description,
            "usr" => $username,
            "mal" => $email,
            "tel" => $phone,
            "lat" => $latitude,
            "lng" => $longitude,
            "tot" => $total,
        ));
    }
    mysqli_stmt_close($stmt);
    return $result;
}

function error_page($message) {
    header("Content-Type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
        <title>Erreur</title>
    </head>
    <body>
        <p><?php echo htmlspecialchars($message, ENT_COMPAT, "UTF-8"); ?></p>
    </body>
</html>
<?php
}
?>
