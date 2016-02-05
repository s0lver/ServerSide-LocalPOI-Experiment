<?php

class MontoliouLive
{
    var $min_time, $max_time, $min_distance;

    public function __construct($min_time, $max_time, $min_distance)
    {
        $this->min_time = $min_time;
        $this->max_time = $max_time;
        $this->min_distance = $min_distance;
    }


    function process_fix(GpsFix $fix)
    {
        // 1.- Store the point
        $fix->store();
        $fix->store_on_temp_table();

        // 2.- Collect all fixes of this trajectory
        // $fixes = get_trajectory_points();
        $fixes = get_temp_table_fixes();
        $size = count($fixes);

        if ($size == 1) {
            return null;
        }else{
            $stay_point = $this->process_live($fixes);
            return $stay_point;
        }
    }

    function process_live($fixes){
        $size = count($fixes);

        $pi = $fixes[0];
        $pj = $fixes[$size-1];
        $pjMinus = $fixes[$size-2];

        $timespan = $pj->time_difference($pjMinus);
        echo "timestamp is " . $timespan." ";
        if ($timespan > $this->max_time) {
            $this->reset_table($pj);
            return null;
        }

        $distance = $pi->distance_to($pj);
        echo "Distance is " . $distance." " ;
        if ($distance > $this->min_distance) {
            $pj->time_difference($pi);
            echo "timestamp 2 is " . $timespan." ";
            if ($timespan > $this->min_time) {
                $stay_point = Staypoint::create_from_fixes($fixes);
                $this->reset_table($pj);
                return $stay_point;
            }
            $this->reset_table($pj);
        }

    }

    function process_last_part()
    {
        $fixes = get_temp_table_fixes();
        $stay_point = Staypoint::create_from_fixes($fixes);
        clear_tmp_table();

        if (count($fixes) > 0) {
            $lastFix = array_slice($fixes, -1)[0];
            update_trajectory_end_time($lastFix);
        }

        return $stay_point;
    }

    public function reset_table($pj)
    {
        clear_tmp_table();
        $pj->store_on_temp_table();
    }
}