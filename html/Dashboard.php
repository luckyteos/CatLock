<?php
    require_once("DatabaseProcessing.php");

    $processing = new DatabaseProcessing();
    $devices = $processing->getEverythingDashboard();
?>

<!DOCTYPE html>
<html lang=en-SG class="h-100">

<head>
    <title>Dashboard</title>
    <meta name="description" content="IOT Dashboard">
    <meta name="keywords" content="dashboard,mainpage">
    <?php include "header.php"; ?>
    <!-- Stylesheet -->
    <link rel=stylesheet href="css/dashboard.css">
</head>

<body class="d-flex flex-column h-100">
    <!-- Navbar + Sidebar -->
    <?php include 'dashboard_nav.inc.php'; ?>

            <main class="col-md-9 p-3">
                <h2>Dashboard</h2>
                <div id="maindash" class="d-flex flex-wrap row mt-2 p-3">

    <!-- Generate Components -->
    <?php
        foreach($devices as $device) {
            $deviceName = $device->getDeviceName();
            $components = $device->getComponents();
            foreach($components as $component) {
                $componentID = $component->getComponentID();
                $componentName = $component->getComponentName();
                $category = $component->getCategory();
                $threshold = $component->getStoredValue();
                $logValue = $component->getLogs()->getValue();

                if ($category == "Lock") {
                    echo '<!-- Locks -->
                        <div class="card m-3 rounded d-flex flex-grow-1" style="width: 18rem;">
                            <div class="card-top rounded-top d-flex justify-content-center flex-grow-1" id="' . htmlspecialchars($componentID) . '">';

                    if ($logValue == "Locked") {
                        echo '<h2 class="m-0 p-2 red">' . htmlspecialchars($logValue) . '</h2>';
                    } else {
                        echo '<h2 class="m-0 p-2 green">' . htmlspecialchars($logValue) . '</h2>';
                    }

                    echo '  </div>
                            <div class="card-body rounded-bottom">
                                <h3 class="card-title">Lock Status</h3>
                                <p class="card-text">' . htmlspecialchars($componentName) . ' (' . htmlspecialchars($deviceName) . ')</p>
                            </div>
                        </div>';
                } elseif ($category == "Sensor") {
                    echo '<!-- Temperature -->
                        <div class="card m-3 rounded d-flex flex-grow-1" style="width: 18rem;">
                            <div class="card-top rounded-top d-flex justify-content-center flex-grow-1" id="' . htmlspecialchars($componentID) . '">
                            <input type="hidden" value="' . htmlspecialchars($threshold) . '">';

                    if ($logValue >= $threshold) {
                        echo '<h2 class="m-0 p-2 red">' . htmlspecialchars($logValue) . '°C</h2>';
                    } else {
                        echo '<h2 class="m-0 p-2 blue">' . htmlspecialchars($logValue) . '°C</h2>';
                    }

                    echo '</div>
                            <div class="card-body rounded-bottom">
                                <h3 class="card-title">Temperature</h4>
                                <p class="card-text">' . htmlspecialchars($componentName) . ' (' . htmlspecialchars($deviceName) . ')</p>
                            </div>
                        </div>';
                }
            }
        }
    ?>
                </div>
            </main>
        </div>
    </div>

    <!-- JQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" integrity="sha512-d9xgZrVZpmmQlfonhQUvTR7lMPtO7NkZMkA0ABN3PHCbKA5nqylQ/yWlFAyY6hYgdF1Qh6nYiuADWwKB4C2WSw==" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
    <!-- MyScript -->
    <script src="/js/navbar.js" defer></script>
    <script src="/js/dashboard.js" defer></script>
</body>

</html>