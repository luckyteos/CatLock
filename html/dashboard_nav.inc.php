<?php
    $navProcessing = new DatabaseProcessing();
    $navDevices = $navProcessing->getDeviceStatus("On");

    // CSRF Token
    session_start();
    if (empty($_SESSION['token'])) {
        $length = 32;
        $_SESSION['token'] = bin2hex(random_bytes(32));
    }
?>

<input type="hidden" id="token" value="<?php echo $_SESSION['token'];?>">
<nav class="navbar navbar-dark sticky-top flex-nowrap shadow">
    <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="#">SELocker</a>
    <button class="navbar-toggler d-md-none collapsed" type="button" id="navButton" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
</nav>

<div class="container-fluid flex-grow-1 h-100">
    <div class="row h-100">
        <nav id="sidebarMenu" class="col-md-3 d-md-block sidebar collapse position-sticky">
            <div class="sidebar-sticky pt-3">
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-2 mb-1">
                    <span>Dashboard</span>
                </h6>

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a id="dashboard" class="nav-link" href="/Dashboard">
                            <i class="fas fa-home"></i>
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a id="statistics" class="nav-link" href="/Statistics">
                            <i class="fas fa-chart-area"></i>
                            Statistics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a id="connections" class="nav-link" href="/Connections">
                            <i class="fas fa-project-diagram"></i>
                            Connections
                        </a>
                    </li>
                    <li class="nav-item">
                        <a id="alerts" class="nav-link" href="/Alerts">
                            <i class="fas fa-exclamation-triangle"></i>
                            Alerts
                            <?php
                                // Get Unread
                                if($num = $navProcessing->getNumAlert()) {
                                    echo "<span class=\"badge badge-pill badge-danger ml-1\">" . $num . "</span>";
                                }
                            ?>
                        </a>
                    </li>
                </ul>

                <h6 class="sidebar-heading d-flex align-items-center px-3 mt-4 mb-1">
                    <i class="fas fa-cog mr-2"></i>
                    <span>Settings</span>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <!-- Generate Settings -->
                        <?php
                            foreach($navDevices as $navDevice) {
                                $deviceID = $navDevice->getDeviceID();
                                $deviceName = $navDevice->getDeviceName();

                                echo "<a class=\"nav-link\" href=\"/Settings/" . htmlspecialchars($deviceID) . "\">" . htmlspecialchars($deviceName) . "</a>";
                            }
                        ?>
                    </li>
                </ul>
            </div>
        </nav>
