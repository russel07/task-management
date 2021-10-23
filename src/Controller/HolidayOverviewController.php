<?php

namespace Russel\WpTms\Controller;

use Russel\WpTms\Model\HolidayOverview;
use Russel\WpTms\Model\ProjectInUser;
use Russel\WpTms\Model\Tasks;
use Russel\WpTms\Model\TMS;
use Russel\WpTms\Model\User;
use Russel\WpTms\Helper\appHelper;
use \DateTime;

class HolidayOverviewController
{
    protected $userModel;
    protected $ProjectInUser;

    public function __construct()
    {
        $this->userModel = new User();
        $this->ProjectInUser = new ProjectInUser();
    }

    public function wptms_get_user_list(){

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
        $users = User::getUserListForAllocateHoliday();

        wp_send_json($users);
        wp_die();
    }

    public function wptms_get_active_users(){

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
        $users = $this->userModel->findAll(true);

        wp_send_json($users);
        wp_die();
    }

    public function wptms_get_holiday_list()
    {
        if (!is_user_logged_in()) {
            wp_send_json(array(
                'status' => false,
                'message' => 'Need to login to submit request'
            ));
            wp_die();

        }
        $employees = HolidayOverview::getAll();

        wp_send_json($employees);
        wp_die();
    }

    public function wptms_delete_holiday(){

        if (!is_user_logged_in()) {
            wp_send_json(array(
                'status' => false,
                'message' => 'Need to login to submit request'
            ));
            wp_die();
        }

        if(isset($_REQUEST['id']) && $_REQUEST['id']){
            $id = $_REQUEST['id'];

            if(HolidayOverview::destroy($id)) {
                wp_send_json(array(
                    'status' => true,
                    'message' => 'Item deleted successfully'
                ));
                wp_die();
            }
            else {
                wp_send_json(array(
                    'status' => false,
                    'message' => 'Something went wrong!! Please try again later'
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

    public function wptms_insert_holiday(){
        if(!is_user_logged_in()) {
            wp_send_json(array(
                'status' => false,
                'message' => 'Need to login to submit request'
            ));
            wp_die();
        }

        if(isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id']) && isset($_REQUEST['annual_leave_allocated']) && !empty($_REQUEST['annual_leave_allocated'])) {

            $data = array(
                'user_id' => $_REQUEST['user_id'],
                'annual_leave_allocated' => $_REQUEST['annual_leave_allocated']
            );
            if(HolidayOverview::store($data)){
                wp_send_json(array(
                    'status' => true,
                    'message' => 'Holiday submitted successfully'
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

    public function prepare_weekly_data(){
        $currentWeek = (isset($_GET['week']) && !empty($_GET['week'])) ? $_GET['week']: '';
        $currentYear = (isset($_GET['year']) && !empty($_GET['year'])) ? $_GET['year']: '';
        $userId = (isset($_GET['userId']) && !empty($_GET['userId'])) ? $_GET['userId']: '';

        if($userId == '')
            $userId = get_current_user_id();

        if($currentWeek == '' && $currentYear == ''){
            $monday = strtotime('monday this week');
            $currentWeek = date('W', $monday);
            $currentYear = date('Y', $monday);
        }
        $nextWeek = $currentWeek + 1;
        $prevWeek = $currentWeek - 1;

        if($nextWeek > 52){
            $nextWeek = 1;
            $nextYear = $currentYear + 1;
        }else{
            $nextYear = $currentYear;
        }

        if($prevWeek < 1){
            $prevWeek = 52;
            $prevYear = $currentYear - 1;
        }else{
            $prevYear = $currentYear;
        }

        $dto = new DateTime();
        $dto->setISODate($currentYear, $currentWeek);
        $weekstart = $dto->format('Y-m-d');
        $date = $dto->format('Y-m-d H:i:s');
        $dto->modify('+6 days');
        $weekend = $dto->format('Y-m-d');

        $data = array();
        $week = array();
        $hours = array();
        for($i = 0; $i < 7; $i++){
            $week[$i] = array();
            $new_date = strtotime($date . " +$i day");
            $week[$i]['date'] = date('Y-m-d', $new_date);
            $hours[$week[$i]['date']] = '0:00';
            $week[$i]['day'] = date('l', $new_date);
        }

        $data['week'] = $week;
        $tasks = array();
        $list = TMS::getTaskListForWeek($weekstart, $weekend);
        $projects = $this->ProjectInUser->getProjectCodeByUser($userId);
        $taskList = Tasks::taskNotIn((array)$list, (array)$projects);

        foreach ($list as $key => $l){
            $tasks[$key]['task_name'] = $l->task;
            $tasks[$key]['weekly_tasks'] = array();
            $taskDetails = TMS::getTaskDetailsForWeek($weekstart, $weekend, $l->task);

            foreach ($taskDetails as $details){

                foreach ($week as $date){
                    if($details->day_date == $date['date']){
                        $tasks[$key]['weekly_tasks'][$details->day_date] = array(
                            'hours' => $details->spend_hour,
                            'task_details' => $details->task_details,
                            'task_id' => $details->id,
                        );
                        $hours[$details->day_date] = appHelper::sum_the_time($hours[$details->day_date], $details->spend_hour);
                    }else{
                        if(empty($tasks[$key]['weekly_tasks'][$date['date']]))
                            $tasks[$key]['weekly_tasks'][$date['date']] = array(
                                'hours' => '',
                                'task_details' => '',
                            );
                    }
                }
            }
        }

        $data['hours'] = $hours;
        $data['tasks'] = $tasks;
        $data['taskList'] = $taskList;
        $data['nextWeek'] = $nextWeek;
        $data['nextYear'] = $nextYear;
        $data['prevWeek'] = $prevWeek;
        $data['prevYear'] = $prevYear;

        return $data;
    }
}