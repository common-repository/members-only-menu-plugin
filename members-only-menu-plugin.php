<?php
/**
 * Plugin Name: Members Only Menu Plugin
 * Plugin URI: http://brandonwamboldt.ca/plugins/members-only-menu-plugin/
 * Author: Brandon Wamboldt
 * Author URI: http://brandonwamboldt.ca/
 * Version: 2.0
 * Description: I've deprecated this project and renamed it to <a href="http://wordpress.org/extend/plugins/wordpress-access-control/">WordPress Access Control</a>. Please download that instead, and receive access to more features such as restricting pages to members OR non-members, specific roles and specifying redirect URLS. 
 */

add_action('wp'            , array('MembersOnlyMenuPlugin', 'check_for_members_only'));
add_action('add_meta_boxes', array('MembersOnlyMenuPlugin', 'add_members_only_meta_box'));
add_action('save_post'     , array('MembersOnlyMenuPlugin', 'members_only_save_postdata'));

add_filter('get_pages', array('MembersOnlyMenuPlugin', 'get_pages'));

/**
 * We check to see if the class exists because it isn't part of
 * pre WordPress 3 systems
 */
if (class_exists('Walker_Nav_Menu')) {
    add_filter( 'wp_nav_menu_args' , array( 'MembersOnlyMenuPlugin' , 'wp_nav_menu_args' ) );

    /**
     * A custom walker that checks to see if the page is members only then
     * checks for authentication
     * 
     * @author brandon
     * @package WordPress
     * @subpackage MembersOnlyMenuPlugin
     */
    class Members_Only_Nav_Menu_Walker extends Walker_Nav_Menu {

        var $in_private = false;
        var $private_lvl = 0;
        
        function start_lvl(&$output, $depth) {
        if ( !$this->in_private ) {
            $indent = str_repeat("    ", $depth);
            $output .= "\n$indent<ul class=\"sub-menu\"><li style='display:none;'></li>\n";
        }
        }

        function end_lvl(&$output, $depth) {
        if ( !$this->in_private ) {
            $indent = str_repeat("    ", $depth);
            $output .= "$indent</ul>\n";
        }
        }  
        
        function start_el(&$output, $item, $depth, $args) {
        global $wp_query;

        // Check to see if the user is logged in
        if ( !get_post_meta( $item->object_id , '_members_only_menu' , true ) || is_user_logged_in() )  {
            
            if ( !$this->in_private ) { 
                $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
        
                $class_names = $value = '';
        
                $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        
                $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
                $class_names = ' class="' . esc_attr( $class_names ) . '"';
        
                $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
        
                $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
                $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
                $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
                $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
        
                $item_output = $args->before;
                $item_output .= '<a'. $attributes .'>';
                $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
                $item_output .= '</a>';
                $item_output .= $args->after;
        
                $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
            }
        } else {
                $this->in_private = true;
                $this->private_lvl++;
            }
        }

        function end_el(&$output, $item, $depth) {
        
        // Check to see if the user is logged in
        if ( !get_post_meta( $item->object_id , '_members_only_menu' , true ) || is_user_logged_in() )  {
            if ( !$this->in_private ) { 
                $output .= "</li>\n";
            }
        } else if ( get_post_meta( $item->object_id , '_members_only_menu' , true ) ) {
                $this->private_lvl--;
                if ( $this->private_lvl == 0 ) {
                $this->in_private = false;
                }
        }
        }
    }
}

/**
 * We check to see if the class exists because it may get removed in the future?
 */
if (class_exists('Walker_Page')) {
    add_filter( 'wp_page_menu_args', array( 'MembersOnlyMenuPlugin' , 'wp_page_menu_args' ) );

    /**
     * A custom walker that checks to see if the page is members only then
     * checks for authentication (For wp_page_menu)
     * 
     * @author brandon
     * @package WordPress
     * @subpackage MembersOnlyMenuPlugin
     */
    class Members_Only_Page_Walker extends Walker_Page {

        var $in_private = false;
        var $private_lvl = 0;
        
        function start_lvl(&$output, $depth) {
            if ( !$this->in_private ) {
                $indent = str_repeat("\t", $depth);
                $output .= "\n$indent<ul class=\"sub-menu\"><li style='display:none;'></div>\n";
            }
        }

        function end_lvl(&$output, $depth) {
            if ( !$this->in_private ) {
                $indent = str_repeat("\t", $depth);
                $output .= "$indent</ul>\n";
            }
        }  
        
        function start_el(&$output, $page, $depth, $args, $current_page) {
            if ( !get_post_meta( $page->ID, '_members_only_menu' , true ) || is_user_logged_in() ) {
                if ( !$this->in_private ) { 
                    if ( $depth )
                        $indent = str_repeat("\t", $depth);
                    else
                        $indent = '';
            
                    extract($args, EXTR_SKIP);
                    $css_class = array('page_item', 'page-item-'.$page->ID);
                    if ( !empty($current_page) ) {
                        $_current_page = get_page( $current_page );
                        if ( isset($_current_page->ancestors) && in_array($page->ID, (array) $_current_page->ancestors) )
                            $css_class[] = 'current_page_ancestor';
                        if ( $page->ID == $current_page )
                            $css_class[] = 'current_page_item';
                        elseif ( $_current_page && $page->ID == $_current_page->post_parent )
                            $css_class[] = 'current_page_parent';
                    } elseif ( $page->ID == get_option('page_for_posts') ) {
                        $css_class[] = 'current_page_parent';
                    }
            
                    $css_class = implode(' ', apply_filters('page_css_class', $css_class, $page));
            
                    $output .= $indent . '<li class="' . $css_class . '"><a href="' . get_page_link($page->ID) . '" title="' . esc_attr( wp_strip_all_tags( apply_filters( 'the_title', $page->post_title, $page->ID ) ) ) . '">' . $link_before . apply_filters( 'the_title', $page->post_title, $page->ID ) . $link_after . '</a>';
            
                    if ( !empty($show_date) ) {
                        if ( 'modified' == $show_date )
                            $time = $page->post_modified;
                        else
                            $time = $page->post_date;
            
                        $output .= " " . mysql2date($date_format, $time);
                    }
                }
            } else {
                $this->in_private = true;
                $this->private_lvl++;
            }
        }

        function end_el(&$output, $page, $depth) {
            if ( !get_post_meta( $page->ID, '_members_only_menu' , true ) || is_user_logged_in() ) {
                if ( !$this->in_private ) { 
                    $output .= "</li>\n";
                }
            } else if (get_post_meta($page->ID, '_members_only_menu', true)) {
                $this->private_lvl--;
                if ($this->private_lvl == 0) {
                    $this->in_private = false;
                }
            }
        }
    }
}

