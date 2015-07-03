<?php
class GpsFix {
    var $sql_insert_fix = 'INSERT INTO `smartphonefixes` (`idTrajectory`, `latitude`, `longitude`, `timestamp`) VALUES (%d,%f,%f,\'%s\')';
    var $sql_insert_fix_temp_table = 'INSERT INTO `smartphonefixes_temp` (`idTrajectory`, `latitude`, `longitude`, `timestamp`) VALUES (%d,%f,%f,\'%s\')';

    var $latitude, $longitude, $timestamp;

    function __construct($latitude, $longitude, $timestamp)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->timestamp = $timestamp;
    }

    function __toString()
    {
        return "" . $this->latitude . ", " . $this->longitude . ", " . $this->timestamp;
    }


    public static function create_fix_from_parameters(){
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $timestamp = create_formated_date($_POST['timestamp']);
        return new GpsFix($latitude, $longitude, $timestamp);
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
        $ts1 = $other_fix->timestamp;
        $ts2 = $this->timestamp;
        $last_date = DateTime::createFromFormat('Y-m-d H:i:s', $ts2);
        $early_date = DateTime::createFromFormat('Y-m-d H:i:s', $ts1);

        $timespan = $last_date->getTimestamp() - $early_date->getTimestamp();
        return $timespan;
    }

    public function store(){
        $id_last_trajectory = get_last_id_trajectory();

        $connection = get_connection();
        $sql_insert_fix = sprintf($this->sql_insert_fix, $id_last_trajectory, $this->latitude, $this->longitude,
            $this->timestamp);

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
            $this->longitude, $this->timestamp);
        $connection->query($sql_insert_fix);
        if ($connection->error){
            die('Could not store fix'.$connection->error);
        }
        $connection->close();
    }
}