<?php

namespace Russel\WpTms\Model;

class Employee
{

    public static function getAll(){
        global $wpdb;
        $user_table = $wpdb->prefix . 'users';
        $employee = $wpdb->prefix . wptms_employee_table;

        $sql = "SELECT $user_table.*, $employee.id as employee_id, $employee.employee_name as employee_name, $employee.designation as designation, $employee.join_date as join_date FROM $user_table INNER JOIN $employee ON $user_table.ID = $employee.user_id";

        return $wpdb->get_results($sql, ARRAY_A);
    }

    public static function find($id){
        global $wpdb;
        $user_table = $wpdb->prefix . 'users';
        $employee = $wpdb->prefix . wptms_employee_table;

        $sql = "SELECT $user_table.*, $employee.id as employee_id, $employee.employee_name as employee_name, $employee.designation as designation, $employee.join_date as join_date FROM $user_table INNER JOIN $employee ON $user_table.ID = $employee.user_id WHERE $employee.id = $id";

        return $wpdb->get_row($sql, ARRAY_A);
    }

}