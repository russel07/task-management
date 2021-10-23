<?php

namespace Russel\WpTms\Model;

class User
{
    protected $tableName;
    protected $_wpdb;

    public function __construct()
    {
        global $wpdb;
        $this->_wpdb = $wpdb;
        $this->tableName = $wpdb->prefix . 'users';
    }

    public static function getUserListForAllocateHoliday(){
        global $wpdb;
        $user_table = $wpdb->prefix . 'users';
        $holiday = $wpdb->prefix . wptms_holiday_overview_table;

        $sql = "SELECT $user_table.ID as user_id, $user_table.display_name as name FROM $user_table WHERE $user_table.ID NOT IN(SELECT user_id FROM $holiday)";

        return $wpdb->get_results($sql, ARRAY_A);
    }

    public function findAll($onlyActive = false){
        if (!$onlyActive)
            return $this->_wpdb->get_results("SELECT * FROM $this->tableName");
        else return $this->_wpdb->get_results("SELECT * FROM $this->tableName WHERE user_status = 0");
    }
}