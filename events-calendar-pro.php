<?php
/*
 Plugin Name: Events Calendar Pro
 Description: The Events Calendar Pro Premium plugin enables recurring events, custom meta, and other premium features for The Events Calendar plugin 
 Version: 2.0
 Author: Modern Tribe, Inc.
 Author URI: http://tribe.pro/
 Text Domain: events-calendar-pro
 */

if ( !class_exists( 'TribeEventsPro' ) ) {
	class TribeEventsPro {

		const PLUGIN_DOMAIN = 'events-calendar-pro';

	    private static $instance;

		//instance variables
		public $pluginDir;
		public $pluginPath;
		public $pluginUrl;
		public static $updateUrl = 'http://tribe.pro/';
		
	    private function __construct()
	    {
			$this->pluginDir = trailingslashit( basename( dirname(__FILE__) ) );
			$this->pluginPath = trailingslashit( dirname(__FILE__) );
			$this->pluginUrl = WP_PLUGIN_URL.'/'.$this->pluginDir;
			if (defined('TRIBE_UPDATE_URL')) { self::$updateUrl = TRIBE_UPDATE_URL; }
			
			require_once( 'lib/tribe-date-series-rules.class.php' );
			require_once( 'lib/tribe-ecp-custom-meta.class.php' );
			require_once( 'lib/tribe-events-recurrence-meta.class.php' );
			require_once( 'lib/tribe-recurrence.class.php' );
			require_once( 'lib/tribe-support.class.php' );
			require_once( 'lib/widget-calendar.class.php' );
			require_once( 'template-tags.php' );
			require_once( 'lib/plugins/pue-client.php' );
         // Advanced Post Manager
         require_once( 'vendor/advanced-post-manager/tribe-apm.php' );
         require_once( 'lib/apm_filters.php');

         // Next Event Widget
         require_once( 'lib/widget-featured.class.php');
			
			add_action( 'init', array( $this, 'init' ), 10 );			
         add_action( 'init', array( $this, 'enqueue_resources') );
         add_action( 'tribe_after_location_details', array( $this, 'add_google_map_preview') );
         add_filter( 'tribe_current_events_page_template', array( $this, 'select_venue_template' ) );
         add_filter( 'tribe_events_template_single-venue.php', array( $this, 'load_venue_template' ) );
	    }
		
		public function init() {
			TribeEventsCustomMeta::init();
			TribeEventsRecurrenceMeta::init();
			new PluginUpdateEngineChecker(self::$updateUrl, self::PLUGIN_DOMAIN, array('apikey'=>'ec94dc0f20324d00831a56b3013f428a'));
		}

      public function select_venue_template($template) {
	      if ( is_singular( TribeEvents::VENUE_POST_TYPE ) ) {
	         return TribeEventsTemplates::getTemplateHierarchy('single-venue');
	      }

         return $template;
      }

      public function load_venue_template($file) {
         if ( !file_exists($file) ) {
            $file = $this->pluginPath . 'views/single-venue.php';
         }

         return $file;
      }

      public function add_google_map_preview($postId) {
         if( tribe_get_option('embedGoogleMaps') ) {
            ?><div style="float:right;"><?php
               echo tribe_get_embedded_map($postId, 200, 200, true);
            ?></div><?php
         }
         ?><div style="clear:both"></div><?php
      }

      public function enqueue_resources() {
         if( is_admin() ) {
            wp_enqueue_script( TribeEvents::POSTTYPE.'-premium-admin', $this->pluginUrl . 'resources/events-admin.js', array('jquery-ui-datepicker'), '', true );
         }
      }
	
		/* Static Methods */
	    public static function instance()
	    {
	        if (!isset(self::$instance)) {
	            $className = __CLASS__;
	            self::$instance = new $className;
	        }

	        return self::$instance;
	    }
		
		/**
		 * check_for_ecp
		 *
		 * Check that the required minimum version of the base events plugin is activated.
		 * 
		 * @author John Gadbois 
		 */
		public static function check_for_ecp() {
			if( !class_exists( 'TribeEvents' ) || !defined('TribeEvents::VERSION') || !version_compare( TribeEvents::VERSION, '2.0', '>=') ) {
				deactivate_plugins(basename(__FILE__)); // Deactivate ourself
				wp_die("Sorry, but you must activate The Events Calendar 2.0 or greater in order for this plugin to be installed.");	
			}
		}
	}
	
	register_activation_hook( __FILE__, array('TribeEventsPro', 'check_for_ecp') );	

	// Instantiate class and set up WordPress actions.
	TribeEventsPro::instance();
}
?>
