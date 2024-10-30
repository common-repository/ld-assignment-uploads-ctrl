<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

if ( !class_exists( 'LDaucLoader' ) ):

	/**
	 * Class LDaucLoader
	 */
	class LDaucLoader {

		protected $hooks;

		/**
		 * empty constructor function to ensure a single instance
		 */
		public function __construct () {
			// leave empty, see singleton below
		}

		public static function instance () {
			static $instance = null;
			if ( null === $instance ) {
				$instance = new LDaucLoader;
				$instance -> setup();
			}

			return $instance;
		}

		/**
		 * setup all
		 */
		public function setup () {

			$this -> load_dependencies();
			$this -> hooks = new LDaucHooks();
			$this -> define_admin_hooks();
			$this -> define_public_hooks();
			$this -> run();

		}

		/**
		 * Load the required
		 */
		private function load_dependencies () {

			require_once LDAUC_PLUGIN_DIR . 'helpers/DbHelper.php';
			require_once LDAUC_PLUGIN_DIR . 'inc/LDaucFunctions.php';
			require_once LDAUC_PLUGIN_DIR . 'inc/LDaucHooks.php';
		}

		/**
		 * Register all of the hooks related to the admin area functionality
		 * of the plugin.
		 *
		 */
		private function define_admin_hooks () {

			$plugin_functions = LDaucFunctions ::instance();

			$actions_to_add = [ // admin scripts
				'admin_enqueue_scripts' => [ [ $plugin_functions, 'ldauc_admin_enqueue_styles' ], [ $plugin_functions, 'ldauc_admin_enqueue_scripts' ] ],

			];

			$filters_to_add = [];

			$this -> hooks -> actions_to_add( $actions_to_add );
			$this -> hooks -> filters_to_add( $filters_to_add );
		}

		/**
		 * Register all of the hooks related to the public-facing functionality
		 * of the plugin and the widget.
		 *
		 */
		private function define_public_hooks () {

			$plugin_functions = LDaucFunctions ::instance();

			$actions_to_add = [

				// wp scripts
				'wp_enqueue_scripts' => [ [ $plugin_functions, 'ldauc_enqueue_styles' ], [ $plugin_functions, 'ldauc_enqueue_scripts' ] ],

				//add_action( 'learndash_assignment_uploaded', 'ldauc_learndash_assignment_uploaded' );
				'learndash_assignment_uploaded' => [ [ $plugin_functions, 'ldauc_learndash_assignment_uploaded' ] ],

				// add_action( 'init', 'register_shortcodes');
				'init' => [ [ $plugin_functions, 'register_shortcodes' ] ],

			];

			$filters_to_add = [];

			$this -> hooks -> actions_to_add( $actions_to_add );
			$this -> hooks -> filters_to_add( $filters_to_add );
		}

		/**
		 * Run to execute all of the hooks with WordPress.
		 *
		 */
		public function run () {
			$this -> hooks -> run();
		}
	}

	LDaucLoader ::instance();

endif;
