<?php

namespace Russel\WpTms\Controller;

use Russel\WpTms\Model\Projects;
use Russel\WpTms\Model\TaskInProject;
use Russel\WpTms\Model\Tasks;

class TaskController
{

    protected $model;
    protected $TaskInProject;
    protected $project;

    public function __construct()
    {
        $this->model = new Tasks();
        $this->TaskInProject = new TaskInProject();
        $this->project = new Projects();
    }

    public function wptms_create_task(){
        if(!is_user_logged_in()){
            wp_send_json(array(
                'status' => false,
                'message' =>  'Need login to submit request'
            ));
            wp_die();
        }

        if(!check_ajax_referer(NONCE, 'nonce')){
            wp_send_json(array(
                'status' => false,
                'message' => 'Invalid request submitted'
            ));
        }

        if(isset($_REQUEST['task_code']) && !empty($_REQUEST['task_code']) && isset($_REQUEST['task_name']) && !empty($_REQUEST['task_name'])){
            $data = array(
                'task_code' => $_REQUEST['task_code'],
                'task_name' => $_REQUEST['task_name']
            );

            if($this->model->store($data)){
                wp_send_json(array(
                    'status' => true,
                    'message' => 'Task created successfully'
                ));
            }else{
                wp_send_json(array(
                    'status' => false,
                    'message' => 'Something went wrong!! Please try again later'
                ));
            }
        }
    }

    public function wptms_get_tasks(){
        if(!is_user_logged_in()) {
            wp_send_json(array(
                'status' => false,
                'message' => 'Need to login to submit request'
            ));
            wp_die();
        }

        if(!check_ajax_referer( NONCE, 'nonce' )) {
            wp_send_json(array(
                'status' => false,
                'message' => 'Invalid request submitted'
            ));
            wp_die();
        }

        $tasks = $this->model->getAll();

        wp_send_json($tasks);
        wp_die();
    }

    public function wptms_change_task_status(){

        if(!is_user_logged_in()) {
            wp_send_json(array(
                'status' => false,
                'message' => 'Need to login to submit request'
            ));
            wp_die();
        }

        if(!check_ajax_referer( NONCE, 'nonce' )) {
            wp_send_json(array(
                'status' => false,
                'message' => 'Invalid request submitted'
            ));
            wp_die();
        }



        if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
            $id = $_REQUEST['id'];

            $row = $this->model->find($id);

            $row->status = $row->status === 'Active' ? 'Inactive' : 'Active';

            $this->model->update((array)$row, $id);

            if($row->status === 'Inactive'){
                $this->TaskInProject->DeactivateTasksByTask($row->task_code);
            }else{
                $this->TaskInProject->ActivateTasksByTask($row->task_code);
            }

            wp_send_json(array(
                'status' => false,
                'message' => 'Status updated successfully'
            ));
        }

