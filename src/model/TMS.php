<?php

namespace Russel\WpTms\Model;

class TMS
{
    public static function find($id){
        global $wpdb;
        $table_name = $wpdb->prefix. wwptms_weekly_tasks_table;

        return $wpdb->get_row("SELECT * FROM $table_name WHERE id=$id");
    }

    public static function store($data){
        global $wpdb;
        $table_name = $wpdb->prefix. wwptms_weekly_tasks_table;

        $wpdb->insert($table_name, $data);
        $data['id'] = $wpdb->insert_id;

        return $data;
    }

    public static function update($data, $id){
        global $wpdb;
        $table_name = $wpdb->prefix. wwptms_weekly_tasks_table;

        $wpdb->update($table_name, $data, array('id' => $id));

        return $data;
    }

    public static function destroy($id){
        global $wpdb;
        $table_name = $wpdb->prefix. wwptms_weekly_tasks_table;

        return $wpdb->delete($table_name, array('id' => $id));
    }

    public static function getTaskListForWeek($start, $end){
        global $wpdb;
        $userId = get_current_user_id();
        $table_name = $wpdb->prefix. wwptms_weekly_tasks_table;
        return $wpdb->get_results("SELECT DISTINCT task FROM $table_name WHERE day_date BETWEEN '$start' AND '$end' AND user_id = $userId ORDER BY task DESC");
    }

    public static function getTaskDetailsForWeek($start, $end, $task){
        global $wpdb;
        $userId = get_current_user_id();
        $table_name = $wpdb->prefix. wwptms_weekly_tasks_table;
        return $wpdb->get_results("SELECT * FROM $table_name WHERE day_date BETWEEN '$start' AND '$end' AND task='$task' AND user_id = $userId");
    }
}