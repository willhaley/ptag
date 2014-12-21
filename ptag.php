<?php

/*
Plugin Name: PTag
Plugin URI: https://surc.us/ptag
Description: Allows selection of a primary tag (per post) in the WordPress admin. Usage Instructions are located on the Post screen "Help" tab at the top of the menu
Version: 1.0
Author: Will Haley
Author URI: http://surc.us
License: A "Slug" license name e.g. GPL2
*/

if ( ! class_exists( 'Surcus_PTag' ) ) {

	/**
	 * Class Surcus_PTag
	 */
	class Surcus_PTag {

		/**
		 * Constant that sets the custom field
		 */
		const CUSTOM_FIELD_NAME = '_surcus_ptag';

		/**
		 *  Adds in the hooks
		 */
		function __construct() {
			add_action( 'post_submitbox_misc_actions', array( $this, 'post_submitbox_misc_actions' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			add_action( 'save_post', array( $this, 'save_post' ) );

			add_action( 'load-post-new.php', array( $this, 'add_help_tag' ));
			add_action( 'load-post.php', array( $this, 'add_help_tag' ));

//			add_filter('contextual_help',  array( $this, 'contextual_help' ), 10, 3);
		}

		/**
		 * adds the new meta box to the post screen
		 */
		function admin_menu() {

			global $wp_taxonomies;

			/**
			 * want to make sure the core tag box is
			 * still there b/c the js is dependent on it.
			 */
			if ( isset ( $wp_taxonomies ['post_tag'] ) ) {

				add_meta_box(
					'surcus_ptag_box',
					'Primary Tag',
					array( $this, 'add_meta_box' ),
					'post',
					'side',
					'default'
				);

			}

		}

		function post_submitbox_misc_actions() {

			global $post;

			$ptag = sanitize_text_field( get_post_meta( $post->ID, $this::CUSTOM_FIELD_NAME, true ) );

			if ( ! $ptag ) {
				$ptag = 'Not Set';
			}

			?>
			<div class="misc-pub-section misc-pub-surcus-ptag">
				<span class="surcus-ptag__label">Primary Tag: </span>
				<span class="surcus-ptag__tag"><?php echo $ptag ?></span>
				<a href="#edit_ptag" class="hide-if-no-js" onclick="jQuery('.surcus-ptag__edit').slideToggle()">
					<span><?php _e( 'Edit' ); ?></span>
				</a>

				<div class="surcus-ptag__edit" style="display: none">
					<select name="_surcus_ptag" id="surcus_ptag_selector"></select>
					<input type="hidden" id="selected_surcus_ptag" value="<?php echo $ptag ?>"/>
					<a class="surcus_ptag__confirm hide-if-no-js button" href="#surcus_ptag__confirm">OK</a>
					<a href="#surc_ptag__cancel"
					   class="surcus_ptag__cancel hide-if-no-js button-confirm"><?php _e( 'Cancel' ); ?></a>
				</div>
			</div>
			<?php
			wp_nonce_field( 'surcus_ptag_meta_box', 'surcus_ptag_meta_box_nonce' );
		}

		/**
		 * Registers and Enqueues the need JS and CSS
		 *
		 * @param $hook
		 */
		function admin_enqueue_scripts( $hook ) {

			if ( in_array( $hook, array( 'post-new.php', 'post.php' ) ) ) {

				wp_register_script( 'surcus_ptag_scripts', plugins_url( 'ptag-script.min.js', __FILE__ ), false, '1.0.0' );
				wp_enqueue_script( 'surcus_ptag_scripts' );

				wp_register_style( 'surcus_ptag_styles', plugins_url( 'ptag-style.min.css', __FILE__ ), false, '1.0.0' );
				wp_enqueue_style( 'surcus_ptag_styles' );

			}

		}

		/**
		 * Collects and saves the primary tag from the edit screen
		 *
		 * @param $post_id
		 */
		function save_post( $post_id ) {
			if ( ! isset( $_REQUEST['surcus_ptag_meta_box_nonce'] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( $_REQUEST['surcus_ptag_meta_box_nonce'], 'surcus_ptag_meta_box' ) ) {
				return;
			}

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			if ( isset( $_REQUEST[ self::CUSTOM_FIELD_NAME ] ) ) {

				$ptag = sanitize_text_field( $_REQUEST[ self::CUSTOM_FIELD_NAME ] );
				delete_post_meta( $post_id, self::CUSTOM_FIELD_NAME );
				add_post_meta( $post_id, self::CUSTOM_FIELD_NAME, $ptag, true );

			}

		}

		function add_help_tag(){

			$settings = array(
				'title'    => 'Primary Tag',
				'id'       => 'surcus_ptag',
				'callback' => array( $this, 'get_contextual_help'),
			);
			$current_screen = get_current_screen();
			$current_screen->add_help_tab( $settings );
		}

		function get_contextual_help(){
			?>
				<h3>Admin Area</h3>
					An additional line is added to the Publish Metabox area that will display and allow you to assign the primary tag for the post.
					<ul>
						<li>The primary tag must be selected from tags that are already associated with the post.  You cannot mark a tag that isn't associated with post as a primary tag.</li>
						<li>If a primary tag is not set for the post or no tags are associated with the post, the primary tag will state "Not Set"</li>
						<li>If you removed the primary tag from the tags associated with the post, the post's priamry tag will also be removed.</li>
						<li>To set a new or change a primary tag, you should click on the "edit" link adjacent to the primary tag text, and then use the dropdown that appears to make your selection.  Click "OK" to confirm the change, or "cancel" to revert back to the original state.</li>
						<li>The new primary tag is not saved until you save the draft or update the post</li>
					</ul>
					<h3>Theme Usage</h3>
						<p>
							<strong>2 functions are provided for get the stored primary tag.</strong>
						</p>
						function the_primary_tag( {post_id (optional)}, { return (true | false) }),
						<ul>
							<li>returns / outputs a simple string of the primary tag name.</li>
							<li>post_id, optional you can leave blank if in the loop.</li>
							<li>return, optional, if false will echo out value, if true will return value.</li>
						</ul>
						function the_primary_tag_html( {post_id (optional)}, { return (true | false) }),
						<ul>
							<li>return / outputs the name of the primary tag wrapped in an anchor tag to that tag.</li>
							<li>post_id, optional you can leave blank if in the loop.</li>
							<li>return, optional, if false will echo out value, if true will return value.</li>
						</ul>
		<?php
		}

	}

}

new Surcus_PTag();


if ( ! function_exists( 'the_primary_tag' ) ) {

	/**
	 * Either outputs or returns the simple primary tag string
	 *
	 * @param null $post_id optional
	 * @param bool $return optional
	 *
	 * @return string
	 */
	function the_primary_tag( $post_id = null, $return = false ) {

		if ( ! $post_id ) {
			global $post;
			$post_id = $post->ID;
		}

		if ( is_int( $post_id ) ) {
			$ptag = sanitize_text_field( get_post_meta( $post_id, Surcus_PTag::CUSTOM_FIELD_NAME, true ) );
		}

		if ( $return ) {
			return $ptag;
		}

		echo $ptag;

		return;
	}

}

if ( ! function_exists( 'the_primary_tag_html' ) ) {

	/**
	 *
	 * Outputs or returns the string that includes the html for an <a> tag that
	 * links to the primary tag
	 *
	 * @param null $post_id optional
	 * @param bool $return optional
	 * @param array $classes optional
	 *
	 * @return string
	 */
	function the_primary_tag_html( $post_id = null, $return = false, $classes = array() ) {

		$ptag_name = the_primary_tag( $post_id, true );
		$ptag      = get_term_by( 'name', $ptag_name, 'post_tag' );
		$ptag_link = get_term_link( $ptag, 'post_tag' );

		$html = sprintf( '<a href="%s" class="%s">%s</a>', $ptag_link, sanitize_text_field( implode( ' ', $classes ) ), $ptag_name );

		if ( $return ) {
			return $html;
		}

		echo $html;

		return;
	}

}