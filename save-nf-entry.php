<?php
/*
Plugin Name: Save Ninja Forms Entry 
Plugin URI: https://wordpress.org/
Description: This is a plugin that saves ninja forms entries into the database
Author: Noman Akram
Text Domain: save-nf-entry
Version: 1.0.0
*/

define( 'SNFE_VERSION', '1.0.0' );

define( 'SNFE_PLUGIN_PATH', dirname( __FILE__ ) );

define( 'SNFE_PLUGIN_BASENAME',  basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );

define( 'SNFE_PLUGIN_URL', plugins_url( '', SNFE_PLUGIN_BASENAME ) );

define( 'SNFE_CONTROLLER_PATH',   SNFE_PLUGIN_PATH  . DIRECTORY_SEPARATOR . 'controller' );

define( 'SNFE_LIB_PATH', SNFE_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'lib' );

require_once SNFE_CONTROLLER_PATH . DIRECTORY_SEPARATOR . 'SNFEMainController.php';
require_once SNFE_CONTROLLER_PATH . DIRECTORY_SEPARATOR . 'SNFEContactController.php';

$main_controller = new SNFEMainController();