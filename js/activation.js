
$(document).on("pagecreate", "#activation", function() {
    var $page = $(this);

    $page.on("show", function() {
        $.mobile.loading('show', {
            text: "Activation en cours...",
            textVisible: true
        });
        $.ajax({
            url: "ws/activation.php",
            data: {usn: $_GET, lng: gotaxi.longitude},
            dataType: 'json'
        }).done(function (data) {
            gotaxi.busy = false;
            gotaxi.updateRequests(data.ts, data.dt);
            gotaxi.requestsTimestamp = data.ts;
            gotaxi.doRefresh();
        }).fail(function() {
            gotaxi.busy = false;
        });
    });
});
