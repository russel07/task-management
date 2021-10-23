<?php
namespace Russel\WpTms\Helper;

class appHelper
{
    public static function sum_the_time($time1, $time2){
        $times = array($time1, $time2);
        $seconds = 0;
        foreach ($times as $time)
        {
            list($hour,$minute) = explode(':', $time);
            $seconds += $hour*3600;
            $seconds += $minute*60;
        }
        $hours = floor($seconds/3600);
        $seconds -= $hours*3600;
        $minutes  = floor($seconds/60);

        if($minutes < 9)
        {
            $minutes = "0".$minutes;
        }
        if($hours < 9)
        {
            $hours = "0".$hours;
        }
        return "{$hours}:{$minutes}";
    }

    public static function getTaskName($task){
        if($task == 'sl_hf')
            return 'Sick Leave Half Day';
        else if($task == 'sl_fd')
            return 'Sick Leave Full Day';
        else if($task == 'al_hf')
            return 'Annual Leave Half Day';
        else if($task == 'al_fd')
            return 'Annual Leave Full Day';
        else if($task == 'bank_holiday_fd')
            return 'Bank Holiday';
        else return $task;
    }
}