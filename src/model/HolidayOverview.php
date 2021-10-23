<?php

namespace Russel\WpTms\Model;

class HolidayOverview
{
    public static function getAll(){
        global $wpdb;
        $user_table = $wpdb->prefix . 'users';
        $holiday_table = $wpdb->prefix . wptms_holiday_overview_table;

        $sql = "SELECT $user_table.*, $holiday_table.id as leave_id, $holiday_table.bank_holiday as bank_holiday, $holiday_table.annual_leave_allocated as annual_leave_allocated, 
       $holiday_table.annual_leave_taken as annual_leave_taken, $holiday_table.sick_leave as sick_leave FROM $user_table 
           INNER JOIN $holiday_table ON $user_table.ID = $holiday_table.user_id";

        return $wpdb->get_results($sql, ARRAY_A);
    }

    public static function getHolidayByUserId($userid){
        global $wpdb;

        $holiday_table = $wpdb->prefix . wptms_holiday_overview_table;

        return $wpdb->get_row("SELECT * from $holiday_table WHERE user_id=$userid");

    }

    public static function store($Holiday = array()){
        global $wpdb;
        $holiday_table = $wpdb->prefix . wptms_holiday_overview_table;
        return $wpdb->insert($holiday_table, $Holiday);
    }

    public static function update($data, $id){
        global $wpdb;
        $holiday_table = $wpdb->prefix . wptms_holiday_overview_table;

        return $wpdb->update($holiday_table, $data, array('id' => $id));
    }

    public static function destroy($id){
        global $wpdb;
        $holiday_table = $wpdb->prefix . wptms_holiday_overview_table;

        return $wpdb->delete($holiday_table, array('id' => $id));

    }
}