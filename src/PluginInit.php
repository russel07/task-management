<?php

namespace Russel\WpTms;

use Russel\WpTms\Controller\EmployeeController;
use Russel\WpTms\Controller\HolidayOverviewController;
use Russel\WpTms\Model\HolidayOverview;
use Russel\WpTms\Router\Router;

class PluginInit
{
    protected $loader;
    protected $name;
    protected $version;
    protected $holidayController;
    static $instance = false;

    public function __construct()
    {
        $this->name = 'WPTMS';
        $this->version = '1.0.0';
        $this->holidayController = new HolidayOverviewController();
        $this->activateMe();
        $this->deactivateMe();
        $this->wptms_add_custom_role();
        $this->loader = new PluginLoader();
        $this->wptms_load_asset();
        $this->define_admin_hooks();

        $this->wptms_shortcode();
        new Router($this->loader);
    }

    public function getInstance(){
        if(!self::$instance)
            self::$instance = new self();

        return self::$instance;
    }

    public function activateMe(){
        register_activation_hook(PUBLIC_PLUGIN_PATH, array($this, 'wptms_install_required_table'));
    }

    public function deactivateMe(){
        register_deactivation_hook(PUBLIC_PLUGIN_PATH, array($this, 'wptms_uninstall_all_tables'));
    }

    public function wptms_shortcode(){
        add_shortcode( 'WPTMS_SHORTCODE', array($this, 'wptms_generate_shortcode') );
    }

    public function wptms_install_required_table(){
        global $wpdb;
        global $wptms_project_table_version;
        global $wptms_tasks_table_version;
        global $wptms_project_tasks_table_version;
        global $wptms_holiday_overview_table_version;
        global $wwptms_weekly_tasks_table_version;
        global $wptms_project_in_users_table_version;

        $wptms_project_table_version = 1.0;
        $wptms_tasks_table_version = 1.0;
        $wptms_project_tasks_table_version = 1.0;
        $wptms_holiday_overview_table_version = 1.0;
        $wwptms_weekly_tasks_table_version = 1.0;
        $wptms_project_in_users_table_version = 1.0;

        $wptms_project_table = $wpdb->prefix.wptms_project_table;
        $wptms_tasks_table = $wpdb->prefix.wptms_tasks_table;
        $wptms_tasks_in_project_table = $wpdb->prefix.wptms_project_tasks_table;
        $holiday_table = $wpdb->prefix.wptms_holiday_overview_table;
        $timesheet = $wpdb->prefix.wwptms_weekly_tasks_table;
        $project_in_user = $wpdb->prefix.wptms_project_in_users_table;


        $pwptms_project_table_sql ="CREATE TABLE  IF NOT EXISTS " . $wptms_project_table . " (
        id int(11) NOT NULL AUTO_INCREMENT,
        project_name VARCHAR(200) NOT NULL,
        project_code VARCHAR(100) NOT NULL,
        status ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
        PRIMARY KEY  (id)
        );";

