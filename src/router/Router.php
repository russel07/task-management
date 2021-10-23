<?php

namespace Russel\WpTms\Router;

use Russel\WpTms\Controller\EmployeeController;
use Russel\WpTms\Controller\HolidayOverviewController;
use Russel\WpTms\Controller\ProjectController;
use Russel\WpTms\Controller\TaskController;

class Router
{
    protected $loader;
    public function __construct($loader)
    {
        $this->loader = $loader;
        $this->wptms_routes();
    }

    public function wptms_routes(){
        $employeeController = new EmployeeController();
        $holidayController  = new HolidayOverviewController();
        $projectController  = new ProjectController();
        $taskController     = new TaskController();

        $this->loader->add_action('wp_ajax_get_employees', $employeeController, 'wptms_get_employees');
        $this->loader->add_action('wp_ajax_get_employee', $employeeController, 'wptms_get_employee');
        $this->loader->add_action('wp_ajax_save_leave_request', $employeeController, 'wptms_save_leave_request');
        $this->loader->add_action('wp_ajax_save_task', $employeeController, 'wptms_save_task');
        $this->loader->add_action('wp_ajax_update_task', $employeeController, 'wptms_update_task');
        $this->loader->add_action('wp_ajax_delete_task', $employeeController, 'wptms_delete_task');

        $this->loader->add_action('wp_ajax_get_holiday_list', $holidayController, 'wptms_get_holiday_list');
        $this->loader->add_action('wp_ajax_delete_holiday', $holidayController, 'wptms_delete_holiday');
        $this->loader->add_action('wp_ajax_get_user_list', $holidayController, 'wptms_get_user_list');
        $this->loader->add_action('wp_ajax_wptms_get_active_users', $holidayController, 'wptms_get_active_users');
        $this->loader->add_action('wp_ajax_insert_holiday', $holidayController, 'wptms_insert_holiday');

        $this->loader->add_action('wp_ajax_wptms_create_project', $projectController, 'wptms_create_project');
        $this->loader->add_action('wp_ajax_get_projects', $projectController, 'wptms_get_projects');
        $this->loader->add_action('wp_ajax_get_active_projects', $projectController, 'wptms_get_active_projects');
        $this->loader->add_action('wp_ajax_change_project_status', $projectController, 'wptms_change_project_status');
        $this->loader->add_action('wp_ajax_wptms_get_project_by_user', $projectController, 'wptms_get_project_by_user');
        $this->loader->add_action('wp_ajax_wptms_allocate_deallocate_project', $projectController, 'wptms_allocate_deallocate_project');
        $this->loader->add_action('wp_ajax_wptms_remove_project_from_user', $projectController, 'wptms_remove_project_from_user');

        $this->loader->add_action('wp_ajax_wptms_create_task', $taskController, 'wptms_create_task');
        $this->loader->add_action('wp_ajax_wptms_get_tasks', $taskController, 'wptms_get_tasks');
        $this->loader->add_action('wp_ajax_wptms_change_task_status', $taskController, 'wptms_change_task_status');
        $this->loader->add_action('wp_ajax_wptms_get_task_by_project', $taskController, 'wptms_get_task_by_project');
        $this->loader->add_action('wp_ajax_wptms_allocate_deallocate_task', $taskController, 'wptms_change_allocate_task_in_project');
        $this->loader->add_action('wp_ajax_wptms_delete_task_from_project', $taskController, 'wptms_remove_task_from_project');

    }
}