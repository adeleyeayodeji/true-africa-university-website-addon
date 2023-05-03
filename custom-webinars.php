<?php

/**
 * Plugin Name: Custom Webinars
 * Plugin URI:  https://trueafricauniversity.com/
 * Author:      trueafricauniversity
 * Author URI:  https://trueafricauniversity.com/
 * Description: This plugin extends the functionality of the webinars post type.
 * Version:     0.1.1
 * License:     GPL-2.0+
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: custom-webinars
 */
//security
if (!defined('ABSPATH')) {
    die("You are not allowed to call this page directly.");
}

//define
define('CUSTOM_WEBINARS_TAU_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CUSTOM_WEBINARS_TAU_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CUSTOM_WEBINARS_TAU_PLUGIN_VERSION', '0.1.0');


class CustomWebinarsTAUshortcode
{
    public function init()
    {
        add_shortcode('custom_webinars-tau', array($this, 'custom_webinars'));
        //css
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 9999);
        //admin script
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        //ajax custom-webinars-tau-get-tag
        add_action('wp_ajax_custom-webinars-tau-get-tag', array($this, 'get_tag'));
        add_action('wp_ajax_nopriv_custom-webinars-tau-get-tag', array($this, 'get_tag'));
        //add meta box to webinars
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        //save meta box
        add_action('save_post', array($this, 'save_meta_box'));
    }

    //save_meta_box
    public function save_meta_box($post_id)
    {
        // Check if our nonce is set.
        if (!isset($_POST['custom-webinars-tau_nonce'])) {
            return;
        }
        // Verify that the nonce is valid.
        if (!wp_verify_nonce($_POST['custom-webinars-tau_nonce'], 'custom-webinars-tau')) {
            return;
        }
        // Check the user's permissions.
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        // Sanitize user input.
        $custom_webinar_thumbnail = sanitize_text_field($_POST['custom_webinar_thumbnail']);
        // Update the meta field in the database.
        update_post_meta($post_id, 'custom_webinar_thumbnail', $custom_webinar_thumbnail);
    }

    //add_meta_box
    public function add_meta_box()
    {
        add_meta_box(
            'custom-webinars-tau',
            __('Webinars Custom Thumbnail', 'custom-webinars'),
            array($this, 'meta_box_callback'),
            'webinars',
            'normal',
            'high'
        );
    }

    //meta_box_callback
    public function meta_box_callback($post)
    {
        // Add a nonce field so we can check for it later.
        wp_nonce_field('custom-webinars-tau', 'custom-webinars-tau_nonce');
        //ob start
        ob_start();
        require CUSTOM_WEBINARS_TAU_PLUGIN_DIR . 'view/meta-box.php';
        //ob get clean
        $html = ob_get_clean();
        //echo html
        echo $html;
    }

    //get_tag
    public function get_tag()
    {
        //security
        $nonce = $_GET['nonce'];
        if (!wp_verify_nonce($nonce, 'custom-webinars-nonce')) {
            wp_send_json([
                'code' => 403,
                'message' => 'You are not allowed to call this page directly.'
            ]);
        }
        //get tag
        $tag = sanitize_text_field($_GET['tag']);
        //get limit
        $limit = sanitize_text_field($_GET['limit']);
        //get all post type webinars
        $args = array(
            'post_type' => 'webinars',
            'posts_per_page' => $limit,
            'order' => 'DESC',
            'orderby' => 'date',
            'post_status' => 'publish',
        );
        //check if tag is not empty taxonomy
        if (!empty($tag)) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'post_tag',
                    'field' => 'slug',
                    'terms' => $tag,
                ),
            );
        }
        //get webinars
        $webinars = get_posts($args);
        //check if webinars is not empty
        if (empty($webinars)) {
            wp_send_json([
                'code' => 404,
                'message' => 'No webinars found.'
            ]);
        }
        //get json html
        $html = $this->get_webinar_item($webinars);
        //send json
        wp_send_json([
            'code' => 200,
            'message' => 'Success',
            'html' => $html,
        ]);
    }

    //enqueue_scripts
    public function enqueue_scripts()
    {
        //izitoast css
        wp_enqueue_style('custom-webinars-izitoast', CUSTOM_WEBINARS_TAU_PLUGIN_URL . 'view/assets/css/iziToast.min.css', array(), time(), 'all');
        //css
        wp_enqueue_style('custom-webinars-tau', CUSTOM_WEBINARS_TAU_PLUGIN_URL . 'view/assets/css/style.css', array(), time(), 'all');
        //responsive
        wp_enqueue_style('custom-webinars-tau-responsive', CUSTOM_WEBINARS_TAU_PLUGIN_URL . 'view/assets/css/responsive.css', array(), time(), 'all');
        //js blockUi
        wp_enqueue_script('custom-webinars-blockUi', CUSTOM_WEBINARS_TAU_PLUGIN_URL . 'view/assets/js/blockUI.js', array('jquery'), time(), true);
        //js izitoast
        wp_enqueue_script('custom-webinars-izitoast', CUSTOM_WEBINARS_TAU_PLUGIN_URL . 'view/assets/js/iziToast.min.js', array('jquery'), time(), true);
        //js
        wp_enqueue_script('custom-webinars-tau', CUSTOM_WEBINARS_TAU_PLUGIN_URL . 'view/assets/js/script.js', array('jquery'), time(), true);
        //localize
        wp_localize_script('custom-webinars-tau', 'customwebinars_props', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('custom-webinars-nonce'),
        ));
    }

    //admin_enqueue_scripts
    public function admin_enqueue_scripts()
    {
        //css
        wp_enqueue_style('custom-webinars-tau-admin', CUSTOM_WEBINARS_TAU_PLUGIN_URL . 'view/assets/css/admin.css', array(), time(), 'all');
        //js
        wp_enqueue_script('custom-webinars-tau-admin', CUSTOM_WEBINARS_TAU_PLUGIN_URL . 'view/assets/js/admin.js', array('jquery'), time(), true);
    }

    public function custom_webinars($atts)
    {
        $atts = shortcode_atts(
            array(
                'title' => 'Webinars',
                'button_text' => 'View All Webinars',
                'button_link' => '#',
                'filter_years' => "2021",
                'limit' => 6,
            ),
            $atts
        );
        //get all post type webinars
        $args = array(
            'post_type' => 'webinars',
            'posts_per_page' => $atts['limit'],
            'order' => 'DESC',
            'orderby' => 'date',
            'post_status' => 'publish',
        );
        //update filter years
        $atts['filter_years'] = strpos($atts['filter_years'], ",") ? explode(",", $atts['filter_years']) : [$atts['filter_years']];
        //check if filter_years is not empty 'post_tag'
        if (!empty($atts['filter_years'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'post_tag',
                    'field' => 'slug',
                    'terms' => $atts['filter_years']
                ),
            );
        }
        //get the posts
        $webinars = get_posts($args);
        //get all post tags for webinars
        $tags = get_terms(array(
            'taxonomy' => 'post_tag',
            'hide_empty' => false,
            //order by DESC
            'order' => 'DESC',
        ));
        //pass to attr
        $atts['tags'] = $tags;
        //pass to attr
        $atts['webinars'] = $webinars;
        //log
        // file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'log.txt', print_r($atts, true));
        //get the content
        $content = $this->getContent($atts);
        //return the content
        return $content;
    }

    //get the content
    public function getContent($args = [])
    {
        ob_start();
        //check if args is empty
        if (!empty($args)) {
            extract($args);
        }
        require __DIR__ . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'custom-webinars.php';
        return ob_get_clean();
    }

    //get_webinar_item
    public function get_webinar_item($webinars)
    {
        ob_start();
        require __DIR__ . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR . 'loop-item.php';
        return ob_get_clean();
    }
}

//instantiate the class
$customWebinarsTAUshortcode = new CustomWebinarsTAUshortcode();
$customWebinarsTAUshortcode->init();
//https://github.com/adeleyeayodeji