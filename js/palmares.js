
$(document).on("pagecreate", "#home", function() {
    function refresh() {
        var res = /([0-9]{4})-([0-9]{2})/.exec($("#yearMonth").val());
        if (!res) {
            alert("Le mois doit être au format AAAA-MM");
            $("#yearMonth").focus();
        } else {
            $.ajax({
                url: "ws/palmares.php",
                data: {year: res[1], month: res[2]},
                dataType: 'json',
                success: function (data) {
                    var $tbody = $("#resultTable tbody");
                    $tbody.empty();
                    if (data.length === 0) {
                        var $tr = $("<tr/>");
                        var $td = $("<td/>");
                        $td.attr("colspan", 5);
                        $td.text("Pas de résultat");
                        $td.appendTo($tr);
                        $tr.appendTo($tbody);
                    } else {
                        $.each(data, function(index, row) {
                            var $tr = $("<tr/>");
                            var $td = $("<td/>");
                            $td.text(row.tot);
                            $td.appendTo($tr);
                            $td = $("<td/>");
                            $td.text(row.pln);
                            $td.appendTo($tr);
                            $td = $("<td/>");
                            $td.text(row.lnm);
                            $td.appendTo($tr);
                            $td = $("<td/>");
                            $td.text(row.fnm);
                            $td.appendTo($tr);
                            $td = $("<td/>");
                            $td.text(row.mal);
                            $td.appendTo($tr);
                            $tr.appendTo($tbody);
                        });
                    }
                },
                error: function() {
                    alert("La requête au serveur a échoué");
                }
            });
            console.log("Settings submitted");
        }
    }

    $("#monthForm").on("submit", function(event) {
        event.preventDefault();
        refresh();
        return false;
    });

    $("#yearMonth").on("change", function(event) {
        refresh();
    });

    $(this).on("show", function() {
        refresh();
    });
});
