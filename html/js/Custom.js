$(function () {
    $("#IFButton").on("click", addNewRule);
    $("#ANDButton").on("click", addAndOption);
    $("#ORButton").on("click", addOrOption);

    $(".inputOptions").on("click", setActiveOption);

    $(".ruleCard").on("dblclick", function() {
        $(this).remove();
    });
});

function addNewRule() {
    let ruleDiv = $("<div class=\"d-flex card ruleCard p-3\">\n" +
        "                            <input type=\"text\" class=\"form-control\" placeholder=\"Rule name\"><hr>\n" +
        "                            <h4><strong>IF</strong></h4>\n" +
        "                            <div class=\"conditionDiv\">\n" +
        "                                <div class=\"input-group mb-3\">\n" +
        "                                    <div class=\"input-group-prepend\">\n" +
        "                                        <p class=\"input-group-text\">Options</p>\n" +
        "                                    </div>\n" +
        "                                    <input type=\"text\" class=\"form-control inputOptions\" readonly>\n" +
        "                                </div>\n" +
        "                            </div>\n" +
        "                            <h4><strong>THEN</strong></h4>\n" +
        "                            <div class=\"conditionResultDiv\">\n" +
        "                                <div class=\"input-group mb-3\">\n" +
        "                                    <div class=\"input-group-prepend\">\n" +
        "                                        <p class=\"input-group-text\">Options</p>\n" +
        "                                    </div>\n" +
        "                                    <input type=\"text\" class=\"form-control inputOptions\" readonly>\n" +
        "                                </div>\n" +
        "                            </div>\n" +
        "                            <hr>\n" +
        "                        </div>");

    $("#inputDiv").append(ruleDiv);
    ruleDiv.on("dblclick", function() {
        $(this).remove();
    });
    $(".inputOptions").on("click", setActiveOption);
}

function addAndOption(cond) {
    let andElement = $("<p class=\"removeDbl\">AND</p>" +
        "<div class=\"input-group mb-3\">\n" +
        "    <div class=\"input-group-prepend\">\n" +
        "        <p class=\"input-group-text\"></p>\n" +
        "    </div>\n" +
        "    <input type=\"text\" class=\"form-control inputOptions\" readonly>\n" +
        "</div>")
    if(activeOption) {
        activeOption.after(andElement);
        $(".removeDbl").on("click", removeANDOR);
    }
}

function addOrOption(cond) {
    if(!activeOption.parent().hasClass("conditionResultDiv")) {
        let orElement = $("<p class=\"removeDbl\">OR</p>" +
            "<div class=\"input-group mb-3\">\n" +
            "    <div class=\"input-group-prepend\">\n" +
            "        <p class=\"input-group-text\"></p>\n" +
            "    </div>\n" +
            "    <input type=\"text\" class=\"form-control inputOptions\" readonly>\n" +
            "</div>")
        if(activeOption) {
            activeOption.after(orElement);
            $(".removeDbl").on("click", removeANDOR);
        }
    }
}

let activeOption;
function setActiveOption() {
    activeOption = $(this).parent();
    $('#exampleModal').modal('show');
}

function removeANDOR() {
    $(this).next().remove();
    $(this).remove();
}