<?php
    require_once("DatabaseProcessing.php");

    $processing = new DatabaseProcessing();
    $devices = $processing->getEverythingStatistics();
    $javascriptForCanvas = array();
?>

<!DOCTYPE html>
<html lang=en-SG class="h-100">

<head>
    <title>Statistics</title>
    <meta name="description" content="IOT Dashboard">
    <meta name="keywords" content="dashboard,mainpage">
    <?php include "header.php"; ?>
    <!-- Stylesheet -->
    <link rel=stylesheet href="css/dashboard.css">
</head>

<body class="d-flex flex-column h-100">
    <?php include 'dashboard_nav.inc.php'; ?>

            <main class="col-md-9 p-3">
                <h2>Statistics</h2>
                <div id="maindash" class="d-flex flex-wrap row mt-2 p-3">
                    <!-- Generate Components -->
                    <?php
                        $index = 0;
                        foreach($devices as $device) {
                            $deviceName = $device->getDeviceName();
                            $components = $device->getComponents();
                            foreach($components as $component) {
                                $componentID = $component->getComponentID();
                                $componentName = $component->getComponentName();
                                $category = $component->getCategory();
                                $threshold = $component->getStoredValue();
                                $logValue = $component->getLogs();

                                if($logValue == null) {
                                    continue;
                                }

                                $canvasID = "chart" . $index;
                                if ($category == "Lock") {
                                    echo "<div id=\"" . htmlspecialchars($componentID) . "\" class=\"card m-3 rounded flex-grow-1\">
                                            <canvas id=\"$canvasID\" width=\"200\" height=\"200\"  class=\"p-3\"></canvas>
                                            <div class=\"card-body\">
                                                <h5 class=\"card-title\">" . htmlspecialchars($componentName) . " (" . htmlspecialchars($deviceName) . ")</h5>
                                                <p class=\"card-text\">Lock logic of " . htmlspecialchars($componentName) . "</p>
                                            </div>
                                        </div>";

                                    // Generate data
                                    $javascript = "[";
                                    foreach($logValue as $log) {
                                        $x = $log->getTime();
                                        $y = $log->getValue();
                                        $javascript .= "{x: new Date(\"$x\"),";
                                        if($y == "Unlocked") {
                                            $javascript .= "y: 1},";
                                        }else {
                                            $javascript .= "y: 0},";
                                        }
                                    }
                                    $javascript = rtrim($javascript, ", ");
                                    $javascript .= "]";

                                    $array = array("ComponentName"=>$componentName, "ID"=>$canvasID, "Cat"=>"Lock", "Data"=>$javascript);
                                    array_push($javascriptForCanvas, $array);
                                } else if ($category == "Sensor") {
                                    echo "<div id=\"" . $componentID . "\" class=\"card m-3 rounded flex-grow-1\">
                                            <canvas id=\"$canvasID\" width=\"200\" height=\"200\"  class=\"p-3\"></canvas>
                                            <div class=\"card-body\">
                                                <h5 class=\"card-title\">" . htmlspecialchars($componentName) . " (" . htmlspecialchars($deviceName) . ")</h5>
                                                <p class=\"card-text\">Current temperature of " . htmlspecialchars($componentName) . "</p>
                                            </div>
                                        </div>";

                                    // Generate data
                                    $javascript = "[";
                                    foreach($logValue as $log) {
                                        $x = $log->getTime();
                                        $y = $log->getValue();
                                        $javascript .= "{x: new Date(\"$x\"),";
                                        $javascript .= "y: $y},";
                                    }
                                    $javascript = rtrim($javascript, ", ");
                                    $javascript .= "]";

                                    $array = array("ComponentName"=>$componentName, "ID"=>$canvasID, "Cat"=>"Sensor", "Data"=>$javascript);
                                    array_push($javascriptForCanvas, $array);
                                }
                                $index += 1;
                            }
                        }
                    ?>
                </div>
            </main>
        </div>
    </div>
    
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
    <script type="text/javascript" src="js/navbar.js" defer></script>
    <?php
        echo "<script>";

        foreach($javascriptForCanvas as $canvas) {
            $componentName = $canvas["ComponentName"];
            $canvasID = $canvas["ID"];
            $category = $canvas["Cat"];
            $javascript = $canvas["Data"];

            if($category == "Lock") {
                echo 'var ' . $canvasID . '= document.getElementById("' . $canvasID . '");
                var my' . $canvasID . ' = new Chart(' . $canvasID . ', {
                    type: "line",
                    data: {
                        datasets: [{
                            label: "Logic of ' . htmlspecialchars($componentName) . '",
                            data: ' . $javascript . ',
                            fill: "origin",
                            borderColor: "rgb(0,122,204)",
                            borderWidth: 3,
                            backgroundColor: "rgb(0,122,204)",
                            pointStyle: "dash",
                            steppedLine: true
                        }]
                    },
                    options: {
                        responsive: true,
                        title: {
                            display: true,
                            text: "Hourly Logic of Door?"
                        },
                        scales: {
                            xAxes: [{
                                type: "time",
                                display: true,
                                displayFormats: {
                                    quarter: "hA"
                                },
                                distribution: "series",
                                time: {
                                    unit: "minute",
                                    stepSize: 10
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: "Time"
                                },
                                ticks: {
                                    major: {
                                        fontStyle: "bold",
                                        fontColor: "#FF0000"
                                    }
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: "Logic"
                                },
                                ticks: {
                                    min: 0,
                                    max: 1,
                                    stepSize: 1
                                }
                            }]
                        }
                    }
                });';
            }else if($category == "Sensor") {
                echo 'var ' . $canvasID . ' = document.getElementById("' . $canvasID . '");
                var my' . $canvasID . ' = new Chart(' . $canvasID . ', {
                    type: "line",
                    data: {
                        datasets: [{
                            label: "Temperature of ' . htmlspecialchars($componentName) . '",
                            fill: false,
                            data: ' . $javascript . ',
                            borderColor: "rgb(0,122,204)",
                            borderWidth: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        title: {
                            display: true,
                            text: "Hourly Temperature"
                        },
                        scales: {
                            xAxes: [{
                                type: "time",
                                display: true,
                                displayFormats: {
                                    quarter: "hA"
                                },
                                distribution: "series",
                                time: {
                                    unit: "minute",
                                    stepSize: 2
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: "Time"
                                },
                                ticks: {
                                    major: {
                                        fontStyle: "bold",
                                        fontColor: "#FF0000"
                                    }
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: "Temperature (°C)"
                                },
                                ticks: {
                                    min: -40,
                                    max: 80,
                                }
                            }]
                        }
                    }
                });';
            }
        }

        echo "</script>";
    ?>
</body>

</html>
