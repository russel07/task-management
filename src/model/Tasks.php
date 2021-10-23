<?php

namespace Russel\WpTms\Model;

class Tasks
{
    protected $_wpdb;
    protected $tableName;
    protected $projectTable;
    protected $tasksInProject;
    public function __construct()
    {
        global $wpdb;
        $this->_wpdb = $wpdb;
        $this->tableName = $wpdb->prefix. wptms_tasks_table;
        $this->projectTable = $wpdb->prefix. wptms_project_table;
        $this->tasksInProject = $wpdb->prefix. wptms_project_tasks_table;
    }

    public function store($data){
        return $this->_wpdb->insert($this->tableName, $data);
    }

    public function find($id){
        return $this->_wpdb->get_row("SELECT * FROM $this->tableName WHERE id=$id");
    }

    public function findByCode($code){
        return $this->_wpdb->get_row("SELECT * FROM $this->tableName WHERE task_code = '$code'");
    }

    public function getAll($onlyActive = false){
        if (!$onlyActive)
            return $this->_wpdb->get_results("SELECT * FROM $this->tableName");
        else return $this->_wpdb->get_results("SELECT * FROM $this->tableName WHERE status = 'Active'");
    }

    public static function taskNotIn($list, $project){
        global $wpdb;

        $table_name = $wpdb->prefix. wptms_project_tasks_table;
        $dataAttributes = array_map(function($value) {
            return $value->task;
        }, array_values($list));

        $projectCode = array_map(function($value) {
            return $value->project_code;
        }, array_values($project));

        $list = '\''.implode("','",$dataAttributes).'\'';
        $projectCode = '\''.implode("','",$projectCode).'\'';
        return $wpdb->get_results("SELECT task_code, task_details FROM $table_name WHERE project_code IN($projectCode) AND task_code NOT IN ($list) AND status = 'Active' ORDER BY task_details ASC");
    }

    public function update($data, $id){
        return $this->_wpdb->update($this->tableName, $data, array('id' => $id));
    }
}