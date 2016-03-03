<?php
class GpsFix {
    var $sql_insert_fix = 'INSERT INTO `smartphonefixes` (`idTrajectory`, `latitude`, `longitude`, `timestamp`, `accuracy`) VALUES (%d,%f,%f,\'%s\',%f)';
    var $sql_insert_fix_temp_table = 'INSERT INTO `smartphonefixes_temp` (`idTrajectory`, `latitude`, `longitude`, `timestamp`, `accuracy`) VALUES (%d,%f,%f,\'%s\',%f)';

    var $latitude, $longitude, $timestamp, $accuracy;

    function __construct($latitude, $longitude, $timestamp, $accuracy)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->timestamp = $timestamp;
        $this->accuracy = $accuracy;
    }

    function __toString()
    {
        return "" . $this->latitude . ", " . $this->longitude . ", " . $this->timestamp . ", " . $this->accuracy;
    }


    public static function create_fix_from_parameters(){
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $timestamp = create_formated_date($_POST['timestamp']);
        $accuracy = $_POST['accuracy'];
        return new GpsFix($latitude, $longitude, $timestamp, $accuracy);
    }

    public function distance_to($other_fix){
        $lat1 = $this->latitude;
        $long1 = $this->longitude;
        $lat2 = $other_fix->latitude;
        $long2 = $other_fix->longitude;

        $distance = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($long1 - $long2)));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $distance = $distance * 60 * 1.1515 * 1.609344;

        return $distance*1000;
    }

    public function time_difference(GpsFix $other_fix)
    {
        date_default_timezone_set("America/Mexico_City");
        $ts1 = $this->timestamp;
        $ts2 = $other_fix->timestamp;
        $early_date = DateTime::createFromFormat('Y-m-d H:i:s', $ts1);
        $last_date = DateTime::createFromFormat('Y-m-d H:i:s', $ts2);

        $timespan = $last_date->getTimestamp() - $early_date->getTimestamp();
        return $timespan * 1000; // the timespan is obtained in seconds
    }

    public function store(){
        $id_last_trajectory = get_last_id_trajectory();

        $connection = get_connection();
        $sql_insert_fix = sprintf($this->sql_insert_fix, $id_last_trajectory, $this->latitude, $this->longitude,
            $this->timestamp, $this->accuracy);

        $connection->query($sql_insert_fix);
        if ($connection->error){
            die('Could not store fix'.$connection->error);
        }
        $connection->close();

    }

    public function store_on_temp_table()
    {
        $id_last_trajectory = get_last_id_trajectory();
        $connection = get_connection();
        $sql_insert_fix = sprintf($this->sql_insert_fix_temp_table, $id_last_trajectory, $this->latitude,
            $this->longitude, $this->timestamp, $this->accuracy);
        $connection->query($sql_insert_fix);
        if ($connection->error){
            die('Could not store fix'.$connection->error);
        }
        $connection->close();
    }
}