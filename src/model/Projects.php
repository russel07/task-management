<?php

namespace Russel\WpTms\Model;

class Projects
{
    protected $_wpdb;
    protected $tableName;
    public function __construct()
    {
        global $wpdb;
        $this->_wpdb = $wpdb;
        $this->tableName = $wpdb->prefix. wptms_project_table;
    }

    public function getAll($onlyActive = false)
    {
        if (!$onlyActive)
            return $this->_wpdb->get_results("SELECT * FROM $this->tableName");
        else return $this->_wpdb->get_results("SELECT * FROM $this->tableName WHERE status = 'Active'");
    }

    public function find($id){
        return $this->_wpdb->get_row("SELECT * FROM $this->tableName WHERE id = $id");
    }

    public function findByCode($code){
        return $this->_wpdb->get_row("SELECT * FROM $this->tableName WHERE project_code = '$code'");
    }

    public function store($data){
        return $this->_wpdb->insert($this->tableName, $data);
    }

    public function update($data, $id){
        return $this->_wpdb->update($this->tableName, $data, array('id' => $id));
    }

    public function destroy($id){
        return $this->_wpdb->delete($this->tableName, array('id' => $id));
    }
}