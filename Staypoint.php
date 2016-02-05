<?php
class Staypoint{
    var $sql_insert_staypoint = 'INSERT INTO `pois` (`idTrajectory`,`latitude`,`longitude`,`arrivalTime`,`departureTime`,`fixesInvolved`) VALUES (%d,%f,%f,\'%s\',\'%s\',%d)';

    var $id_trajectory;
    var $latitude;
    var $longitude;
    var $arrival_time;
    var $departure_time;
    var $fixes_involved;

    public function __construct($p_latitude, $p_longitude){
        $this->latitude = $p_latitude;
        $this->longitude = $p_longitude;
    }

    public static function create_from_fixes($fixes)
    {
        $size = count($fixes);

        $sumLat = 0.0;
        $sumLng = 0.0;

        for ($i = 0; $i < $size; $i++) {
            $sumLat += $fixes[$i]->latitude;
            $sumLng += $fixes[$i]->longitude;
        }

        $lat = $sumLat / $size;
        $lng = $sumLng / $size;

        $sp = new Staypoint($lat,$lng);
        $sp->arrival_time = $fixes[0]->timestamp;
        $sp->departure_time = $fixes[$size - 1]->timestamp;
        $sp->fixes_involved = $size;

        return $sp;
    }

    public function __toString() {
        $smartphone_date_format = 'd/m/Y H:i:s';
        $mysql_date_format = 'Y-m-d H:i:s';
        $date_arrival = DateTime::createFromFormat($mysql_date_format, $this->arrival_time);
        $date_departure = DateTime::createFromFormat($mysql_date_format, $this->departure_time);

        $str_arrival_time = $date_arrival->format($smartphone_date_format);
        $str_departure_time = $date_departure->format($smartphone_date_format);

        return $this->latitude.",".$this->longitude.",".$str_arrival_time.",".$str_departure_time.",".$this->fixes_involved;
    }

    public function store()
    {
        $id_last_trajectory = get_last_id_trajectory();
        $sql_insert_staypoint = sprintf($this->sql_insert_staypoint, $id_last_trajectory,
            $this->latitude, $this->longitude, $this->arrival_time, $this->departure_time, $this->fixes_involved);

        $connection = get_connection();
        $connection->query($sql_insert_staypoint);

        if ($connection->error){
            die('Could not store staypoint'.$connection->error);
        }
        $connection->close();
    }
}
