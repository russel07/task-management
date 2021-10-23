<?php

namespace Russel\WpTms\Controller;

use Russel\WpTms\Model\ProjectInUser;
use Russel\WpTms\Model\Projects;
use Russel\WpTms\Model\TaskInProject;

class ProjectController
{
    protected $model;
    protected $TaskInProject;
    protected $ProjectInUser;
    protected $project;

    public function __construct()
    {
        $this->model = new Projects();
        $this->TaskInProject = new TaskInProject();
        $this->ProjectInUser = new ProjectInUser();
        $this->project = new Projects();
    }

    public function wptms_create_project(){
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

        if(isset($_REQUEST['project_code']) && !empty($_REQUEST['project_code']) && isset($_REQUEST['project_name']) && !empty($_REQUEST['project_name'])) {
            $data = array(
                'project_code' => $_REQUEST['project_code'],
                'project_name' => $_REQUEST['project_name']
            );

            if($this->model->store($data)){
                wp_send_json(array(
                    'status' => true,
                    'message' => 'Project created successfully'
                ));
                wp_die();
            }else{
                wp_send_json(array(
                    'status' => false,
                    'message' => 'Something went wrong! Please try again later'
                ));
                wp_die();
            }
        }
    }

    public function wptms_get_projects(){
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

        $projects = $this->model->getAll();

        wp_send_json($projects);
        wp_die();
    }

    public function wptms_get_active_projects(){
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

        $projects = $this->model->getAll(true);

        wp_send_json(array(
            'status' => true,
            'data' => $projects
        ));
        wp_die();
    }

    public function wptms_change_project_status(){
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
                $this->TaskInProject->DeactivateTasks($row->project_code);
            }else{
                $this->TaskInProject->ActivateTasks($row->project_code);
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

    public function wptms_get_project_by_user(){
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

        if(isset($_REQUEST['userId']) && !empty($_REQUEST['userId'])){
            $userId = $_REQUEST['userId'];
            $list = $this->ProjectInUser->getProjectByUser($userId);
            $projects = $this->model->getAll(true);

            foreach ($projects as $project){
                $projectCode = $project->project_code;
                foreach ($list as $item){
                    if($projectCode === $item->project_code){
                        $project->allocated_id = $item->id;
                        $project->allocated_status = ($item->status === 'Active') ? 'Allocated': 'Deallocated';
                    }
                }
            }

            wp_send_json(array(
                'status' => true,
                'projects' => $projects
            ));
            wp_die();
        }
        wp_send_json(array(
            'status' => false,
            'message' => 'Invalid request submitted'
        ));
        wp_die();
    }

    public function wptms_allocate_deallocate_project(){
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

        if(isset($_REQUEST['project_code']) && !empty($_REQUEST['project_code']) && isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])){
            $projectInfo = $this->project->findByCode($_REQUEST['project_code']);
            if(isset($_REQUEST['allocated_status']) && !empty($_REQUEST['allocated_status'])){
                if($_REQUEST['allocated_status'] === 'Allocate'){
                    $data = array(
                        'project_code' => $_REQUEST['project_code'],
                        'user_id' => $_REQUEST['user_id'],
                        'status' => 'Active'
                    );

                    $projectInfo->allocated_id = $this->ProjectInUser->store($data);
                    $projectInfo->allocated_status = 'Allocated';

                    wp_send_json(array(
                        'status' => true,
                        'message' => 'Project Allocated Successfully',
                        'data' => $projectInfo
                    ));
                    wp_die();
                }else{
                    if(isset($_REQUEST['allocated_status']) && !empty($_REQUEST['allocated_status'])){
                        $id = $_REQUEST['allocated_id'];
                        $data = array(
                            'status' => $_REQUEST['allocated_status'] === 'Allocated' ? 'Inactive': 'Active'
                        );
                        $this->ProjectInUser->update($data, $id);
                        $projectInfo->allocated_id = $id;
                        $projectInfo->allocated_status = $_REQUEST['allocated_status'] === 'Allocated' ? 'Deallocated': 'Allocated';

                        wp_send_json(array(
                            'status' => true,
                            'message' => 'Project '.$projectInfo->allocated_status.' Successfully',
                            'data' => $projectInfo
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

    public function wptms_remove_project_from_user(){
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

        if(isset($_REQUEST['project_id']) && !empty($_REQUEST['project_id']) && isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
            $taskInfo = $this->model->find($_REQUEST['project_id']);
            $id = $_REQUEST['id'];
            $this->ProjectInUser->destroy($id);

            $taskInfo->allocated_id = 0;
            $taskInfo->allocated_status = '';
            wp_send_json(array(
                'status' => true,
                'message' => 'Project removed successfully',
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