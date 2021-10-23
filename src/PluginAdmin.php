<?php

namespace Russel\WpTms;

class PluginAdmin
{
    protected $plugin_name;
    protected $plugin_version;
    protected $views;

    public function __construct($plugin_name, $plugin_version)
    {
        $this->plugin_name = $plugin_name;
        $this->plugin_version = $plugin_version;

        $this->views = array();
    }

    public function load_view(){
        $current_view = $this->views[current_filter()];

        include_once(dirname(PUBLIC_PLUGIN_PATH).'/admin/view/'.$current_view.'.php');
    }

    public function wptms_admin_menu(){
        $admin_menu_hook = add_menu_page(
            'Manage Holiday',
            'Holiday',
            'manage_options',
            'manage-holiday',
            array(&$this, 'load_view')
        );

        add_submenu_page(
            'manage-holiday',
        'Manage Project',
        'Manage Projects',
        'manage_options',
        'manage-holiday#/projects',
        array(&$this, 'load_view'));

        add_submenu_page(
            'manage-holiday',
            'Manage Tasks',
            'Manage Tasks',
            'manage_options',
            'manage-holiday#/tasks',
            array(&$this, 'load_view'));

        add_submenu_page(
            'manage-holiday',
            'Allocate Tasks to Projects',
            'Task Allocation',
            'manage_options',
            'manage-holiday#/allocate-tasks',
            array(&$this, 'load_view'));

        add_submenu_page(
            'manage-holiday',
            'Allocate Project to User',
            'Project Allocation',
            'manage_options',
            'manage-holiday#/allocate-project',
            array(&$this, 'load_view'));

        $this->views[$admin_menu_hook] = 'employee';
    }

    public function wptms_enqueue_style(){
        wp_enqueue_style('wptms_css', plugin_dir_url(PUBLIC_PLUGIN_PATH).'assets/css/style.css');
    }

    public function wptms_enqueue_scripts(){
        wp_register_script($this->plugin_name.'_VUE', plugin_dir_url( PUBLIC_PLUGIN_PATH ) . 'assets/js/main.js', array(), $this->plugin_version, true );

        wp_enqueue_script( $this->plugin_name.'_VUE' );
        wp_localize_script( $this->plugin_name.'_VUE', 'ajax_object', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce(NONCE),
            'plugin_assets' => plugin_dir_url(PUBLIC_PLUGIN_PATH).'assets/'
        ));
    }
}