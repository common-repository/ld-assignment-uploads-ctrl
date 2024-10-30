<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

/**
 * LDaucFileUpoladSettingsPage - adds File Upload options page under LearnDash Admin Menu
 *
 */
class LDaucFileUploadSettingsPage {

	/**
	 * A reference the class responsible for rendering the submenu page.
	 *
	 */
	private $submenu_page;

	/**
	 * LDaucFileUploadSettingsPage constructor.
	 * @param $submenu_page
	 */
	public function __construct ( $submenu_page ) {
		$this -> submenu_page = $submenu_page;
	}

	/**
	 * Adds a submenu for this plugin to the 'Tools' menu.
	 */
	public function init () {
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'admin_init', array( $this, 'settings_page_init' ) );
	}

	/**
	 * Creates the submenu item and calls on the Submenu Page object to render
	 * the actual contents of the page.
	 */
	public function add_options_page () {
		add_submenu_page( 'learndash-lms', // add under Learn Dash parent
			__( 'LearnDash Assignment Uploads Control Settins', 'ldauc' ), __( 'LDAUC Settings', 'ldauc' ), 'manage_options', 'learndash-lms/file-upload-settings', array( $this -> submenu_page, 'display' ) );
	}

	/**
	 * Register settings, add sections and fields to settings page
	 */
	public function settings_page_init () {

		// register setting API
		register_setting( 'ld_file_upload_settings', 'ld_file_upload_settings' );

		// section
		add_settings_section( 'ld_file_upload_section', '', array( $this, 'ld_file_upload_section_callback' ), 'learndash-lms/file-upload-settings' );

		// fields
		add_settings_field( 'allowed_file_types', 'Allowed file types <br />( comma separated, if empty - no limit )', array( $this, 'allowed_file_types_callback' ), 'learndash-lms/file-upload-settings', 'ld_file_upload_section' );

		add_settings_field( 'allowed_file_size', 'Allowed file size in MB <br />( if empty - no limit )', array( $this, 'allowed_file_size_callback' ), 'learndash-lms/file-upload-settings', 'ld_file_upload_section' );

		add_settings_field( 'max_number_of_uploads', 'Max Number of Uploads per student <br />( if empty - no limit )', array( $this, 'max_number_of_uploads_callback' ), 'learndash-lms/file-upload-settings', 'ld_file_upload_section' );

	}

	/**
	 * Section callback
	 */
	public function ld_file_upload_section_callback () {
		echo __( 'Shortcode to be used in LearnDash Lessons/Topics template: [ld-markcomplete] ', 'ldauc' );
	}

	/**
	 * Field callback - allowed_file_types_callback
	 */
	public function allowed_file_types_callback () {
		$settings = get_option( 'ld_file_upload_settings' );
		$allowed_file_types = $settings[ 'allowed_file_types' ];
		if ( !$allowed_file_types ) {
			$allowed_file_types = '';
		}
		echo '<input style="width:450px;" type="text" name="ld_file_upload_settings[allowed_file_types]" value="' . $allowed_file_types . '" /><br />';
	}

	/**
	 * Field callback - allowed_file_size_callback
	 */
	public function allowed_file_size_callback () {
		$settings = get_option( 'ld_file_upload_settings' );
		$allowed_file_size = $settings[ 'allowed_file_size' ];
		if ( !$allowed_file_size ) {
			$allowed_file_size = '';
		}
		echo '<input style="width:50px;" type="text" name="ld_file_upload_settings[allowed_file_size]" value="' . $allowed_file_size . '" /> MB<br />';
	}

	/**
	 * Field callback - max_number_of_uploads_callback
	 */
	public function max_number_of_uploads_callback () {
		$settings = get_option( 'ld_file_upload_settings' );
		$max_number_of_uploads = $settings[ 'max_number_of_uploads' ];
		if ( !$max_number_of_uploads ) {
			$max_number_of_uploads = '';
		}
		echo '<input style="width:50px;" type="text" name="ld_file_upload_settings[max_number_of_uploads]" value="' . $max_number_of_uploads . '" /><br />';
	}
}
