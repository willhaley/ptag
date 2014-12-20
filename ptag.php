<?php

/*
Plugin Name: PTag
Plugin URI: https://surc.us/ptag
Description: Allows selection of a primary tag (per post) in the WordPress admin.
Version: 1.0
Author: Will Haley
Author URI: http://surc.us
License: A "Slug" license name e.g. GPL2
*/

if ( ! class_exists( 'Surcus_PTag' ) ) {

    /**
     * Class Surcus_PTag
     */
    class Surcus_PTag
    {

        /**
         * Constant that sets the custom field
         */
        const CUSTOM_FIELD_NAME = '_surcus_ptag';

        /**
         *
         */
        function __construct()
        {
            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
            add_action( 'save_post', array( $this, 'save_post' ) );
        }

        /**
         * adds the new meta box to the post screen
         */
        function admin_menu()
        {



            global $wp_taxonomies;

            /**
             * want to make sure the core tag box is
             * still there
             */
            if ( isset ($wp_taxonomies ['post_tag'] ) ) {

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

        /**
         * @param $post
         * @param $box
         */
        function add_meta_box( $post, $box )
        {

            wp_nonce_field( 'surcus_ptag_meta_box', 'surcus_ptag_meta_box_nonce' );

            $ptag = sanitize_text_field( get_post_meta( $post->ID, Surcus_PTag::CUSTOM_FIELD_NAME, true ) );

            ?>
            <div class="surcus_ptag">
                <label for="surcus_ptag">Select a Primary Tag: </label>
                <select name="_surcus_ptag" id="surcus_ptag_selector"></select>
                <input type="hidden" id="selected_surcus_ptag" value="<?php echo $ptag ?>"/>
            </div>
        <?php

        }


        /**
         * @param $hook
         */
        function admin_enqueue_scripts( $hook )
        {

            if ( in_array( $hook, array( 'post-new.php', 'post.php' ) ) ) {

                wp_register_script( 'surcus_ptag_scripts', plugins_url( 'script.js', __FILE__ ), false, '1.0.0');
                wp_enqueue_script( 'surcus_ptag_scripts' );

                wp_register_style( 'surcus_ptag_styles', plugins_url( 'ptag-style.css', __FILE__ ), false, '1.0.0');
                wp_enqueue_style( 'surcus_ptag_styles' );

            }

        }

        /**
         * @param $post_id
         */
        function save_post( $post_id )
        {

            if ( ! isset( $_POST['surcus_ptag_meta_box_nonce'] ) ) {
                return;
            }

            if ( ! wp_verify_nonce( $_POST['surcus_ptag_meta_box_nonce'], 'surcus_ptag_meta_box' ) ) {
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
                $result = add_post_meta( $post_id, self::CUSTOM_FIELD_NAME, $ptag, true );

                if ( ! $result ) {
                    die ( 'something went wrong' );
                }

            }

        }

    }

}

new Surcus_PTag();


if ( ! function_exists( 'the_primary_tag' ) ) {

    /**
     * @param null $post_id
     * @param bool $return
     * @return string
     */
    function the_primary_tag( $post_id = null, $return = true )
    {

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