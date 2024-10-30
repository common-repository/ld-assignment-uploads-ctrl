<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

/**
 * File Upload options view
 */
class LDaucFileUploadSettingsAdminView {

	private $functions;

	/**
	 * Initialize the class.
	 *
	 * @param      object $functions Object.
	 */
	public function __construct ( $functions ) {
		$this -> functions = $functions;
	}

	/**
	 * Render the page.
	 */
	public function display () {
		?>
        <div class="wrap">
            <div class="wrap-left">
                <h1> <?php echo __( 'LearnDash Assignment Uploads Control Settings', 'ldauc' ); ?> </h1>
                <form action="options.php" method="POST">
					<?php settings_fields( 'ld_file_upload_settings' ); ?>
					<?php do_settings_sections( 'learndash-lms/file-upload-settings' ); ?>
					<?php submit_button(); ?>
                </form>
            </div>
        </div>
		<?php
	}
}
