<?php

class ZhenLive
{
    var $min_time, $min_distance;
    var $log;

    public function __construct($min_time, $min_distance)
    {
        $this->min_time = $min_time;
        $this->min_distance = $min_distance;

        $this->log = new Logging();
        $this->log->lfile('C:\\wamp\\www\\local-poi\\mylogZhenLive.txt');
    }


    function process_fix(GpsFix $fix)
    {
        $this->log->lwrite("Processing FIX ".$fix);
        // 1.- Store the point
        $fix->store();
        $fix->store_on_temp_table();

        // 2.- Collect all fixes of this trajectory
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

        $timespan = $pjMinus->time_difference($pj);
        if ($timespan > $this->max_time) {
            $this->reset_table($pj);
            return null;
        }

        $distance = $pi->distance_to($pj);
        if ($distance > $this->min_distance) {
            $timespan = $pi->time_difference($pj);
            if ($timespan > $this->min_time) {
                $stay_point = Staypoint::create_from_fixes($fixes);
                $this->reset_table($pj);
                $this->log->lwrite("Stay point created! ".$stay_point);
                $stay_point->store();
                return $stay_point;
            }
            $this->reset_table($pj);
        }

        return null;
    }

    function process_last_part()
    {
        $stay_point = null;
        $fixes = get_temp_table_fixes();
        clear_tmp_table();

        if (count($fixes) > 0) {
            $stay_point = Staypoint::create_from_fixes($fixes);
            $stay_point->store();
            $this->log->lwrite("Creating stay point in last part: " . $stay_point);
            $lastFix = array_slice($fixes, -1)[0];
            update_trajectory_end_time($lastFix);
        }

        return $stay_point;
    }

    public function reset_table($pj)
    {
        clear_tmp_table();
        $pj->store_on_temp_table();
        $this->log->lwrite("Cleaning the list with: " . $pj);
    }
}