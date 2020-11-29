$(function () {
    updateLock = setInterval(updateLocks, 1000);
    
    const MINTEMP = -40.0;
    const MAXTEMP = 80.0;

    // Set height
    $('.tempDiv').each(function() {
        let temp = parseFloat($(this).find(".temp > h4").text().slice(0, -2));
        let thisDiv = $(this);
        let scrollerHeight = (temp - MINTEMP) / (MAXTEMP - MINTEMP) * thisDiv.height();
        thisDiv.find(".scroller").height(scrollerHeight);
    });

    // --- Desktop Mouse ---
    $(".tempDiv").on("mousedown", function(evt) {
        $(".tempDiv").on("mousemove", tempSlider);
    });

    $(".tempDiv").on("mouseup", function(evt) {
        $(".tempDiv").off("mousemove", tempSlider);
        updateComponent();
    });

    $(".tempDiv").on("mouseleave", function(evt) {
        $(".tempDiv").off("mousemove", tempSlider);
    });

    // --- Phone Touches ---
    // Disable scrolling
    $(".tempDiv").on("touchstart", function() {
        // Get the current page scroll position
        scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

        // if any scroll is attempted, set this to the previous value
        window.onscroll = function() {
            window.scrollTo(scrollLeft, scrollTop);
        };
    });

    $(".tempDiv").on("touchmove", tempSliderTouch);

    $(".tempDiv").on("touchend", function(){
        window.onscroll = function() {}
        updateComponent();
    });

    $(".lockDiv").on("click", function() {
        let h2status = $(this).find(".card-top > h2");
        let lockstate;
        if("Locked" == h2status.text()) {
            lockstate = "Unlocked";
        }else {
            lockstate = "Locked";
        }
        // --------------------------------------------------------
        h2status.text(lockstate);
        if("Locked" == lockstate) {
            h2status.removeClass("green");
            h2status.addClass("red");
        }else {
            h2status.removeClass("red");
            h2status.addClass("green");
        }
        updateComponent();
    });

    $("h2[contenteditable]").on("keydown", function(e) {
        // trap the return key being pressed
        if (e.code === "Enter") {
            updateDeviceName($(this));
            $(this).blur();
            // prevent the default behaviour of return key pressed
            return false;
        }
    });

    function tempSlider(evt) {
        let thisDiv = $(this);
        let scrollerHeight = thisDiv.height() + thisDiv.offset().top - evt.pageY;
        let temperature = (scrollerHeight / thisDiv.height()  * (MAXTEMP - MINTEMP) + MINTEMP).toFixed(1);
        if(temperature < MINTEMP) {
            temperature = MINTEMP;
        }

        // Making sure is 1 decimal
        temperature = parseFloat(temperature).toFixed(1);

        thisDiv.children(".scroller").height(scrollerHeight);
        $(this).find(".temp > h4").text(temperature + "°C");
    }

    function tempSliderTouch(evt) {
        let thisDiv = $(this);
        let scrollerHeight = thisDiv.height() + thisDiv.offset().top - evt.changedTouches[0].pageY;
        let temperature = (scrollerHeight / thisDiv.height()  * (MAXTEMP - MINTEMP) + MINTEMP).toFixed(1);
        if(scrollerHeight > thisDiv.height()) {
            scrollerHeight = thisDiv.height();
        }
        if(temperature < MINTEMP) {
            temperature = MINTEMP;
        }else if(temperature > MAXTEMP) {
            temperature = MAXTEMP;
        }

        // Making sure is 1 decimal
        temperature = parseFloat(temperature).toFixed(1);

        thisDiv.children(".scroller").height(scrollerHeight);
        $(this).find(".temp > h4").text(temperature + "°C");
    }

    function updateDeviceName(element){
        let deviceID = element.attr('id');
        let deviceName = element.text();
        let token = $("#token").val();

        $.ajax({
            url: "/updateName",
            method: "POST",
            data: {
                cat: "updateDeviceName",
                deviceID: deviceID,
                deviceName: deviceName,
                token: token
            },
            success: function(data){
                addToast(data);
            }
        });
    }

    function updateComponent() {
        let deviceID = $("main > h2").eq(0).attr("id");
        let temp = $(".tempDiv > .temp > h4").text().slice(0, -2);
        temp = parseFloat(temp).toFixed(1);
        let lock = $(".lockDiv > div").eq(0).find("h2").text();
        let token = $("#token").val();

        $.ajax({
            url: "/updateName",
            method: "POST",
            data: {
                cat: "updateComponent",
                deviceID: deviceID,
                temp: temp,
                lock: lock,
                token: token
            },
            success: function(data){
                addToast(data);
            }
        });
    }

    async function addToast(success) {
        let toast = $("<div class=\"toasts\" role=\"alert\">\n" +
            "            <div class=\"toast_top\">\n" +
            "                <p class=\"toastText\">Success</p>\n" +
            "                <button type=\"button\" class=\"close\" aria-label=\"Close\">\n" +
            "                    <span aria-hidden=\"true\">&times;</span>\n" +
            "                </button>\n" +
            "            </div>\n" +
            "            <div class=\"toast_progress\"></div>\n" +
            "        </div>");

        if(success) {
            toast.addClass("toast_success");
            toast.find(".toastText").text("Success");
        }else {
            toast.addClass("toast_danger");
            toast.find(".toastText").text("Error! Please try again later.");
        }
        toast.find("div > button").on("click", function() {
           $(this).parent().parent().fadeOut("slow");
        });

        $("#toastDiv").append(toast);

        toast.fadeIn("slow");
        // Wait 5 secs before removing
        await sleep(4500);
        toast.fadeOut("slow");
    }

    function updateLocks() {
        let componentID = $(".lockDiv").attr("id");
        let token = $("#token").val();
        
        $.ajax({
            url: "/updateName",
            method: "POST",
            data: {
                cat: "getLockStatus",
                componentID: componentID,
                token: token
            },
            success: function(data){
                let h2status = $(".lockDiv").find(".card-top > h2");
                let lockstate = data;
                // --------------------------------------------------------
                h2status.text(lockstate);
                if("Locked" == lockstate) {
                    h2status.removeClass("green");
                    h2status.addClass("red");
                }else {
                    h2status.removeClass("red");
                    h2status.addClass("green");
                }
            }
        });
    }
    
    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
});

