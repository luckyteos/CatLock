$(function () {
    // Updating value every 5 secs
    updateValuesTimer = setInterval(updateValues, 1000);

    function updateValues() {
        let token = $("#token").val();

        $(".card-top").each(function(index, ele) {
            let componentID = $(this).attr("id");

            $.ajax({
                url: "/updateName",
                method: "POST",
                data: {
                    cat: "getComponentLog",
                    componentID: componentID,
                    token: token
                },
                success: function(data){
                    let h2text = $(ele).find("h2");
                    // --------------------------------------------------------
                    h2text.text(data);
                    if("Locked" == data) {
                        h2text.removeClass("green");
                        h2text.addClass("red");
                    }else if("Unlocked" == data){
                        h2text.removeClass("red");
                        h2text.addClass("green");
                    }else {
                        h2text.text(data + "°C");
                        let threshold = $(ele).find("input").val();
                        if(parseFloat(data) >= parseFloat(threshold)) {
                            h2text.removeClass("blue");
                            h2text.addClass("red");
                        }else {
                            h2text.removeClass("red");
                            h2text.addClass("blue");
                        }
                    }
                }
            });
        });
    }
});

