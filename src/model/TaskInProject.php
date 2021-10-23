<?php

namespace Russel\WpTms\Model;

class TaskInProject
{
    protected $_wpdb;
    protected $tableName;
    public function __construct()
    {
        global $wpdb;
        $this->_wpdb = $wpdb;
        $this->tableName = $wpdb->prefix. wptms_project_tasks_table;
    }

    public function ActivateTasks($projectCode){
        $data['status'] = 'Active';
        return $this->_wpdb->update($this->tableName, $data, array('project_code' => $projectCode));
    }

    public function DeactivateTasks($projectCode){
        $data['status'] = 'Inactive';
        return $this->_wpdb->update($this->tableName, $data, array('project_code' => $projectCode));
    }

    public function ActivateTasksByTask($taskCode){
        $data['status'] = 'Active';
        return $this->_wpdb->update($this->tableName, $data, array('tasks_code' => $taskCode));
    }

    public function DeactivateTasksByTask($taskCode){
        $data['status'] = 'Inactive';
        return $this->_wpdb->update($this->tableName, $data, array('tasks_code' => $taskCode));
    }

    public function getTasksByProject($projectCode){
        return $this->_wpdb->get_results("SELECT * FROM $this->tableName WHERE project_code = '$projectCode'");
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