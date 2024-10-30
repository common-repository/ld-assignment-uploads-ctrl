<?php
/*
 * Plugin Name: LearnDash Assignment Uploads Control
 * Description: Allows Control of Students uploads. Define max number of uploads, max file size and allowed file types. 
 * Author: Slobodan Brbaklic
 * Author Email: brbaso@gmail.com
 * Author URI: https://github.com/brbaso/
 * Version:     1.0.2
 * Text Domain: ldauc
 * Domain Path: /languages/
 * License:   GPLv2 or later
 *
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

/**
 * ========================================================================
 * CONSTANTS
 * ========================================================================
 */

// Directory
if ( !defined( 'LDAUC_PLUGIN_DIR' ) ) {
	define( 'LDAUC_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

// Url
if ( !defined( 'LDAUC_PLUGIN_URL' ) ) {
	$plugin_url = plugin_dir_url( __FILE__ );

	// If we're using https, update the protocol. Workaround for WP13941, WP15928, WP19037.
	if ( is_ssl() )
		$plugin_url = str_replace( 'http://', 'https://', $plugin_url );
	define( 'LDAUC_PLUGIN_URL', $plugin_url );
}

// File
if ( !defined( 'LDAUC_PLUGIN_FILE' ) ) {
	define( 'LDAUC_PLUGIN_FILE', __FILE__ );
}

// Directory
if ( !defined( 'LDAUC_PLUGIN_DIR_NAME' ) ) {
	define( 'LDAUC_PLUGIN_DIR_NAME', 'ld-assignment-uploads-ctrl' );
}

/**
 * Main
 *
 * @return void
 */
function LDAUC_init () {
	global $LDAUC;

	//Check Learndash Plugin install and active
	if ( !class_exists( 'SFWD_LMS' ) ) {
		add_action( 'admin_notices', 'ldauc_install_notice' );

		return;
	}

	// load text domain
	load_plugin_textdomain( 'ldauc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	$main_include = LDAUC_PLUGIN_DIR . 'inc/LDaucMainClass.php';

	try {
		if ( file_exists( $main_include ) ) {
			require( $main_include );
		} else {
			$msg = sprintf( __( "Couldn't load main class at:<br/>%s", 'ldauc' ), $main_include );
			throw new Exception( $msg, 404 );
		}
	} catch ( Exception $e ) {
		$msg = sprintf( __( "<h1>Fatal error:</h1><hr/><pre>%s</pre>", 'ldauc' ), $e -> getMessage() );
		echo $msg;
	}

	$LDAUC = LDaucMainClass ::instance();

}

add_action( 'plugins_loaded', 'LDAUC_init' );

/**
 * Must be called after hook 'plugins_loaded'
 *
 * useful - throughout the site e.g. functions can be called like: ldauc_custom() -> functions -> some_function_defined_in_LDaucFunctions_class() ....
 */

function ldauc_custom () {
	global $LDAUC;

	return $LDAUC;
}

/**
 * Show the admin notice to install/activate LearnDash and BuddyPress first
 */
function ldauc_install_notice () {
	echo '<div id="message" class="error fade"><p style="line-height: 150%">';
	_e( '<strong>Learn dash upload control </strong> requires the LearnDash plugin to work!', 'ldauc' );
	echo '</p></div>';
}
