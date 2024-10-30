<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

if ( !class_exists( 'LDaucFunctions' ) ):

	/**
	 *
	 * LDaucFunctions class, wraps all functions needed
	 *
	 */
	class LDaucFunctions {

		public $tmp = null;
		private $dbhandle = null;

		/**
		 * empty constructor function to ensure a single instance
		 */
		public function __construct () {
			// leave empty, see singleton below
		}

		public static function instance () {

			static $instance = null;

			if ( null === $instance ) {
				$instance = new LDaucFunctions;
				$instance -> setup();
			}

			return $instance;
		}

		/**
		 * setup all
		 */
		public function setup () {

			$this -> load_dependencies();
			$this -> dbhandle = new DbHelper;
			$this -> tmp = new GetTemplateHelper( LDAUC_PLUGIN_DIR_NAME, LDAUC_PLUGIN_DIR, LDAUC_PLUGIN_URL );
		}

		/**
		 * Load the required
		 */
		public function load_dependencies () {
			require_once LDAUC_PLUGIN_DIR . 'helpers/DbHelper.php';
			require_once LDAUC_PLUGIN_DIR . 'helpers/GetTemplateHelper.php';
		}


		/**
		 * check assignments upload for file type, size and max allowed uploads
		 *
		 * @param $assignment_post_id
		 * @param string $assignment_meta
		 */
		public function ldauc_learndash_assignment_uploaded ( $assignment_post_id, $assignment_meta = '' ) {

			// get assignment meta
			if ( !$assignment_meta ) {
				$assignment_meta = get_post_meta( $assignment_post_id );
			}

			//gives -> $file_name[0], $file_link[0], $user_name[0], $disp_name[0], $file_path[0], $user_id[0], $lesson_id[0], $course_id[0], $lesson_title[0], $lesson_type[0]
			extract( $assignment_meta );

			// get File Upload options
			$ld_file_upload_settings = get_option( 'ld_file_upload_settings' );

			// gives -> $allowed_file_types, $allowed_file_size, $max_number_of_uploads
			extract( $ld_file_upload_settings );

			// find file size
			$filesize = filesize( rawurldecode( $file_path[ 0 ] ) );
			$bytes = number_format( $filesize / 1048576, 2 ) . ' MB';

			// find file extension
			$ext = pathinfo( rawurldecode( $file_path[ 0 ] ), PATHINFO_EXTENSION );
			$allowed_exts = explode( ',', $allowed_file_types );

			// find back_link	
			$type = ( $lesson_type[ 0 ] == 'sfwd-lessons' ) ? 'lessons' : 'topic';
			$slug = get_post_field( 'post_name', $lesson_id[ 0 ] );
			$back_url = site_url() . '/' . $type . '/' . $slug;
			$back_link = '<a   href="' . $back_url . '" class="back-to-lesson-link"><i class="fa fa-angle-left"></i> ' . sprintf( __( 'Back to: %s', 'ldauc' ), $lesson_title[ 0 ] ) . '</a>';

			// find all user assignements, published
			$students_assignements = $this -> ldauc_get_user_assignments_list( $user_id[ 0 ] );

			// compare all assignments for the given lesson/topic and count total
			$count_assignments = 0;
			foreach ( $students_assignements as $sa ) {
				if ( $sa -> lesson_id == $lesson_id[ 0 ] ) {
					$count_assignments++;
				}
			}

			// throw error if max number of uploads reached			
			if ( $max_number_of_uploads && $count_assignments > $max_number_of_uploads ) {

				wp_delete_post( $assignment_post_id, true );

				$title = __( 'Max number of uploads reached', 'ldauc' );
				$message = sprintf( __( 'Only %d uploads allowed', 'ldauc' ), $max_number_of_uploads );
				$args = [ 'title' => $title, 'message' => $message, 'back_link' => $back_link ];
				$this -> tmp -> get_template( 'default', $args, true, false );
			}

			// check if file extension is allowed and throw an error if not			
			if ( !empty( $allowed_file_types ) && !in_array( $ext, $allowed_exts ) ) {

				wp_delete_post( $assignment_post_id, true );

				$title = __( 'File extension not allowed', 'ldauc' );
				$message = sprintf( __( 'File extension "%s" not allowed. <br /> Allowed extensions: <span class="allowed-extensions">%s</span>', 'ldauc' ), $ext, $allowed_file_types );
				$args = [ 'title' => $title, 'message' => $message, 'back_link' => $back_link ];
				$this -> tmp -> get_template( 'default', $args, true, false );
			}

			// check for file size and throw an error if allowed size exceeded
			if ( $allowed_file_size && $bytes > $allowed_file_size ) {

				wp_delete_post( $assignment_post_id, true );

				$title = __( 'File too big', 'ldauc' );
				$message = sprintf( __( 'File size of %s exceeds %s MB allowed.', 'ldauc' ), $bytes, $allowed_file_size );
				$args = [ 'title' => $title, 'message' => $message, 'back_link' => $back_link ];
				$this -> tmp -> get_template( 'default', $args, true, false );
			}
		}

		/**
		 * override for learndash_mark_complete function - \wp-content\plugins\sfwd-lms\includes\course\ld-course-progress.php
		 *
		 * @return string
		 */
		public function ldauc_learndash_mark_complete () {
			$post = get_post();

			// get File Upload options
			$ldauc_settings = get_option( 'ld_file_upload_settings' );


			$allowed_file_types = ( $ldauc_settings[ 'allowed_file_types' ] ) ? sprintf( __( 'Allowed file types : %s', 'ldauc' ), $ldauc_settings[ 'allowed_file_types' ] ) : '';
			$allowed_file_size = ( $ldauc_settings[ 'allowed_file_size' ] ) ? sprintf( __( 'Allowed file size max: %s MB', 'ldauc' ), $ldauc_settings[ 'allowed_file_size' ] ) : '';
			$max_number_of_uploads = ( $ldauc_settings[ 'max_number_of_uploads' ] ) ? sprintf( __( 'Max number of uploads: %s', 'ldauc' ), $ldauc_settings[ 'max_number_of_uploads' ] ) : '';

			$args = [ 'post' => $post, 'allowed_file_types' => $allowed_file_types, 'allowed_file_size' => $allowed_file_size, 'max_number_of_uploads' => $max_number_of_uploads ];
			extract( $args ); // gives -> $allowed_file_types, $allowed_file_size, $max_number_of_uploads

			return lesson_hasassignments( $post ) ? $this -> tmp -> get_template( 'ldauc-mark-complete', $args, false, false ) : learndash_mark_complete( $post );
		}

		/**
		 * function to add [ld-markcomplete] shortcode, hooked to 'init'
		 *
		 * This shortcode should be used in LearnDash lesson and topic templates by replacing LearnDash original 'learndash_mark_complete' function.
		 *
		 * e.g. in your lesson.php or topic.php template, in Display Lesson Assignments section, replace line :
		 * " echo learndash_mark_complete( $post ); "
		 * with the new [ld-markcomplete] shortcode line :
		 * " echo do_shortcode("[ld-markcomplete]"); "
		 *
		 */
		public function register_shortcodes () {
			add_shortcode( 'ld-markcomplete', array( $this, 'ldauc_learndash_mark_complete' ) );
		}

		/**
		 * get assignements for a specific user
		 *
		 * @param string $user_id
		 * @param bool $published
		 * @return array
		 */
		public function ldauc_get_user_assignments_list ( $user_id = '', $published = true ) {

			$user_q = '';
			$status_q = '';

			if ( $user_id ) {
				$user_q = '&author=' . $user_id . '';
			}

			if ( $published ) {
				$status_q = '&post_status=publish';
			}

			$posts = get_posts( 'post_type=sfwd-assignment&posts_per_page=-1' . $user_q . $status_q );

			if ( !empty( $posts ) ) {
				foreach ( $posts as $key => $p ) {
					$meta = get_post_meta( $p -> ID, '', true );

					foreach ( $meta as $meta_key => $value ) {

						if ( is_string( $value ) || is_numeric( $value ) ) {
							$posts[ $key ] ->{$meta_key} = $value;
						} else if ( is_string( $value[ 0 ] ) || is_numeric( $value[ 0 ] ) ) {
							$posts[ $key ] ->{$meta_key} = $value[ 0 ];
						}

						if ( $meta_key == 'file_path' ) {
							$posts[ $key ] ->{$meta_key} = rawurldecode( $posts[ $key ] ->{$meta_key} );
						}
					}
				}
			}

			return $posts;
		}

		/**
		 * Register the stylesheets -admin
		 *
		 * @since    1.0.0
		 */
		public function ldauc_admin_enqueue_styles () {
			wp_enqueue_style( LDAUC_PLUGIN_DIR_NAME . '_admin_css', LDAUC_PLUGIN_URL . 'css/admin.css', array(), '1.0.0', 'all' );
		}

		/**
		 * Register the JavaScript - admin
		 *
		 * @since    1.0.0
		 */
		public function ldauc_admin_enqueue_scripts () {
			wp_enqueue_script( LDAUC_PLUGIN_DIR_NAME . 'admin_js', LDAUC_PLUGIN_URL . 'js/admin.js', array( 'jquery' ), '1.0.0', false );
		}

		/**
		 * Register the stylesheets - front
		 *
		 * @since    1.0.0
		 */
		public function ldauc_enqueue_styles () {
			global $wp_styles;

			$srcs = array_map( 'basename', (array) wp_list_pluck( $wp_styles -> registered, 'src' ) );
			if ( in_array( 'font-awesome.css', $srcs ) || in_array( 'font-awesome.min.css', $srcs ) ) {
				// font-awesome.css already registered - do nothing
			} else {
				wp_enqueue_style( LDAUC_PLUGIN_DIR_NAME . '_font_awesome', LDAUC_PLUGIN_URL . 'css/font-awesome/css/font-awesome.css', array(), '1.0.0', 'all' );
			}
			wp_enqueue_style( LDAUC_PLUGIN_DIR_NAME . '_template_style', $this -> tmp -> get_template( LDAUC_PLUGIN_DIR_NAME . '_style.css', null, null, true ), array(), '1.0.0', 'all' );
		}

		/**
		 * Register the JavaScript - front
		 *
		 * @since    1.0.0
		 */
		public function ldauc_enqueue_scripts () {
			wp_enqueue_script( LDAUC_PLUGIN_DIR_NAME . '_template_script', $this -> tmp -> get_template( LDAUC_PLUGIN_DIR_NAME . '_script.js', null, null, true ), array( 'jquery' ), '1.0.0', true );
		}
	}

	LDaucFunctions ::instance();

endif;
