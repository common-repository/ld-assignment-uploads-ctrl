<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

/**
 * Get template class
 */
class GetTemplateHelper {

	private $plugin_dir_name = '';
	private $plugin_dir = '';
	private $plugin_url = '';

	/**
	 * Initialize the class.
	 * @param $plugin_dir_name
	 * @param $plugin_dir
	 * @param $plugin_url
	 */
	public function __construct ( $plugin_dir_name, $plugin_dir, $plugin_url ) {
		$this -> plugin_dir_name = $plugin_dir_name;
		$this -> plugin_dir = $plugin_dir;
		$this -> plugin_url = $plugin_url;
	}

	/**
	 * Get Plugins template and pass data to be used in template
	 *
	 * Checks to see if user has a 'plugins' directory in their current theme
	 * and uses the template if it exists.
	 *
	 *
	 * @param  string $name template name
	 * @param  array $args data for template
	 * @param  boolean $echo echo or return
	 * @param  boolean    return_file_path  return just file path instead of output
	 * @return mixed
	 *
	 */
	public function get_template ( $name, $args, $echo = false, $return_file_path = false ) {

		$template_paths = array();
		$file_pathinfo = pathinfo( $name );

		if ( !isset( $file_pathinfo[ 'extension' ] ) ) {
			$file_pathinfo[ 'extension' ] = 'php';
		}

		// If for some reason the $name has an extension we reset the $name to be without it.
		if ( $file_pathinfo[ 'extension' ] == 'php' ) {
			$template_paths = array_merge( $template_paths, array( $this -> plugin_dir_name . '/' . $file_pathinfo[ 'filename' ] . '.' . $file_pathinfo[ 'extension' ], $file_pathinfo[ 'filename' ] . '.' . $file_pathinfo[ 'extension' ] ) );

			// and if the extension is .js or .css we check for minified versions.
		} else if ( ( $file_pathinfo[ 'extension' ] == 'js' ) || ( $file_pathinfo[ 'extension' ] == 'css' ) ) {

			// force here returning file path
			$return_file_path = true;
			$template_paths = array_merge( $template_paths, array( $this -> plugin_dir_name . '/' . $file_pathinfo[ 'filename' ] . '.' . $file_pathinfo[ 'extension' ], $this -> plugin_dir_name . '/' . $file_pathinfo[ 'filename' ] . '.min.' . $file_pathinfo[ 'extension' ], $file_pathinfo[ 'filename' ] . '.' . $file_pathinfo[ 'extension' ], $file_pathinfo[ 'filename' ] . '.min.' . $file_pathinfo[ 'extension' ] ) );

		}
		$filepath = locate_template( $template_paths );

		// locate_template found them there but if we have js or css we need URL instead of PATH
		if ( ( $file_pathinfo[ 'extension' ] == 'js' ) || ( $file_pathinfo[ 'extension' ] == 'css' ) ) {
			$filepath = str_replace( ABSPATH, '/', $filepath );
		}

		if ( !$filepath ) {
			$template_files = array();

			foreach ( $template_paths as $template_path ) {
				$template_file = basename( $template_path );
				if ( !array_key_exists( $template_file, $template_files ) ) {
					$template_files[ $template_file ] = $template_file;
				}
			}

			if ( !empty( $template_files ) ) {
				foreach ( $template_files as $template_file ) {
					if ( file_exists( $this -> plugin_dir . 'templates/' . $template_file ) ) {
						$filepath = $this -> plugin_dir . 'templates/' . $template_file;
						break;
					}
				}

				if ( ( $file_pathinfo[ 'extension' ] == 'js' ) || ( $file_pathinfo[ 'extension' ] == 'css' ) ) {
					$filepath = untrailingslashit( $this -> plugin_url ) . str_replace( $this -> plugin_dir, '/', $filepath );
				}
			}
		}

		if ( !$filepath ) {
			return false;
		}

		/**
		 * Filter filepath for template being called
		 *
		 */
		$filepath = apply_filters( $this -> plugin_dir_name . '_template', $filepath, $name, $args, $echo, $return_file_path );

		if ( $return_file_path ) {
			return $filepath;
		}

		// Added check to ensure external hooks don't return empty or non-accessible filenames.
		if ( ( !empty( $filepath ) ) && ( file_exists( $filepath ) ) && ( is_file( $filepath ) ) ) {
			extract( $args );
			ob_start();
			include( $filepath );
			$contents = ob_get_clean();

			if ( !$echo ) {
				return $contents;
			}
			echo $contents;
		}
	}

	/**
	 * @return string
	 */
	public function getPluginDirName () {
		return $this -> plugin_dir_name;
	}

	/**
	 * @param string $plugin_dir_name
	 */
	public function setPluginDirName ( $plugin_dir_name ) {
		$this -> plugin_dir_name = $plugin_dir_name;
	}

	/**
	 * @return string
	 */
	public function getPluginDir () {
		return $this -> plugin_dir;
	}

	/**
	 * @param string $plugin_dir
	 */
	public function setPluginDir ( $plugin_dir ) {
		$this -> plugin_dir = $plugin_dir;
	}

	/**
	 * @return string
	 */
	public function getPluginUrl () {
		return $this -> plugin_url;
	}

	/**
	 * @param string $plugin_url
	 */
	public function setPluginUrl ( $plugin_url ) {
		$this -> plugin_url = $plugin_url;
	}
}