        $project_in_user_sql ="CREATE TABLE  IF NOT EXISTS " . $project_in_user . " (
        id int(11) NOT NULL AUTO_INCREMENT,
        user_id VARCHAR(200) NOT NULL,
        project_code VARCHAR(100) NOT NULL,
        status ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
        PRIMARY KEY  (id)
        );";

        $wptms_tasks_table_sql ="CREATE TABLE  IF NOT EXISTS " . $wptms_tasks_table . " (
        id int(11) NOT NULL AUTO_INCREMENT,
        task_name VARCHAR(100) NOT NULL,
        task_code VARCHAR(50) NOT NULL,
        status ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
        PRIMARY KEY  (id)
        );";

        $wptms_tasks_in_project_table_sql ="CREATE TABLE  IF NOT EXISTS " . $wptms_tasks_in_project_table . " (
        id int(11) NOT NULL AUTO_INCREMENT,
        project_code VARCHAR(100) NOT NULL,
        tasks_code VARCHAR(100) NOT NULL,
        task_code VARCHAR(100) NOT NULL,
        task_details VARCHAR(200) NOT NULL,
        status ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
        PRIMARY KEY  (id)
        );";

        $overview_table = "CREATE TABLE  IF NOT EXISTS " . $holiday_table . " (
        id int(11) NOT NULL AUTO_INCREMENT,
        user_id INT(20) NOT NULL,
        bank_holiday decimal(10,1) NOT NULL DEFAULT 0,
        annual_leave_allocated decimal(10,1) NOT NULL DEFAULT 20,
        annual_leave_taken decimal(10,1) NOT NULL DEFAULT 0,
        sick_leave decimal(10,1) NOT NULL DEFAULT 0,
        PRIMARY KEY  (id)
        );";

        $tms_table ="CREATE TABLE  IF NOT EXISTS " . $timesheet . " (
        id int(11) NOT NULL AUTO_INCREMENT,
        user_id INT(20) NOT NULL,
        day_date DATE NOT NULL,
        task VARCHAR(100) NOT NULL,
        task_details VARCHAR(500) NOT NULL,
        spend_hour TIME NOT NULL,
        PRIMARY KEY  (id)
        );";

        require_once(ABSPATH.'wp-admin/includes/upgrade.php');

        dbDelta($pwptms_project_table_sql);//Create wp_projects table
        dbDelta($project_in_user_sql);//Create wptms_project_in_users table
        dbDelta($wptms_tasks_table_sql);//Create wp_tasks table
        dbDelta($wptms_tasks_in_project_table_sql);//Create wp_task_in_projects table
        dbDelta($overview_table);//Create wp_holiday_overview table
        dbDelta($tms_table);//Create wp_weekly_tasks table

        add_option('wptms_project_table_version', $wptms_project_table_version);
        add_option('wptms_tasks_table_version', $wptms_tasks_table_version);
        add_option('wptms_tasks_in_projects_table_version', $wptms_project_tasks_table_version);
        add_option('wptms_holiday_overview_version', $wptms_holiday_overview_table_version);
        add_option('wptms_weekly_tasks_table_version', $wwptms_weekly_tasks_table_version);
        add_option('wptms_project_in_users_table_version', $wptms_project_in_users_table_version);

        $this->projectSeed();
        $this->taskSeed();
    }

    public function wptms_uninstall_all_tables(){
        global $wpdb;

        $pwptms_project_table = $wpdb->prefix.wptms_project_table;
        $wptms_tasks_table = $wpdb->prefix.wptms_tasks_table;
        $wptms_tasks_in_project_table = $wpdb->prefix.wptms_project_tasks_table;
        $holiday_table = $wpdb->prefix.wptms_holiday_overview_table;
        $timesheet = $wpdb->prefix.wwptms_weekly_tasks_table;
        $projectInUser = $wpdb->prefix.wptms_project_in_users_table;

        $sql = "DROP TABLE IF EXISTS $pwptms_project_table";
        $sql2 = "DROP TABLE IF EXISTS $wptms_tasks_table";
        $sql3 = "DROP TABLE IF EXISTS $wptms_tasks_in_project_table";
        $sql4 = "DROP TABLE IF EXISTS $holiday_table";
        $sql5 = "DROP TABLE IF EXISTS $timesheet";
        $sql6 = "DROP TABLE IF EXISTS $projectInUser";

        $wpdb->query($sql);
        $wpdb->query($sql2);
        $wpdb->query($sql3);
        $wpdb->query($sql4);
        $wpdb->query($sql5);
        $wpdb->query($sql6);
        delete_option("wptms_project_table_version");
        delete_option("wptms_tasks_table_version");
        delete_option("wptms_tasks_in_projects_table_version");
        delete_option("wptms_holiday_overview_version");
        delete_option("wptms_weekly_tasks_table_version");
        delete_option("wptms_project_in_users_table_version");
    }

    public function wptms_add_custom_role(){
        add_role(
            'standard_user', __( 'Standard User' ), array(
                'read' => true,
                'edit_posts'  => false,
                'delete_posts'  => false,
            )
        );
    }

    public function define_admin_hooks(){
        $admin = new PluginAdmin($this->name, $this->version);

        $this->loader->add_action('admin_menu', $admin, 'wptms_admin_menu');
        $this->loader->add_action('admin_init', $admin, 'wptms_enqueue_style');
        $this->loader->add_action('admin_init', $admin, 'wptms_enqueue_scripts');
    }

    public function wptms_load_public_style(){
        wp_enqueue_style( 'WPTMS_bootstrap_css', plugin_dir_url( PUBLIC_PLUGIN_PATH ) . 'assets/css/bootstrap.css');
        wp_enqueue_style( 'WPTMS_notify_css', plugin_dir_url( PUBLIC_PLUGIN_PATH ) . 'assets/css/sweetalert2.min.css');
    }

    public function wptms_load_public_script(){
        wp_register_script($this->name.'_notify_js', plugin_dir_url( PUBLIC_PLUGIN_PATH ) . 'assets/js/sweetalert2.all.min.js', array(), $this->version, true );
        wp_register_script($this->name.'_plugin_js', plugin_dir_url( PUBLIC_PLUGIN_PATH ) . 'assets/js/plugin-user.js', array(), $this->version, true );



        wp_enqueue_script( $this->name.'_notify_js' );
        wp_enqueue_script( $this->name.'_plugin_js' );
        wp_localize_script( $this->name.'_plugin_js', 'ajax_object', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce(NONCE),
            'plugin_assets' => plugin_dir_url(PUBLIC_PLUGIN_PATH).'assets/'
        ));
    }


    public function wptms_load_public_jquery()
    {
        wp_register_script($this->name.'_jquery_js', plugin_dir_url( PUBLIC_PLUGIN_PATH ) . 'assets/js/jquery.min.js', array(), $this->version, true );

        if(!wp_script_is('jquery', 'enqueued')){
            wp_enqueue_script( $this->name.'_jquery_js' );
        }
    }

    public function wptms_load_asset(){
        $this->loader->add_action('init', $this, 'wptms_load_public_style');
        $this->loader->add_action('init', $this, 'wptms_load_public_script');
        $this->loader->add_action('wp_enqueue_scripts', $this, 'wptms_load_public_jquery');
    }

    public function wptms_generate_shortcode($atts = [], $content = null, $tag = ''){
        $atts = array_change_key_case( (array) $atts, CASE_LOWER );

        if (isset($atts['holiday_overview']) && !empty($atts['holiday_overview'])){
            return $this->get_holiday_overview();
        }

        if (isset($atts['weekly_timesheet']) && !empty($atts['weekly_timesheet'])){
            $data = $this->holidayController->prepare_weekly_data();

            return $this->loader->view('weekly_sheet', $data);
        }

        if (isset($atts['task_form']) && !empty($atts['task_form'])){
            return $this->loader->view('leave_request');
        }
        return '<h1>Hello</h1>';
    }

    public function get_holiday_overview(){
        $userId = get_current_user_id();
        $overview = HolidayOverview::getHolidayByUserId($userId);

        return $this->loader->view('holiday_overview', $overview);
    }

    public function projectSeed(){
        global $wpdb;
        $wptms_project_table = $wpdb->prefix.wptms_project_table;

        $wpdb->insert($wptms_project_table, array(
            'project_name' => 'Bank Holiday',
            'project_code' => 'bank_holiday',
            'status' => 'Active'
        ));

        $wpdb->insert($wptms_project_table, array(
            'project_name' => 'Annual Leave',
            'project_code' => 'al',
            'status' => 'Active'
        ));

        $wpdb->insert($wptms_project_table, array(
            'project_name' => 'Sick Leave',
            'project_code' => 'sl',
            'status' => 'Active'
        ));
    }

    public function taskSeed(){
        global $wpdb;
        $wptms_task_table = $wpdb->prefix. wptms_tasks_table;
        $wpdb->insert($wptms_task_table, array(
            'task_code' => 'fd',
            'task_name' => 'Full Day',
            'status' => 'Active'
        ));

        $wpdb->insert($wptms_task_table, array(
            'task_code' => 'hf',
            'task_name' => 'Half Day',
            'status' => 'Active'
        ));
    }

    public function run(){
        $this->loader->run();
    }
}