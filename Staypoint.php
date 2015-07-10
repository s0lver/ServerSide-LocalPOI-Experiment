<?php
class Staypoint{
    var $sql_insert_staypoint = 'INSERT INTO `pois` (`idTrajectory`,`latitude`,`longitude`,`arrivalTime`,`departureTime`) VALUES (%d,%f,%f,\'%s\',\'%s\')';

    var $id_trajectory;
    var $latitude;
    var $longitude;
    var $arrival_time;
    var $departure_time;
    var $involved_fixes;

    public function __construct($p_latitude, $p_longitude){
        $this->latitude = $p_latitude;
        $this->longitude = $p_longitude;
    }

    public static function create_from_fixes($fixes)
    {
        $size = count($fixes);
        $sumLat = 0.0;
        $sumLng = 0.0;

        echo "New sp generated, fixes involved:\n";
        for ($i = 0; $i < $size; $i++) {
            $sumLat += $fixes[$i]->latitude;
            $sumLng += $fixes[$i]->longitude;
            echo $fixes[$i]."\n";
        }

        $lat = $sumLat / $size;
        $lng = $sumLng / $size;

        $sp = new Staypoint($lat,$lng);
        $sp->arrival_time = $fixes[0]->timestamp;
        $sp->departure_time = $fixes[$size - 1]->timestamp;
        $sp->involved_fixes = $size;

        return $sp;
    }

    public function __toString() {
        return $this->latitude.",".$this->longitude.",".$this->arrival_time.",".$this->departure_time.",".$this->involved_fixes;
    }

    public function store()
    {
        $id_last_trajectory = get_last_id_trajectory();
        $sql_insert_staypoint = sprintf($this->sql_insert_staypoint, $id_last_trajectory,
            $this->latitude, $this->longitude, $this->arrival_time, $this->departure_time);

        $connection = get_connection();
        $connection->query($sql_insert_staypoint);

        if ($connection->error){
            die('Could not store staypoint'.$connection->error);
        }
        $connection->close();
    }
}
