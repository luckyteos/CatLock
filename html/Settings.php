<?php
    require_once("DatabaseProcessing.php");
?>

<!DOCTYPE html>
<html lang=en-SG class="h-100">

<head>
    <title>Settings</title>
    <meta name="description" content="IOT Dashboard">
    <meta name="keywords" content="dashboard,mainpage">
    <?php include "header.php"; ?>
    <!-- Stylesheet -->
    <link rel=stylesheet href="/css/setting.css">
</head>

<body class="d-flex flex-column h-100">

    <?php include 'dashboard_nav.inc.php'; ?>

            <main class="col-md-9 p-3 h-100">
                <?php
                    if(isset($_GET["deviceID"])) {
                        $processing = new DatabaseProcessing();
                        if(!($device = $processing->getDevice($_GET["deviceID"]))) {
                            echo "<h2>Invalid Device</h2>";
                            exit();
                        }

                        // Get Component
                        $deviceWith = $processing->getAllComponents([$device]);
                        $deviceWith = array_pop($deviceWith);
                        $components = $deviceWith->getComponents();

                        echo "<h2 id=\"" . $_GET["deviceID"] . "\" contenteditable=\"true\" spellcheck=\"false\">" . htmlspecialchars($device->getDeviceName()) . "</h2>
                            <div id=\"maindash\" class=\"d-flex flex-wrap row mt-2 p-3\">";

                        // Display components settings
                        foreach($components as $component) {
                            $componentID = $component->getComponentID();
                            $componentName = $component->getComponentName();
                            $componentCategory = $component->getCategory();
                            $componentValue = $component->getStoredValue();

                            if($componentCategory == "Lock") {
                                echo "<!-- Locks -->
                                <div class=\"card m-3 rounded d-flex flex-grow-1 lockDiv\" id=\"" . htmlspecialchars($componentID) . "\">
                                    <div class=\"card-top rounded-top d-flex justify-content-center flex-grow-1\">
                                        ";
                                if($componentValue == "Locked") {
                                    echo "<h2 class=\"m-0 p-2 red\">" . htmlspecialchars($componentValue) . "</h2>";
                                }elseif("Unlocked" == $componentValue) {
                                    echo "<h2 class=\"m-0 p-2 green\">" . htmlspecialchars($componentValue) . "</h2>";
                                }

                                echo "</div>
                                    <div class=\"card-body rounded-bottom\">
                                        <h5 class=\"card-title\">Lock Status</h5>
                                        <p class=\"card-text\">" . htmlspecialchars($componentName) . "</p>
                                    </div>
                                </div>";
                            }else if($componentCategory == "Sensor") {
                                echo "<!-- Temperature -->
                                <div class=\"card m-3 rounded d-flex flex-grow-1 tempDiv\" id=\"" . htmlspecialchars($componentID) . "\" draggable=\"false\">
                                    <div class=\"scroller rounded\" height=\"\" draggable=\"false\"></div>
                                    <div class=\"temp\" draggable=\"false\">
                                        <h4 draggable=\"false\">" . htmlspecialchars($componentValue) . "°C</h4>
                                    </div>
                                    <div class=\"card-body rounded-bottom\" draggable=\"false\">
                                        <h5 class=\"card-title\" draggable=\"false\">Temperature Threshold</h5>
                                        <p class=\"card-text\" draggable=\"false\">" . htmlspecialchars($componentName) . "</p>
                                    </div>
                                </div>";
                            }
                        }

                    }else {
                        echo "<h2>Invalid Device</h2>";
                        exit();
                    }
                ?>

                </div>
            </main>
        </div>
    </div>

    <div id="toastDiv"></div>

    <!-- JQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
    <!-- JQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    <!-- Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous"></script>
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" integrity="sha512-d9xgZrVZpmmQlfonhQUvTR7lMPtO7NkZMkA0ABN3PHCbKA5nqylQ/yWlFAyY6hYgdF1Qh6nYiuADWwKB4C2WSw==" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
    <!-- MyScript -->
    <script type="text/javascript" src="/js/navbar.js" defer></script>
    <script type="text/javascript" src="/js/setting.js" defer></script>
</body>

</html>