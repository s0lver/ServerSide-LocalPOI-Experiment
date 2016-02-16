<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
require_once("dbConnection.php");
require_once("MontoliouLive.php");
require_once("GpsFix.php");
require_once("Staypoint.php");
require_once("request_tools.php");
require_once("Logging.php");

if (isset($_POST['createTrajectory'])){
    $min_time = $_POST["minTime"];
    $max_time = $_POST["maxTime"];
    $min_distance = $_POST["minDistance"];

    $time_zone = new DateTimeZone("America/Mexico_City");
    $date_now = new DateTime("now",$time_zone);
    $converted_date = $date_now->format('Y-m-d H:i:s');

    $log = new Logging();
    $log->lfile('logs/mylogTrajectory.txt');
    $log->lwrite("fecha es: " . $converted_date);

    insert_new_trajectory($converted_date, $converted_date, $min_distance, $min_time, $max_time);

    echo "trajOk";
}
else {
    $last_trajectory = get_last_trajectory();
    $min_time = $last_trajectory["minTime"];
    $max_time = $last_trajectory["maxTime"];
    $min_distance = $last_trajectory["minDistance"];

    $ml = new MontoliouLive($min_time, $max_time, $min_distance);

    if (!isset($_POST['lastPart'])) {
        if (validate_fix_input()) {
            $fix = GpsFix::create_fix_from_parameters();

            $stay_point = $ml->process_fix($fix);

            if ($stay_point != null) {
                echo $stay_point;
            } else {
                echo -1;
            }
        } else {
            echo -2;
        }
    } else {
        $stay_point = $ml->process_last_part();
        if ($stay_point != null) {
            echo $stay_point;
        } else {
            echo - 1;
        }
    }
}