        wp_send_json(array(
            'status' => false,
            'message' => 'Invalid request submitted'
        ));
        wp_die();
    }

    public function wptms_get_task_by_project(){
        if(!is_user_logged_in()){
            wp_send_json(array(
                'status' => false,
                'message' => 'Need login to send request'
            ));
            wp_die();
        }

        if(!check_ajax_referer(NONCE, 'nonce')){
            wp_send_json(array(
                'status' => false,
                'message' => 'Invalid request submitted'
            ));
            wp_die();
        }

        if(isset($_REQUEST['project_code']) && !empty($_REQUEST['project_code'])){
            $projectCode = $_REQUEST['project_code'];

            $list = $this->TaskInProject->getTasksByProject($projectCode);
            $tasks = $this->model->getAll(true);

            foreach ($tasks as $task){
                $taskCode = $task->task_code;
                foreach ($list as $item){
                    if($taskCode === $item->tasks_code){
                        $task->allocated_id = $item->id;
                        $task->allocated_status = ($item->status === 'Active') ? 'Allocated': 'Deallocated';
                    }
                }
            }

            wp_send_json(array(
                'status' => true,
                'tasks' => $tasks
            ));
            wp_die();
        }else{
            wp_send_json(array(
                'status' => false,
                'message' => 'Invalid request submitted'
            ));
            wp_die();
        }
    }

    public function wptms_change_allocate_task_in_project(){

        if(!is_user_logged_in()){
            wp_send_json(array(
                'status' => false,
                'message' => 'Need login to send request'
            ));
            wp_die();
        }

        if(!check_ajax_referer(NONCE, 'nonce')){
            wp_send_json(array(
                'status' => false,
                'message' => 'Invalid request submitted'
            ));
            wp_die();
        }

        if(isset($_REQUEST['project_code']) && !empty($_REQUEST['project_code']) && isset($_REQUEST['task_code']) && !empty($_REQUEST['task_code'])){
            $projectInfo = $this->project->findByCode($_REQUEST['project_code']);
            $taskInfo = $this->model->findByCode($_REQUEST['task_code']);
            if(isset($_REQUEST['allocated_status']) && !empty($_REQUEST['allocated_status'])){
                if($_REQUEST['allocated_status'] === 'Allocate'){
                    $data = array(
                        'project_code' => $_REQUEST['project_code'],
                        'tasks_code' => $_REQUEST['task_code'],
                        'task_code' => $projectInfo->project_code.'_'.$taskInfo->task_code,
                        'task_details' => $projectInfo->project_name.' '.$taskInfo->task_name,
                        'status' => 'Active'
                    );

                    $taskInfo->allocated_id = $this->TaskInProject->store($data);
                    $taskInfo->allocated_status = 'Allocated';

                    wp_send_json(array(
                        'status' => true,
                        'message' => 'Task allocated successfully',
                        'data' => $taskInfo
                    ));
                    wp_die();
                }else{
                    if(isset($_REQUEST['allocated_status']) && !empty($_REQUEST['allocated_status'])){
                        $id = $_REQUEST['allocated_id'];
                        $data = array(
                            'status' => $_REQUEST['allocated_status'] === 'Allocated' ? 'Inactive': 'Active'
                        );
                        $this->TaskInProject->update($data, $id);
                        $taskInfo->allocated_id = $id;
                        $taskInfo->allocated_status = $_REQUEST['allocated_status'] === 'Allocated' ? 'Deallocated': 'Allocated';

                        wp_send_json(array(
                            'status' => true,
                            'message' => 'Task allocated successfully',
                            'data' => $taskInfo
                        ));
                        wp_die();
                    }else{
                        wp_send_json(array(
                            'status' => false,
                            'message' => 'Invalid request submitted'
                        ));
                        wp_die();
                    }
                }
            }else{
                wp_send_json(array(
                    'status' => false,
                    'message' => 'Invalid request submitted'
                ));
                wp_die();
            }
        }else{
            wp_send_json(array(
                'status' => false,
                'message' => 'Invalid request submitted'
            ));
            wp_die();
        }
    }

    public function wptms_remove_task_from_project(){
        if(!is_user_logged_in()){
            wp_send_json(array(
                'status' => false,
                'message' => 'Need login to send request'
            ));
            wp_die();
        }

        if(!check_ajax_referer(NONCE, 'nonce')){
            wp_send_json(array(
                'status' => false,
                'message' => 'Invalid request submitted'
            ));
            wp_die();
        }

        if(isset($_REQUEST['task_id']) && !empty($_REQUEST['task_id']) && isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
            $taskInfo = $this->model->find($_REQUEST['task_id']);
            $id = $_REQUEST['id'];
            $this->TaskInProject->destroy($id);

            $taskInfo->allocated_id = 0;
            $taskInfo->allocated_status = '';
            wp_send_json(array(
                'status' => true,
                'message' => 'Task allocated successfully',
                'data' => $taskInfo
            ));
            wp_die();
        }else{
            wp_send_json(array(
                'status' => false,
                'message' => 'Invalid request submitted'
            ));
            wp_die();
        }
    }
}