<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

if ( !class_exists( 'LDaucMainClass' ) ):

	/**
	 *  Plugin  Main Class
	 *
	 */
	class LDaucMainClass {

		private $includes = [ 'LDaucLoader' ];
		private $admin_includes = [ 'LDaucFileUploadSettingsPage' ];
		private $partials = [ 'LDaucFileUploadSettingsAdminView' ];
		private $helpers = [ 'GetTemplateHelper', 'DbHelper' ];
		public $functions;
		public $plugin_dir = '';
		public $includes_dir = '';
		public $partials_dir = '';
		public $helpers_dir = '';

		/**
		 * Main LDaucMainClass Instance.
		 *
		 */
		public static function instance () {

			// Store the instance locally to avoid private static replication
			static $instance = null;

			// Only run these methods if they haven't been run previously
			if ( null === $instance ) {
				$instance = new LDaucMainClass;
				$instance -> init();
			}

			// Always return the instance
			return $instance;
		}

		private function __construct () { /* Do nothing here */
		}

		/**
		 * Init.
		 *
		 */
		private function init () {

			$this -> plugin_dir = LDAUC_PLUGIN_DIR;

			// Includes
			$this -> includes_dir = $this -> plugin_dir . 'inc';

			// Partials
			$this -> partials_dir = $this -> plugin_dir . 'partials';

			// Helpers
			$this -> helpers_dir = $this -> plugin_dir . 'helpers';

			// Load all neccessary
			$this -> functions = $this -> load_functions();
			$this -> load_main();
			$this -> load_file_upload_admin();
			$this -> load_helpers();
		}

		/**
		 * Include required File Upload Settings admin files.
		 *
		 */
		public function load_file_upload_admin () {

			$this -> do_includes( $this -> admin_includes );
			$this -> do_include_partials( $this -> partials );

			// LearnDash File Uploads Options page - file types, size, max # of uploads ...
			$admin_view = new LDaucFileUploadSettingsAdminView( $this -> functions );
			$admin = new LDaucFileUploadSettingsPage( $admin_view );

			return $admin -> init();
		}

		/**
		 * Include required files.
		 *
		 */
		public function load_main () {
			$this -> do_includes( $this -> includes );

			return LDaucLoader ::instance();
		}

		/**
		 * Load Functions.
		 *
		 */
		public function load_functions () {
			require_once( $this -> includes_dir . '/LDaucFunctions.php' );

			return LDaucFunctions ::instance();
		}

		/**
		 * Include helpers.
		 *
		 */
		public function load_helpers () {
			$this -> do_include_helpers( $this -> helpers );
		}

		/**
		 * Include required array of files in the includes directory
		 * @param array $includes
		 */
		public function do_includes ( $includes = [] ) {
			foreach ( $includes as $include ) {
				require_once( $this -> includes_dir . '/' . $include . '.php' );
			}
		}

		/**
		 * Include required array of files in the partials directory
		 * @param array $partials
		 */
		public function do_include_partials ( $partials = [] ) {
			foreach ( $partials as $partial ) {
				require_once( $this -> partials_dir . '/' . $partial . '.php' );
			}
		}

		/**
		 * Include required array of files in the helpers directory
		 * @param array $helpers
		 */
		public function do_include_helpers ( $helpers = [] ) {
			foreach ( $helpers as $helper ) {
				require_once( $this -> helpers_dir . '/' . $helper . '.php' );
			}
		}
	}
endif;
