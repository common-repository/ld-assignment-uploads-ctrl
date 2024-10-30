<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

/**
 * Database operations
 */
class DbHelper {

	/**
	 * Initialize the class.
	 */
	public function __construct () {
		global $wpdb;
		$this -> db = $wpdb;
		$this -> postmeta_table = $this -> db -> prefix . 'postmeta';
		$this -> posts_table = $this -> db -> prefix . 'posts';
		$this -> options_table = $this -> db -> prefix . 'options';
	}

	/**
	 * get courses
	 *
	 * @param array $args
	 * @return array
	 */
	public function ldauc_get_all_courses ( $args = array() ) {

		$defaults = array( 'post_type' => 'sfwd-courses', 'fields' => 'ids', 'nopaging' => true, 'post_status' => 'publish'

		);

		$course_ids = array();

		$course_query_args = wp_parse_args( $args, $defaults );

		$course_query = new WP_Query( $course_query_args );

		if ( ( isset( $course_query -> posts ) ) && ( !empty( $course_query -> posts ) ) ) {
			$course_ids = $course_query -> posts;
		}

		return $course_ids;
	}

	/**
	 * Get group courses
	 * @param int $group_id
	 *
	 * @return array|null|object
	 */
	public function ldauc_get_all_group_courses ( $group_id = 0 ) {

		$courses = array();

		if ( !empty( $group_id ) ) {
			$course_ids = learndash_group_enrolled_courses( $group_id );

			$count_course_ids = count( $course_ids );
			$placeholders = array_fill( 0, $count_course_ids, '%d' );
			$placeholders_implode = implode( ',', $placeholders ); // %d,%d

			$query = "SELECT * FROM " . $this -> db -> posts . " WHERE ID IN(" . $placeholders_implode . ") AND post_status = 'publish'";
			$courses = $this -> db -> get_results( $this -> db -> prepare( $query, $course_ids ) );
		}

		return $courses;
	}

	/**
	 * Get all group leaders groups
	 *
	 * @param int $user_id
	 * @return array|null|object
	 */
	public function ldauc_get_all_group_leaders_groups ( $user_id = 0 ) {

		$groups = array();

		if ( empty( $user_id ) ) {
			$user = wp_get_current_user();
			$user_id = $user -> ID;
		}

		if ( !empty( $user_id ) ) {

			$sql_str = $this -> db -> prepare( "SELECT usermeta.meta_value as group_ids FROM " . $this -> db -> usermeta . " as usermeta INNER JOIN " . $this -> db -> posts . " as posts ON posts.ID=usermeta.meta_value WHERE  user_id = %d  AND meta_key LIKE %s AND posts.post_status = 'publish'", $user_id, 'learndash_group_leaders_%' );
			$group_ids = $this -> db -> get_col( $sql_str );

			$count_group_ids = count( $group_ids );
			$placeholders = array_fill( 0, $count_group_ids, '%d' );
			$placeholders_implode = implode( ',', $placeholders ); // %d,%d

			$query = "SELECT * FROM " . $this -> db -> posts . " WHERE ID IN(" . $placeholders_implode . ") AND post_status = 'publish'";
			$groups = $this -> db -> get_results( $this -> db -> prepare( $query, $group_ids ) );

		}

		return $groups;
	}
}
