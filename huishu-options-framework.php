<?php
/*
Plugin Name: HUisHU Options Framework
Description: Bietet Funktionen für Optionen für HUisHU Themes und Plugins
Version: 1.0.4
Author: HUisHU. Digitale Kreativagentur GmbH
Author URI: https://www.huishu-agentur.de
License: GPL2+
*/

/**
 * Silence is golden; exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Use Plugin Update Checker to check for Updates on Github
 */
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/HUisHUAgentur/huishu-options-framework/',
	__FILE__,
	'huishu-options-framework'
);
$myUpdateChecker->setBranch('main');

require_once(plugin_dir_path( __FILE__ ).'class-tgm-plugin-activation.php');

function huishu_options_framework_add_required_plugins_to_tgmpa(){
    $plugins = array(
        array(
            'name'      => 'CMB2',
            'slug'      => 'cmb2',
        ),
    );

    $config = array(
        'id'           => 'huishu_options_framework',   // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to bundled plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'parent_slug'  => 'plugins.php',            // Parent menu slug.
        'capability'   => 'manage_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
    );
    tgmpa( $plugins, $config );
}
add_action('tgmpa_register','huishu_options_framework_add_required_plugins_to_tgmpa');

add_action('plugins_loaded',array('HUisHUOptionsFramework','getInstance'));

class HUisHUOptionsFramework {
    protected static $_instance = null;
    private $options_name = 'hu_options_framework_main_options';
    
    /**
    * get instance
    *
    * Falls die einzige Instanz noch nicht existiert, erstelle sie
    * Gebe die einzige Instanz dann zurück
    *
    * @return   Singleton
    */
    public static function getInstance(){
        if (null === self::$_instance){
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    /**
    * clone
    *
    * Kopieren der Instanz von aussen ebenfalls verbieten
    */
    protected function __clone() {}
   
    /**
    * constructor
    *
    * externe Instanzierung verbieten
    */
    protected function __construct() {
        add_action( 'cmb2_admin_init', array( $this, 'register_options' ) );
    }

    public function get_main_options_name(){
        return $this->options_name;
    }

    public function _add_legacy_fields_to_main_options_page( $cmb_options_main_page ){
        $main_options_page_fields = apply_filters( 'huishu_options_framework_main_page_fields', array() );
        $main_options_even_older = apply_filters( 'huishu_framework_options_page_options', array() );
        $main_options_page_fields = array_merge( $main_options_page_fields, $main_options_even_older );
        $group_fields = array();
        foreach($main_options_page_fields as $main_option){
            if(($main_option['type'] == 'group') && isset($main_option['groupfields']) && !empty($main_option['groupfields'])){
                $group_fields[$this->get_main_options_name()][$main_option['id']] = $cmb_options_main_page->add_field($main_option);
                foreach($main_option['groupfields'] as $groupfield){
                    $cmb_options_main_page->add_group_field($group_fields[$this->get_main_options_name()][$main_option['id']], $groupfield);
                }
            } else {
                $cmb_options_main_page->add_field($main_option);
            }
        }
    }

    public function _add_legacy_options_pages( $main_options_page_key ){
        $options_pages = apply_filters( 'huishu_options_framework_options_pages', array() );
        $cmb_additional_options = array();
        foreach($options_pages as $options_page){
            $title = $options_page['title'];
            $key = $options_page['options_key'];
            $cap = $options_page['capability'] ?? 'manage_options';
            $cmb_additional_options[$key] = new_cmb2_box( array(
                'id'           => 'hu_options_framework_options_'.$key.'_metabox',
                'title'        => esc_html__( $title, 'myprefix' ),
                'object_types' => array( 'options-page' ),
                'option_key'      => $key, // The option key and admin menu page slug.
                // 'icon_url'        => 'dashicons-palmtree', // Menu icon. Only applicable if 'parent_slug' is left empty.
                // 'menu_title'      => esc_html__( 'Options', 'myprefix' ), // Falls back to 'title' (above).
                 'parent_slug'     => $main_options_page_key, // Make options page a submenu item of the themes menu.
                 'capability'      => $cap, // Cap required to view options-page.
                // 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
                // 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
                // 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
                // 'save_button'     => esc_html__( 'Save Theme Options', 'myprefix' ), // The text for the options-page save button. Defaults to 'Save'.
            ) );
            foreach($options_page['fields'] as $options_field){
                if(($options_field['type'] == 'group') && isset($options_field['groupfields']) && !empty($options_field['groupfields'])){
					$group_fields[$key][$options_field['id']] = $cmb_additional_options[$key]->add_field($options_field);
					foreach($options_field['groupfields'] as $groupfield){
						$cmb_additional_options[$key]->add_group_field($group_fields[$key][$options_field['id']],$groupfield);
					}
				} else {
					$cmb_additional_options[$key]->add_field($options_field);
				}
            }
        }
    }

    public function register_options(){

        if( has_filter( 'huishu_options_framework_main_page_fields' ) ) {
            add_action( 'huishu_options_framework_do_additional_fields', array( $this, '_add_legacy_fields_to_main_options_page' ) );
        } 
        if( has_filter('huishu_options_framework_options_pages') ) {
            add_action( 'huishu_options_framework_do_additional_pages', array( $this, '_add_legacy_options_pages' ) );
        }

        $main_options_title = __(apply_filters('huishu_options_framework_main_page_title','Zusätzliche Optionen'));
        $main_options_capability = apply_filters('huishu_options_framework_main_page_capability','manage_options');
        
        if( ! ( has_action( 'huishu_options_framework_do_additional_fields' ) || has_action( 'huishu_options_framework_do_additional_pages' ) ) ){
            return;
        }

        $cmb_options_main_page = new_cmb2_box( array(
			'id'                => 'huishu_options_framework_options_metabox',
			'title'             => esc_html( $main_options_title ),
			'object_types'      => array( 'options-page' ),
			'option_key'        => $this->get_main_options_name(), // The option key and admin menu page slug.
			'capability'        => $main_options_capability, // Cap required to view options-page.
            // 'icon_url'        => 'dashicons-palmtree', // Menu icon. Only applicable if 'parent_slug' is left empty.
		) );

        do_action( 'huishu_options_framework_do_additional_fields', $cmb_options_main_page );

        do_action( 'huishu_options_framework_do_additional_pages', $this->get_main_options_name() );
        
    }

    public function get_main_option( $key = '', $default = false ){
        return $this->get_option( $this->get_main_options_name(), $key, $default );
    }

    public function get_option( $option, $key = '', $default = false ) {
        if ( function_exists( 'cmb2_get_option' ) ) {
            // Use cmb2_get_option as it passes through some key filters.
            return cmb2_get_option( $option, $key, $default );
        }
        // Fallback to get_option if CMB2 is not loaded yet.
        $opts = get_option( $option, $default );
        $val = $default;
        if ( 'all' == $key ) {
            $val = $opts;
        } elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
            $val = $opts[ $key ];
        }
        return $val;
    }
}

function hu_options_framework(){
    return HUisHUOptionsFramework::getInstance();
}

if( ! function_exists( 'huishu_framework_get_options_page_options' ) ){
    function huishu_framework_get_options_page_options(){
    }
}

if( ! function_exists( 'huishu_get_custom_theme_options_name' ) ){
    function huishu_get_custom_theme_options_name(){
        return hu_options_framework()->get_main_options_name();
    }
}
