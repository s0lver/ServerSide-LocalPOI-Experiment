<?php
$sql_select_temp_table_fixes = 'SELECT * FROM `smartphonefixes_temp` ORDER BY `timestamp` ASC';
$sql_select_last_trajectory = 'SELECT * FROM `trajectories` ORDER BY ID desc LIMIT 1';
$sql_select_fixes = 'SELECT * FROM `smartphonefixes` WHERE `idTrajectory` = %d ORDER BY `timestamp` ASC';
$sql_clear_table = 'DELETE FROM `smartphonefixes_temp`';

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
    global $sql_clear_table;
    $connection = get_connection();
    $connection->query($sql_clear_table);
    if ($connection->error){
        die('Could not clear fixes '.$connection->error);
    }
    $connection->close();
}

function get_last_id_trajectory(){
    global $sql_select_last_trajectory;

    $connection = get_connection();
    $result = $connection->query($sql_select_last_trajectory);

    $first_row = $result->fetch_assoc();
    $id_last_trajectory = $first_row["id"];
    $connection->close();

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

    $id_last_trajectory = $this->get_last_id_trajectory();
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

function create_formated_date($date_string)
{
    //Tue May 15 13:47:20 CDT 2012
    //D M d H:i:s e Y
    $date_format = 'D M d H:i:s e Y';
    $date = DateTime::createFromFormat($date_format, $date_string);
    $converted_date = $date->format('Y-m-d H:i:s');
    return $converted_date;
}

