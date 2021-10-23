<?php

namespace Russel\WpTms\Model;

class ProjectInUser
{
    protected $tableName;
    protected $_wpdb;

    public function __construct() {
        global $wpdb;
        $this->tableName = $wpdb->prefix. wptms_project_in_users_table;
        $this->_wpdb = $wpdb;
    }

    public function getProjectByUser($userid){
        return $this->_wpdb->get_results("SELECT * FROM $this->tableName WHERE user_id = $userid");
    }

    public function getProjectCodeByUser($userid){
        return $this->_wpdb->get_results("SELECT DISTINCT project_code FROM $this->tableName WHERE user_id = $userid");
    }


    public function store($data){
        $this->_wpdb->insert($this->tableName, $data);
        return $this->_wpdb->insert_id;
    }

    public function update($data, $id){
        $this->_wpdb->update($this->tableName, $data, array('id' => $id));
    }

    public function destroy($id){
        $this->_wpdb->delete($this->tableName, array('id' => $id));
    }
}