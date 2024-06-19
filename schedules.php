<?php
require_once ('config.php');
$date = isset($_GET['date']) ? date("Y-m-d", strtotime($_GET['date'])) : "";
$time = isset($_GET['time']) ? date("H:i", strtotime($_GET['time'])) : "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Maglev &mdash; Train Booking System</title>

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">

    <!-- =======================================================
  * Template Name: Techie
  * Template URL: https://bootstrapmade.com/techie-free-skin-bootstrap-3/
  * Updated: Mar 17 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

    <!-- ======= Header ======= -->
    <header id="header" class="fixed-top ">
        <div class="container d-flex align-items-center justify-content-between">
            <h1 class="logo"><a href="index.html">MAGLEV</a></h1>
            <!-- Uncomment below if you prefer to use an image logo -->
            <!-- <a href="index.html" class="logo"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->

            <nav id="navbar" class="navbar">
                <ul>
                    <li><a class="getstarted scrollto" href="#about">Admin Login</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav><!-- .navbar -->

        </div>
    </header><!-- End Header -->

    <!-- ======= Hero Section ======= -->
    <section id="hero" class="d-flex align-items-center justify-content-center">

        <div class="card">
            <h5 class="card-header">Train Schedules</h5>
            <div class="card-body">
                <h5 class="card-title">Find Train</h5>
                <fieldset>
                    <form action="" id="filter-schedule">
                        <div class="row align-items-end">
                            <div class="col-md-3 col-sm-4">
                                <label for="date" class="control-label">Desired Date</label>
                                <input type="date" name="date" id="date" class="form-control form-control-sm rounded-0"
                                    value="<?= $date ?>" required>
                            </div>
                            <div class="col-md-3 col-sm-4">
                                <label for="time" class="control-label">Desired Time</label>
                                <input type="time" name="time" id="time" class="form-control form-control-sm rounded-0"
                                    value="<?= $time ?>" required>
                            </div>
                            <div class="col-md-3 col-sm-4">
                                <button class="btn btn-flat btn-primary"><i class="fa fa-search"></i>
                                    Search
                                </button>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <table class="table table-hover table-striped table-bordered">
                        <colgroup>
                            <col width="15%">
                            <col width="15%">
                            <col width="20%">
                            <col width="20%">
                            <col width="20%">
                            <col width="10%">
                        </colgroup>
                        <thead>
                            <tr class="bg-gradient-primary text-light">
                                <th>Code</th>
                                <th>Schedule</th>
                                <th>Route</th>
                                <th>Train</th>
                                <th>Slot/Rate</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Schedule List Dynamic where clause
                            $swhere = "";
                            if (!empty($date) && !empty($time)) {
                                $swhere = " and ((`type` = 1 and time(time_schedule) BETWEEN '" . (date('H:i', strtotime($date . " " . $time . " -2 hours"))) . "' and '" . (date('H:i', strtotime($date . " " . $time . " +2 hours"))) . "' ) or (`type` = 2 and date(date_schedule) = '{$date}' and time(time_schedule) BETWEEN '" . (date('H:i', strtotime($date . " " . $time . " -2 hours"))) . "' and '" . (date('H:i', strtotime($date . " " . $time . " +2 hours"))) . "' ))";
                            }
                            $i = 1;
                            $reservations = $conn->query("SELECT * FROM `reservation_list` where schedule_id in (SELECT id FROM `schedule_list` where delete_flag = 0 {$swhere}) ");
                            while ($row = $reservations->fetch_assoc()) {
                                if (!isset($reserve[$row['schedule_id']][$row['seat_type']]))
                                    $reserve[$row['schedule_id']][$row['seat_type']] = 0;
                                $reserve[$row['schedule_id']][$row['seat_type']] += 1;
                            }
                            $trains = $conn->query("SELECT *,Concat(code,' - ',`name`) as train FROM `train_list` where id in (SELECT train_id FROM `schedule_list` where delete_flag = 0 {$swhere})");
                            $res = $trains->fetch_all(MYSQLI_ASSOC);
                            $train_fcf_arr = array_column($res, 'first_class_capacity', 'id');
                            $train_ef_arr = array_column($res, 'economy_capacity', 'id');
                            $train_arr = array_column($res, 'train', 'id');
                            $qry = $conn->query("SELECT * from `schedule_list` where delete_flag = 0 {$swhere} order by unix_timestamp(`date_created`) asc ");
                            while ($row = $qry->fetch_assoc()):
                                $fc_capacity = isset($train_fcf_arr[$row['train_id']]) ? $train_fcf_arr[$row['train_id']] : 0;
                                $e_capacity = isset($train_ef_arr[$row['train_id']]) ? $train_ef_arr[$row['train_id']] : 0;
                                $fc_reserve = isset($reserve[$row['id']][1]) ? $reserve[$row['id']][1] : 0;
                                $e_reserve = isset($reserve[$row['id']][2]) ? $reserve[$row['id']][2] : 0;
                                $fc_slot = $fc_capacity - $fc_reserve;
                                $e_slot = $e_capacity - $e_reserve;
                                ?>
                                <tr>
                                    <td class="text-center px-1"><?= $row['code'] ?></td>
                                    <td class="px-0">
                                        <?php if ($row['type'] == 1): ?>
                                            <div class="px-1 border-bottom"><span class="text-muted fa fa-calendar"></span>
                                                Everyday</div>
                                        <?php else: ?>
                                            <div class="px-1 border-bottom"><span class="text-muted fa fa-calendar-day"></span>
                                                <?= date("M d, Y", strtotime($row['date_schedule'])) ?></div>
                                        <?php endif; ?>
                                        <div class="px-1"><span class="text-muted fa fa-clock"></span>
                                            <?= date("h:i A", strtotime($row['time_schedule'])) ?></div>
                                    </td>
                                    <td class="px-0">
                                        <div class="px-1 border-bottom"><span class="text-muted">From:</span>
                                            <b><?= $row['route_from'] ?></b>
                                        </div>
                                        <div class="px-1"><span class="text-muted">To:</span>
                                            <b><?= $row['route_to'] ?></b>
                                        </div>
                                    </td>
                                    <td class="px-1">
                                        <?php echo isset($train_arr[$row['train_id']]) ? $train_arr[$row['train_id']] : "N/A" ?>
                                    </td>
                                    <td class="px-0">
                                        <div class="px-1 border-bottom"><span class="text-muted">First Class:</span>
                                            <span class="text-muted fa fa-user"></span>
                                            <b><?= $row['type'] == 1 ? "<i class='fa fa-question' title='Slot depends to the date you desire.'></i>" : number_format($fc_slot) ?></b>
                                            <span class="text-muted ml-2 fa fa-tag"></span>
                                            <b><?= rtrim(number_format($row['first_class_fare'], 2), '.') ?></b>
                                        </div>
                                        <div class="px-1"><span class="text-muted">Economy:</span> <span
                                                class="text-muted fa fa-user"></span>
                                            <b><?= $row['type'] == 1 ? "<i class='fa fa-question' title='Slot depends to the date you desire.'></i>" : number_format($e_slot) ?></b>
                                            <span class="text-muted ml-2 fa fa-tag"></span>
                                            <b><?= rtrim(number_format($row['economy_fare'], 2), '.') ?></b>
                                        </div>
                                    </td>
                                    <td class="px-1" align="center">
                                        <a href="./?page=reserve&sid=<?= $row['id'] ?>"
                                            class="btn btn-flat btn-primary btn-sm">Book <i
                                                class="fa fa-angle-right"></i></a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </fieldset>

            </div>
        </div>

    </section><!-- End Hero -->

    <!-- ======= Footer ======= -->
    <footer id="footer">
        <div class="container">

            <div class="copyright-wrap d-md-flex py-4">
                <div class="me-md-auto text-center text-md-start">
                    <div class="copyright">
                        &copy; Copyright <strong><span>Techie</span></strong>. All Rights Reserved
                    </div>
                    <div class="credits">
                        <!-- All the links in the footer should remain intact. -->
                        <!-- You can delete the links only if you purchased the pro version. -->
                        <!-- Licensing information: https://bootstrapmade.com/license/ -->
                        <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/techie-free-skin-bootstrap-3/ -->
                        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
                    </div>
                </div>
            </div>

        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>

</body>

</html>