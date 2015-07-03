<?php
require_once("MontoliouLive.php");
require_once("GpsFix.php");
require_once("request_tools.php");

$ml = new MontoliouLive();

if (!isset($_POST['lastPart'])){
    if (validate_fix_input()){
        $fix = GpsFix::create_fix_from_parameters();


        $poi = $ml->process_fix($fix);

        if ($poi != null) {
            echo $poi;
        } else {
            echo -1;
        }
    }
    else{
        echo -2;
    }
}
else{
    $poi = $ml->process_last_part();
    if ($poi != null) {
        echo $poi;
    } else {
        $echo -1;
    }
}

