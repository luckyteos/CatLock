<?php
    require_once("DatabaseProcessing.php");
?>

<!DOCTYPE html>
<html lang=en-SG class="h-100">

<head>
    <title>Alerts</title>
    <meta name="description" content="IOT Dashboard">
    <meta name="keywords" content="dashboard,mainpage">
    <?php include "header.php"; ?>
    <!-- Stylesheet -->
    <link rel=stylesheet href="css/alert.css">
</head>

<body class="d-flex flex-column h-100">
    <!-- Navbar + Sidebar -->
    <?php include 'dashboard_nav.inc.php'; ?>

            <main class="col-md-9 p-3">
                <div id="maindash" class="d-flex flex-wrap row p-3">
                    <div class="card">
                        <div class="card-header">
                            <h2>Alert</h2>
                        </div>
                        <div class="card-body">
<!--                            <div class="alerts unread">-->
<!--                                <i class="fas fa-circle fa-xs"></i>-->
<!--                                <div class="messagebox">-->
<!--                                    <p>Device4.0 Temperature has reached threshold!</p>-->
<!--                                    <p>2020-11-13 23:34:41</p>-->
<!--                                </div>-->
<!--                            </div>-->
                            <?php
                                $processing = new DatabaseProcessing();
                                $alerts = $processing->getAlerts();

                                // Marking as read
                                //$processing->updateAllAlerts();

                                foreach($alerts as $alert) {
                                    $alertID = $alert->getAlertID();
                                    $time = $alert->getTime();
                                    $readOrNot = $alert->getReadOrNot();
                                    $alertMessage = $alert->getAlertMessage();
                                    $deviceName = $alert->getDeviceName();

                                    if("Read" == $readOrNot) {
                                        echo "<div class=\"alerts\" id=\"" . htmlspecialchars($alertID) . "\">";
                                    }else {
                                        echo "<div class=\"alerts unread\" id=\"" . htmlspecialchars($alertID) . "\">";
                                    }


                                    echo "<i class=\"fas fa-circle fa-xs\"></i>";

                                    echo "<div class=\"messagebox\">
                                            <p>" . htmlspecialchars($deviceName) . " " . htmlspecialchars($alertMessage) . "</p>";

                                    $yesterdayStart = new DateTime(date('d.m.Y', strtotime("-1 days")), new DateTimeZone("Asia/Singapore"));
                                    $todayStart = new DateTime(date("Y-m-d"), new DateTimeZone("Asia/Singapore"));
                                    $compareDate = new DateTime($time, new DateTimeZone("Asia/Singapore"));

                                    $now = new DateTime();
                                    $now->setTimezone(new DateTimeZone("Asia/Singapore"));

                                    if($todayStart <= $compareDate) {
                                        // TODAY
                                        $seconds = $now->getTimestamp() - $compareDate->getTimestamp();
                                        if($seconds < 60) {
                                            echo "<p class=\"secTime\">" . $seconds . " seconds ago</p>";
                                        }elseif($seconds < 60 * 60) {
                                            echo "<p class=\"minTime\">" . FLOOR($seconds / 60) . " minutes ago</p>";
                                        }elseif($seconds < 24 * 60 * 60) {
                                            echo "<p class=\"hourTime\">" . FLOOR($seconds / (60 * 60)) . " hours ago</p>";
                                        }
                                    }elseif($yesterdayStart <= $compareDate) {
                                        // YESTERDAY
                                        echo "<p>Yesterday, " . $compareDate->format("h:i A") . "</p>";
                                    }else {
                                        echo "<p>" . $compareDate->format("F d, h:i A") . "</p>";
                                    }

                                    echo "</div>
                                    </div>";
                                }
                            ?>
                        </div>
                    </div>
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
    <script type="text/javascript" src="/js/navbar.js" defer></script>
    <script type="text/javascript" src="/js/alerts.js" defer></script>
</body>

</html>
