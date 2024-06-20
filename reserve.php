<?php
require_once ('config.php');
if(isset($_GET['sid'])){
    $trains = $conn->query("SELECT *,Concat(code,' - ',`name`) as train FROM `train_list` where id in (SELECT train_id FROM `schedule_list` where delete_flag = 0 and id='{$_GET['sid']}')");
    $res = $trains->fetch_all(MYSQLI_ASSOC);
    $train_fcf_arr = array_column($res,'first_class_capacity','id');
    $train_ef_arr = array_column($res,'economy_capacity','id');
    $train_arr = array_column($res,'train','id');
    $qry = $conn->query("SELECT * from `schedule_list` where delete_flag = 0 and id='{$_GET['sid']}'");
    if($qry->num_rows > 0){
        $res = $qry->fetch_array();
        foreach($res as $k => $v){
            if(!is_numeric($k))
                $$k = $v;
        }
    }else{
    echo '<script> alert("Unkown Schedule ID.");location.replace("./?page=schedules") </script>';
    }
} else{
    echo '<script> alert("Schedule ID is required to view this page.");location.replace("./?page=schedules") </script>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Maglev &mdash; Train Booking System</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

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
  <section id="hero" class="d-flex align-items-center">

  <div class="card rounded-0 card-outline card-primary shadow">
        <div class="card-header rounded-0">
            <h5 class="card-title">Reservation Form</h5>
        </div>
        <div class="card-body rounded-0">
            <div class="callout border-primary rounded-0">
                <div class="row">
                    <div class="col-md-4 col-sm-6">
                        <dl>
                            <dt class="text-muted">Schedule Code:</dt>
                            <dd class="pl-3"><b><?= isset($code) ? $code : 'N/A' ?></b></dd>
                            <dt class="text-muted">Schedule:</dt>
                            <dd class="pl-3"><b><?= isset($date_schedule) && !is_null($date_schedule) ? date("M d, Y", strtotime($date_schedule)) : "Everday" ?> <?= isset($time_schedule) ? date("h:i A", strtotime($time_schedule)) : "--:-- --" ?></b></dd>
                        </dl>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <dl>
                            <dt class="text-muted">From:</dt>
                            <dd class="pl-3"><b><?= isset($route_from) ? $route_from : "N/A" ?></b></dd>
                            <dt class="text-muted">To:</dt>
                            <dd class="pl-3"><b><?= isset($route_to) ? $route_to : "N/A" ?></b></dd>
                        </dl>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <dl>
                            <dt class="text-muted">First Class Fare:</dt>
                            <dd class="pl-3"><b><?= isset($first_class_fare) ? $first_class_fare : '--.--' ?></b></dd>
                            <dt class="text-muted">Economy Fare:</dt>
                            <dd class="pl-3"><b><?= isset($economy_fare) ? $economy_fare : "--.--" ?></b></dd>
                        </dl>
                    </div>
                </div>
            </div>
            <hr>
            <form action="" id="reserve-form">
                <input type="hidden" name="schedule_id" value=<?= isset($id) ? $id : "" ?>>
                <input type="hidden" name="time" value=<?= isset($time_schedule) ? $time_schedule : "" ?>>
                <div class="form-group col-md-4 col-sm-6 <?= isset($date_schedule) && !is_null($date_schedule) ? 'd-none' : '' ?>">
                    <label for="date" class="form-group">Schedule Date</label>
                    <input class="form-control form-control-sm rounded-0" type="date" name="date" id="date" required value="<?= isset($date_schedule) && !is_null($date_schedule) ? $date_schedule : '' ?>" min="<?= date("Y-m-d") ?>">
                </div>
                <div class="row">
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <select class="form-control form-control-sm form-control-border" name="seat_type" required>
                                <option value="" disabled selected>Select here</option>
                                <option value="1">First Class</option>
                                <option value="2">Economy</option>
                            </select>
                            <small class="text-muted mx-2">Seat Type</small>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <input type="text" class="form-control form-control-sm form-control-border text-right" name="fare_amount" readonly>
                            <small class="text-muted mx-2">Fare Amount</small>
                        </div>
                    </div>
                </div> 
                <!-- List Group -->
                <div class="list-group" id="reserve-field">
                    <div class="list-group-item border reserve-item">
                        <div class="row">
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-sm form-control-border" name="firstname[]" required>
                                    <small class="text-muted mx-2">First Name</small>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-sm form-control-border" name="middlename[]" placeholder="(optional)">
                                    <small class="text-muted mx-2">Middle Name</small>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-sm form-control-border" name="lastname[]" required>
                                    <small class="text-muted mx-2">Last Name</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group text-right">
                                    <button class="btn btn-danger btn-sm btn-flat btn-remove" type="button"><i class="fa fa-trash"></i> Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-between my-2">
                    <div class="form-group">
                        <button class="btn btn-sm btn-info btn-flat" type="button" id="add_item"><i class="fa fa-plus"></i> Add Passenger</button>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-primary btn-flat">Submit Reservation</button>
                    </div>
                </div>
                <!-- List Group -->

            </form>
        </div>
    </div>

  </section><!-- End Hero -->

  <!-- ======= Footer ======= -->
  <footer id="footer">
    <div class="container">

      <div class="copyright-wrap d-md-flex py-4">
        <div class="me-md-auto text-center text-md-start">
          <div class="copyright">
            &copy; Copyright <strong><span>MagLev</span></strong>. All Rights Reserved
          </div>
        </div>
      </div>

    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
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