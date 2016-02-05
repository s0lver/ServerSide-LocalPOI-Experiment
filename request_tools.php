<?php
$sql_select_temp_table_fixes = 'SELECT * FROM `smartphonefixes_temp` ORDER BY `timestamp` ASC';
$sql_select_last_trajectory = 'SELECT * FROM `trajectories` ORDER BY ID desc LIMIT 1';
$sql_insert_trajectory = 'INSERT INTO `trajectories` (`startTime`, `endTime`, `minDistance`, `minTime`, `maxTime`) VALUES (\'%s\',\'%s\',%d,%d,%d)';
$sql_select_fixes = 'SELECT * FROM `smartphonefixes` WHERE `idTrajectory` = %d ORDER BY `timestamp` ASC';
$sql_clear_temp_table = 'DELETE FROM `smartphonefixes_temp`';
$sql_update_trajectory_end_time = 'UPDATE `trajectories` SET `endTime`=\'%s\' WHERE `id`=%d';

function validate_fix_input(){
    if (!isset($_POST['latitude'])) {
        return false;
    }
    if (!isset($_POST['longitude'])) {
        return false;
    }
    if (!isset($_POST['timestamp'])) {
        return false;
    }
    return true;
}

function clear_tmp_table(){
    global $sql_clear_temp_table;
    $connection = get_connection();
    $connection->query($sql_clear_temp_table);
    if ($connection->error){
        die('Could not clear fixes '.$connection->error);
    }
    $connection->close();
}

function get_last_trajectory(){
    global $sql_select_last_trajectory;
    $connection = get_connection();

    $result = $connection->query($sql_select_last_trajectory);
    $trajectory = $result->fetch_assoc();
    $connection->close();

    return $trajectory;
}

function get_last_id_trajectory(){
    $trajectory = get_last_trajectory();
    $id_last_trajectory = $trajectory["id"];

    return $id_last_trajectory;
}

function get_temp_table_fixes()
{
    global $sql_select_temp_table_fixes;
    $fixes = array();

    $connection = get_connection();
    $result = $connection->query($sql_select_temp_table_fixes);

    if ($connection->error){
        die('Could not obtain fixes '.$connection->error);
    }

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $current_fix = new GpsFix($row['latitude'], $row['longitude'], $row['timestamp']);
            $fixes[] = $current_fix;
        }
    }

    $connection->close();
    return $fixes;
}

function get_trajectory_points(){
    global $sql_select_fixes;
    $fixes = array();

    $id_last_trajectory = get_last_id_trajectory();
    $sql_select_trajectory_fixes = sprintf($sql_select_fixes, $id_last_trajectory);

    $connection = get_connection();
    $result = $connection->query($sql_select_trajectory_fixes);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $current_fix = new GpsFix($row['latitude'], $row['longitude'], $row['timestamp']);
            $fixes[] = $current_fix;
        }
    }

    $connection->close();
    return $fixes;
}

function insert_new_trajectory($start_time, $end_time, $min_distance, $min_time, $max_time){
    global $sql_insert_trajectory;
    $sql_insert_trajectory = sprintf($sql_insert_trajectory, $start_time, $end_time, $min_distance, $min_time, $max_time);

    $connection = get_connection();
    $connection->query($sql_insert_trajectory);

    if ($connection->error){
        die('Could not store staypoint'.$connection->error);
    }
    $connection->close();
}

function create_formated_date($date_string)
{
    //Tue May 15 13:47:20 CDT 2012
    //D M d H:i:s e Y
    //$date_format = 'D M d H:i:s e Y';

    // nuevo y definitivo: 20/10/2015 19:00:36
    $date_format = 'd/m/Y H:i:s';
    $mysql_date_format = 'Y-m-d H:i:s';
    $date = DateTime::createFromFormat($date_format, $date_string);
    $converted_date = $date->format($mysql_date_format);
    return $converted_date;
}

function update_trajectory_end_time(GpsFix $gpsFix)
{
    global $sql_update_trajectory_end_time;

    $last_id = get_last_id_trajectory();
    $sql_update_trajectory_end_time = sprintf($sql_update_trajectory_end_time, $gpsFix->timestamp, $last_id);

    $connection = get_connection();
    $connection->query($sql_update_trajectory_end_time);

    if ($connection->error){
        die('Could not update trajectory info'.$connection->error);
    }
    $connection->close();

}
