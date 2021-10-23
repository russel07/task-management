<?php

namespace Russel\WpTms\Controller;

use Russel\WpTms\Model\Employee;
use Russel\WpTms\Model\HolidayOverview;
use Russel\WpTms\Model\TMS;

class EmployeeController
{
    public function wptms_get_employees(){
        $employees = Employee::getAll();

        wp_send_json($employees);
        wp_die();
    }

    public function wptms_get_employee(){
        if(isset($_REQUEST['id']) && $_REQUEST['id']){
            $id = $_REQUEST['id'];

            $employee = Employee::find($id);

            wp_send_json($employee);
            wp_die();
        }else{
            wp_send_json(array(
                'status' => false,
                'message' => 'Invalid request submitted'
            ));
            wp_die();
        }
    }

    public function wptms_save_leave_request(){
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

        if(isset($_REQUEST['leave_type']) && !empty($_REQUEST['leave_type']) && isset($_REQUEST['leave_day']) && !empty($_REQUEST['leave_day'])){
            $userId = get_current_user_id();
            $leave_type = $_REQUEST['leave_type'];
            $leave_day = $_REQUEST['leave_day'];

            $overview = HolidayOverview::getHolidayByUserId($userId);

            if ($overview){
                if($leave_type === 'annual_leave'){
                    $overview->annual_leave_taken += $leave_day;
                }
                if($leave_type === 'sick_leave'){
                    $overview->sick_leave += $leave_day;
                }
            }

            if(HolidayOverview::update((array)$overview, $overview->id))
                wp_send_json(array(
                    'status' => true,
                    'message' => 'Leave Request submitted successfully'
                ));
            else
                wp_send_json(array(
                'status' => false,
                'message' => 'Something went wrong!! Please try again later'
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

    public function wptms_save_task(){
        if (!is_user_logged_in()) {
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

        $userId = get_current_user_id();
        $overview = HolidayOverview::getHolidayByUserId($userId);

        if($_REQUEST['task'] === 'bank_holiday_fd' || $_REQUEST['task'] === 'al_hf' || $_REQUEST['task'] === 'al_fd' || $_REQUEST['task'] === 'sl_hf' || $_REQUEST['task'] === 'sl_fd') {
            if ($_REQUEST['task'] === 'bank_holiday_fd') {
                $_REQUEST['hour'] = '8:00';
                $_REQUEST['task_details'] = 'Bank Holiday';
                $overview->bank_holiday += 1;
            } else if ($_REQUEST['task'] === 'al_hf') {
                $_REQUEST['hour'] = '4:00';
                $_REQUEST['task_details'] = 'Annual Leave Half Day';
                $overview->annual_leave_taken += .5;
            } else if ($_REQUEST['task'] === 'al_fd') {
                $_REQUEST['hour'] = '8:00';
                $_REQUEST['task_details'] = 'Annual Leave Full Day';
                $overview->annual_leave_taken += 1;
            } else if ($_REQUEST['task'] === 'sl_hf') {
                $_REQUEST['hour'] = '4:00';
                $_REQUEST['task_details'] = 'Sick Leave Half Day';
                $overview->sick_leave += 0.5;
            }else if ($_REQUEST['task'] === 'sl_fd') {
                $_REQUEST['hour'] = '8:00';
                $_REQUEST['task_details'] = 'Sick Leave Full Day';
                $overview->sick_leave += 1;
            }

            HolidayOverview::update((array)$overview, $overview->id);
        }


        $data = array(
            'user_id' => $userId,
            'day_date' => $_REQUEST['date'],
            'spend_hour' => $_REQUEST['hour'],
            'task' =>  $_REQUEST['task'],
            'task_details' =>  $_REQUEST['task_details']
        );

        $row = TMS::store($data);

        if($row)
            wp_send_json(array(
                'status' => true,
                'message' => "Data inserted successfully",
                'row' => $row,
                'overview' => $overview
            ));
        else
            wp_send_json(array(
                'status' => false,
                'message' => 'Something went wrong!! Please try again later'
            ));
        wp_die();
    }

    public function wptms_update_task(){
        if (!is_user_logged_in()) {
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

        $userId = get_current_user_id();
        $row = TMS::find($_REQUEST['id']);
        $overview = HolidayOverview::getHolidayByUserId($userId);

        if(isset($row->task) && isset($_REQUEST['task']) && ($row->task !== $_REQUEST['task'])){
            if ($row->task === 'bank_holiday') {
                $overview->bank_holiday -= 1;
            } else if ($row->task === 'al_hf') {
                $overview->annual_leave_taken -= .5;
            } else if ($row->task === 'al_fd') {
                $overview->annual_leave_taken -= 1;
            } else if ($row->task === 'sl') {
                $overview->sick_leave -= 1;
            }
        }

        if($_REQUEST['task'] === 'bank_holiday' || $_REQUEST['task'] === 'al_hf' || $_REQUEST['task'] === 'al_fd' || $_REQUEST['task'] === 'sl') {
            if ($_REQUEST['task'] === 'bank_holiday') {
                $_REQUEST['hour'] = '8:00';
                $_REQUEST['task_details'] = 'Bank Holiday';
                $overview->bank_holiday += 1;
            } else if ($_REQUEST['task'] === 'al_hf') {
                $_REQUEST['hour'] = '4:00';
                $_REQUEST['task_details'] = 'Annual Leave Half Day';
                $overview->annual_leave_taken += .5;
            } else if ($_REQUEST['task'] === 'al_fd') {
                $_REQUEST['hour'] = '8:00';
                $_REQUEST['task_details'] = 'Annual Leave Full Day';
                $overview->annual_leave_taken += 1;
            } else if ($_REQUEST['task'] === 'sl') {
                $_REQUEST['hour'] = '8:00';
                $_REQUEST['task_details'] = 'Sick Leave';
                $overview->sick_leave += 1;
            }
        }

        HolidayOverview::update((array)$overview, $overview->id);

        $data = array(
            'day_date' => $_REQUEST['date'],
            'spend_hour' => $_REQUEST['hour'],
            'task' =>  $_REQUEST['task'],
            'task_details' =>  $_REQUEST['task_details']
        );

        $row = TMS::update($data, $_REQUEST['id']);

        if($row)
            wp_send_json(array(
                'status' => true,
                'message' => "Data updated successfully",
                'row' => $row,
                'overview' => $overview
            ));
        else
            wp_send_json(array(
                'status' => false,
                'message' => 'Something went wrong!! Please try again later'
            ));
        wp_die();
    }

    public function wptms_delete_task(){
        if (!is_user_logged_in()) {
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

        $userId = get_current_user_id();
        $row = TMS::find($_REQUEST['id']);
        $overview = HolidayOverview::getHolidayByUserId($userId);

        if ($row->task === 'bank_holiday_fd') {
            $overview->bank_holiday -= 1;
        } else if ($row->task === 'al_hf') {
            $overview->annual_leave_taken -= .5;
        } else if ($row->task === 'al_fd') {
            $overview->annual_leave_taken -= 1;
        } else if ($row->task === 'sl_fd') {
            $overview->sick_leave -= 1;
        }else if ($row->task === 'sl_hf') {
            $overview->sick_leave -= 0.5;
        }
        HolidayOverview::update((array)$overview, $overview->id);

        $row = TMS::destroy($_REQUEST['id']);

        if($row)
            wp_send_json(array(
                'status' => true,
                'message' => "Data deleted successfully",
                'row' => $row,
                'overview' => $overview
            ));
        else
            wp_send_json(array(
                'status' => false,
                'message' => 'Something went wrong!! Please try again later'
            ));
        wp_die();
    }
}