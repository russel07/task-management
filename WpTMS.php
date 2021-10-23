<?php
/**
 * Plugin Name
 *
 * @package WpTMS
 * @author Md. Russel Hussain
 * @copyright 2021 rus.org
 * @license GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: WpTMS
 * Plugin URI: https://russel.authlab/plugins
 * Description: Time and leave management for employee
 * Version: 1.0.0
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * Author: Md. Russel Hussain
 * Author URI: https://russel.authlab
 * Author URI: https://author.example.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI: https://example.com/my-plugin/
 * Text Domain: my-basics-plugin
 * Domain Path: /languages
 */
require 'vendor/autoload.php';

const PUBLIC_PLUGIN_PATH = __FILE__;
const wptms_project_table = 'wptms_projects';
const wptms_tasks_table = 'wptms_tasks';
const wptms_project_in_users_table = 'wptms_project_in_users';
const wptms_project_tasks_table = 'wptms_tasks_in_projects';
const wptms_holiday_overview_table = 'holiday_overview';
const wwptms_weekly_tasks_table = 'weekly_tasks';
const NONCE = 'WPTMS123';

use Russel\WpTms\PluginInit;

$wptms = new PluginInit();
$wptms->run();