/**
 * The main plugin
 * 
 * @author brandon
 * @package WordPress
 * @subpackage MembersOnlyMenuPlugin
 */
class MembersOnlyMenuPlugin {

    /**
     * This executes right after the post data is set, and 
     * checks if some post meta is set. If it is, we redirect 
     * to the login page (Only if the user isn't logged in).
     */
    function check_for_members_only() 
    {
        global $post;
    
        if ( get_post_meta($post->ID, '_members_only_menu', true) && !is_user_logged_in()) {
            header('Location: ' . get_bloginfo('wpurl') . '/wp-login.php');
            exit();
        }
    }
    
    /**
     * Add a meta box to the editor for pages
     */
    function add_members_only_meta_box() 
    {
        add_meta_box('add_members_only_meta', 'Members Only', array( 'MembersOnlyMenuPlugin', 'add_members_only_meta'), 'page', 'side', 'high');
    }

    /**
     * This generates the HTML for the meta box (A nonce and a checkbox)
     */
    function add_members_only_meta() 
    {
        global $post;
    
        $meta_box_value = get_post_meta($post->ID, '_members_only_menu', true ); 
    
        if ($meta_box_value == 'true') {
            $meta_box_value = 'checked="checked"';
        }
    
        echo '<input type="hidden" name="members_only_nonce" id="members_only_nonce" value="' . wp_create_nonce('members_only') . '" />';
        echo '<input type="checkbox" name="members_only" id="members_only" value="true" '.$meta_box_value .' /> ' . __('Restrict To Members?');
    }
    
    /**
     * Saves meta data to save our members only value
     * 
     * @param int $post_id The ID of the page we are saving
     */
    function members_only_save_postdata($post_id)
    {
        /*
         * Verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times
         */
        if (!isset($_POST['members_only_nonce'])) return $post_id;
        
        if (!wp_verify_nonce($_POST['members_only_nonce'], 'members_only')) {
            return $post_id;
        }
    
        /*
         * verify if this is an auto save routine. If it is our form has not been submitted, 
         * so we don't want to do anything
         */
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;

        // Save
        if (isset($_POST['members_only']) && $_POST['members_only'] == 'true') {
            update_post_meta($post_id, '_members_only_menu', 'true');
        } else {
            delete_post_meta($post_id, '_members_only_menu');
        }
    
        return $post_id;
    }
    
    /**
     * Adds the walker parameter automatically to all wp_nav_menu calls
     * 
     * @param array $args The array of arguments passed to wp_nav_menu
     * @return array
     */
    function wp_nav_menu_args($args) 
    {
        $args['walker'] = new Members_Only_Nav_Menu_Walker();
        return $args;
    }
    
    /**
     * Removes the walker parameter if no nav menu exists as it falls 
     * back to wp_page_menu which our walker would break with
     * 
     * @param array $args The array of arguments passed to wp_nav_menu
     * @return array
     */
    function wp_page_menu_args($args) 
    {
        // Only remove the walker if it is ours
        if (isset($args['walker']) && get_class($args['walker']) == 'Members_Only_Nav_Menu_Walker') {
            $args['walker'] = new Members_Only_Page_Walker();
        }
        
        return $args;
    }
    
    /**
     * This hooks in at a higher level to make sure functions like 
     * wp_list_pages won't return members only pages.
     * 
     * @param $pages
     * @return array
     */
    function get_pages($pages) 
    {
        $auth = is_user_logged_in();
        
        foreach ($pages as $key => $page) {
            $meta = get_post_meta($page->ID, '_members_only_menu', true);
            
            if ($meta && !$auth) {
                unset($pages[$key]);
            }
        }
        
        return $pages;
    }
}
