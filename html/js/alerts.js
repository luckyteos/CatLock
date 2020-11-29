$(function () {
    $(".alerts").on("click", function() {
        let ele = $(this);
        if(ele.hasClass("unread")) {
            $.ajax({
                url: "/updateName",
                method: "POST",
                data: {
                    cat: "setUnread",
                    alertID: ele.attr("id"),
                    readOrNot: "Read",
                    token: $("#token").val()
                },
                success: function(data){
                    if(data) {
                        ele.removeClass("unread");
                    }
                }
            });
        }else {
            $.ajax({
                url: "/updateName",
                method: "POST",
                data: {
                    cat: "setUnread",
                    alertID: ele.attr("id"),
                    readOrNot: "Unread",
                    token: $("#token").val()
                },
                success: function(data){
                    if(data) {
                        ele.addClass("unread");
                    }
                }
            });
        }
    });

    secondsTimer = setInterval(updateSecs, 1000);
    minutesTimer = setInterval(updateMins, 1000);
    hoursTimer = setInterval(updateHours, 1000 * 60);

    function updateSecs() {
        $(".secTime").each(function(i, ele) {
            let alertID = $(this).parent().parent().attr("id");
            let token = $("#token").val();

            $.ajax({
                url: "/updateName",
                method: "POST",
                data: {
                    cat: "getUpdatedTimeLog",
                    alertID: alertID,
                    token: token
                },
                success: function(data){
                    let obj = JSON.parse(data);
                    if($(ele).text() !== obj.data) {
                        $(ele).text(obj.data);
                    }

                    if(obj.update == 2) {
                        $(ele).removeClass("secTime");
                        $(ele).addClass("minTime");
                    }
                }
            });
        });
    }

    function updateMins() {
        $(".minTime").each(function(i, ele) {
            let alertID = $(this).parent().parent().attr("id");
            let token = $("#token").val();

            $.ajax({
                url: "/updateName",
                method: "POST",
                data: {
                    cat: "getUpdatedTimeLog",
                    alertID: alertID,
                    token: token
                },
                success: function(data){
                    let obj = JSON.parse(data);

                    if($(ele).text() !== obj.data) {
                        $(ele).text(obj.data);
                    }

                    if(obj.update == 3) {
                        $(ele).removeClass("minTime");
                        $(ele).addClass("hourTime");
                    }
                }
            });
        });
    }

    function updateHours() {
        $(".hourTime").each(function(i, ele) {
            let alertID = $(this).parent().parent().attr("id");
            let token = $("#token").val();

            $.ajax({
                url: "/updateName",
                method: "POST",
                data: {
                    cat: "getUpdatedTimeLog",
                    alertID: alertID,
                    token: token
                },
                success: function(data){
                    let obj = JSON.parse(data);
                    if($(ele).text() !== obj.data) {
                        $(ele).text(obj.data);
                    }

                    if(obj.update != 3) {
                        $(ele).removeClass("hourTime");
                    }
                }
            });
        });
    }

});

