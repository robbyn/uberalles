CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    taxi_id INT REFERENCES taxis(id),

    username VARCHAR(127) NOT NULL,
    password_hash VARCHAR(32) NOT NULL,

    email VARCHAR(127) NOT NULL,
    phone VARCHAR(15) NOT NULL,

    active BOOLEAN NOT NULL DEFAULT 0,
    admin BOOLEAN NOT NULL DEFAULT 0,
    comment TEXT,

    creationtime DATETIME,
    ipaddr VARCHAR(127)
) ENGINE=InnoDB;

CREATE UNIQUE INDEX users_username ON users(username);
CREATE INDEX users_email ON users(email);

CREATE TABLE taxis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id),

    plate_number VARCHAR(20) NOT NULL,
    description VARCHAR(255) NOT NULL,

    update_time DATETIME,
    latitude DOUBLE,
    longitude DOUBLE
) ENGINE=InnoDB;

CREATE TABLE requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id),
    taxi_id INT REFERENCES taxis(id),
    status VARCHAR(6),
    creationtime DATETIME NOT NULL,
    latitude DOUBLE NOT NULL,
    longitude DOUBLE NOT NULL,
    accuracy DOUBLE,
    altitude DOUBLE,
    address VARCHAR(255),
    locality VARCHAR(127),
    country VARCHAR(3),
    state VARCHAR(2)
) ENGINE=InnoDB;

CREATE INDEX requests_status ON requests(status);
CREATE INDEX requests_latitude ON requests(latitude);
CREATE INDEX requests_longitude ON requests(longitude);

CREATE TABLE requests_taxis (
    request_id INT NOT NULL REFERENCES requests(id),
    taxi_id INT NOT NULL REFERENCES taxis(id),
    PRIMARY KEY (request_id, taxi_id)
) ENGINE=InnoDB;

CREATE UNIQUE INDEX requests_taxis_reverse
    ON requests_taxis(taxi_id, request_id);

CREATE TABLE callcenters (
    country VARCHAR(3) NOT NULL,
    state VARCHAR(2) NOT NULL,
    title VARCHAR(255) NOT NULL,
    phone VARCHAR(32) NOT NULL,
    PRIMARY KEY (country, state)
) ENGINE=InnoDB;

CREATE TABLE eventlog (
    request_id INT REFERENCES requests(id),
    user_id INT REFERENCES users(id),
    logtime DATETIME NOT NULL,
    event_type VARCHAR(6),
    message TEXT,
    latitude DOUBLE,
    longitude DOUBLE
) ENGINE=InnoDB;

CREATE INDEX eventlog_request ON eventlog(request_id,logtime);
CREATE INDEX eventlog_taxi ON eventlog(taxi_id,logtime);

CREATE VIEW monthly_total AS
SELECT taxi_id,YEAR(logtime) AS year,MONTH(logtime) AS month,event_type,
    count(0) AS total
FROM eventlog GROUP BY 1,2,3,4;

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,

    pub_datetime DATETIME,
    title VARCHAR(255) NOT NULL,
    summary TEXT NOT NULL,
    content TEXT NOT NULL
) ENGINE=InnoDB;

CREATE INDEX posts_pub_datetime ON posts(pub_datetime);
