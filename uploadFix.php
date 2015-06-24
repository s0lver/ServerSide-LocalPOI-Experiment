<?php
if(validate_input() == true){
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $altitude = $_POST['altitude'];
    $timestamp = $_POST['timestamp'];
    echo '<h1>Have a nice day</h1>';
}else{
    echo '<h1>I need more data</h1>';
}

/**
 * Evaluates the presence of required parameters in POST request
 * @return bool
 */
function validate_input(){
    if (!isset($_POST['latitude'])) {
        echo 'Not latitude';
        return false;
    }
    if (!isset($_POST['longitude'])) {
        echo 'Not longitude';
        return false;
    }
    if (!isset($_POST['altitude'])) {
        echo 'Not altitude';
        return false;
    }
    if (!isset($_POST['timestamp'])) {
        echo 'Not timestamp';
        return false;
    }
    return true;
}
?>
