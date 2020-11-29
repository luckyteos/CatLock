$(function () {
    // Updating value every 5 secs
    updateAlertTimer = setInterval(updateAlerts, 1000);

    let title = document.title;
    if ("Dashboard" == title) {
        $("#dashboard").addClass("active");
    } else if ("Statistics" == title) {
        $("#statistics").addClass("active");
    } else if ("Connections" == title) {
        $("#connections").addClass("active");
    }

    function updateAlerts() {
        let token = $("#token").val();

        $.ajax({
            url: "/updateName",
            method: "POST",
            data: {
                cat: "getAlerts",
                token: token
            },
            success: function(data){
                if(data > 0 && !$("#alerts > .badge").length) {
                    let alertSpan = $("<span class=\"badge badge-pill badge-danger ml-1\"></span>");
                    alertSpan.text(data);
                    $("#alerts").append(alertSpan);
                }else if(data <= 0 && $("#alerts > .badge").length) {
                    $("#alerts > .badge").remove();
                }else {
                    $("#alerts > .badge").text(data);
                }
            }
        });
    }
});
