<?php // (C) Copyright Bobbing Wide 2016, 2017
if ( !defined( "CLASS_OIK_PLUGIN_UPDATE_INCLUDED" ) ) {
define( "CLASS_OIK_PLUGIN_UPDATE_INCLUDED", "0.1.1" );

/**
 * Implements oik's plugin update logic
 *
 * Class: OIK_Plugin_Update
 
 *
 * Note: We use bw_trace2() and bw_backtrace() but can't use trace levels since we don't know if trace is actually available. 
 * @TODO: Confirm the above!
 * 
 * We also use oik library functions with bootstrapping logic 
 */
class OIK_Plugin_Update {

	/**
	 * @var $instance - the true instance
	 */
	private static $instance;

	/**
	 * Return the single instance of this class
	 */
	public static function instance() {
		if ( !isset( self::$instance ) && !( self::$instance instanceof self ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/** 
	 * Constructor for OIK_Plugin_Update
	 * 
	 */
	function __construct() {
	}
	
	/**
	 * Display an "update" message
	 * 
	 * @param string $text the message to display
	 * @return string the generated HTML
	 */								 
	function show_update_message( $text ) {
		$message = '<tr class="plugin-update-tr">';
		$message .= '<td colspan="3" class="plugin-update colspanchange">';
		$message .= '<div class="update-message">';
		$message .= $text;
		$message .= "</div>";
		$message .= "</td>";
		$message .= "</tr>";
		echo $message;
	}
	
	/**
	 * Display an "update-nag" message
	 */
	function show_update_nag( $text ) {
		$message = '<div class="update-nag">';
		$message .= $text;
		$message .= "</div>";
		echo $message;
	}
	
	/**
	 * Implement "after_plugin_row" for this plugin
	 * 
	 * Quick and dirty solution to decide if data migration is required
	 * and if so produce a link to the Migration settings page.
	 */
	function after_plugin_row( $plugin_file, $plugin_data, $status ) {
		bw_trace2();
		$message = "Use oik to check for updates";
		$this->show_update_message( "$message" );
	}
	
	/**
	 * Returns admin page URL 
	 */
	public static function get_page_url() {
		$args = array( 'page' => 'oik_plugins' );
		$url = add_query_arg( $args, admin_url( "admin.php" ) );
		return $url;
	}

	/**
	 * Implement "plugin_action_links" 
	 * 
	 * @TODO Determine if it's really necessary to create the link to the "oik plugins" page.
	 * Answer: No, it's not. It's confusing
	 *
	 */
	function plugin_action_links( $links, $file, $plugin_data, $context ) {
		bw_trace2();
		//bw_trace2( plugin_basename( __FILE__ ), "plugin_basename" );
		//if ( $file == plugin_basename( __FILE__ ) ) {
		//	$links['update'] =  '<a href="' . esc_url( self::get_page_url() ) . '">'.esc_html__( 'Settings' , 'oik-weight-zone-shipping-pro').'</a>';
		//}
		return( $links );
	}
	
	/**
	 * Query if the primary menu exists
	 *
	 * @param string $menu_slug e.g. "oik_menu"
	 * @return bool true if the menu item exists
	 */
	function query_menu( $menu_slug ) {
		global $submenu;
		$menu_exists = false;
		if ( is_array( $submenu ) ) {
			$menu_exists = array_key_exists( $menu_slug, $submenu );
		}	
		return $menu_exists;
	} 
	
	/**
	 * Query if the menu subitem exists
	 * 
	 * We probably want to use get_plugin_page_hook()
	 *
	 * @param string $menu_slug e.g. "oik_menu"
	 * @param string $sub_item e.g. "oik_themes"
	 * @return string|null the hookname it's registered
	 */
	function query_menu_subitem( $menu_slug, $parent ) {
		$hookname = get_plugin_page_hook( $menu_slug, $parent );
		return( $hookname );
	}
	
	/**
	 * Add the oik updates and Updates menu items if required
	 * 
	 * If oik or another plugin or theme has already added the oik_menu then we don't need to
	 * Otherwise we add the primary menu item and its first child
	 */
	function add_oik_menu() {
		$menu_slug = $this->query_menu( "oik_menu" );
		if ( !$menu_slug ) {
			$hook = add_menu_page( __('[oik] Options', null), __('oik updates', null), 'manage_options', 'oik_menu', array( $this, "oik_menu" ) );
			$hook = add_submenu_page( 'oik_menu', __( 'oik updates', null ), __( 'Updates', null), 'manage_options', 'oik_menu', array( $this, "oik_menu" ) );
		}
	}

	/**
	 * Prepares for converting local requests to use sslverify=false
	 * 
	 * When https is being used and we're operating on a local server then we need to cater for SSL certificate verification problems.
	 * 
	 */ 								 
	function admin_action_upgrade_plugin() {
		add_filter( "http_request_args", array( "oik_remote", "bw_adjust_args" ), 10, 2 );
	}

	/**
	 * Implement "admin_menu" for plugin updates 
	 *
	 * - We need to add the oik_plugins submenu if it's not already present.
	 * - We may need to create the oik menu and its first submenu item.
	 *
	 */
	function admin_menu() {
		$plugins_slug = $this->query_menu_subitem( "oik_plugins", "oik_menu" );
		if ( !$plugins_slug ) {
			$loaded = $this->bootstrap_oik_libs();
			if ( $loaded ) {
				$dependencies = array( "class-bobbcomp" => "3.1.0" 
														 , "bobbfunc" => "3.2.0"
														 , "class-oik-update" => "3.0.1"
														 , "oik-l10n" => "3.2.0" 
														 , "class-BW-" => "3.2.0"
														 , "class-oik-remote" => "0.1.0"
														 );
				$loaded = $this->require_dependencies( $dependencies ); 
				if ( $loaded ) {
					oik_l10n_enable_jti();
					$this->add_oik_menu();
					add_submenu_page( 'oik_menu', __( 'oik plugins', null ), __('Plugins', null), 'manage_options', 'oik_plugins', array( $this, 'oik_plugins_do_page' ) );
					$this->admin_action_upgrade_plugin();
					do_action( "oik_register_plugin_server" );
				}
			}	
		}		
	}

	/**
	 * Implement oik's Plugins page
	 *
	 * This allows oik-weight-zone-shipping-pro and other oik plugins to 
	 * implement automatic / manual updates from an oik-plugins server.
	 *
	 * The logic caters for oik, or another plugin, already providing the logic. See "admin_menu".
	 * 
	 * When we've 
	 */
	function oik_plugins_do_page() {
		$loaded = $this->bootstrap_oik_libs();
		if ( $loaded ) {
			$dependencies = array( "class-bobbcomp" => "3.1.0" 
													 , "bobbfunc" => "3.2.0"
													 , "bobbforms" => "3.2.0"
													 , "oik-admin" => "3.2.0"
													 , "oik-depends" => "3.2.0"
													 , "oik_plugins" => "0.2.0"
													 , "class-BW-" => "3.2.0"
													 );
			
			$loaded = $this->require_dependencies( $dependencies ); 
			
			//$bobbcomp = $this->require_lib( "class-bobbcomp", "0.0.1" );
			//if ( $bobbcomp ) {
			//		$bobbfunc = $this->require_lib( "bobbfunc", "3.0.0" );
			//		if ( $bobbfunc ) { 
			//			$bobbforms = $this->require_lib( "bobbforms", "3.0.1" );
			//				if ( $bobbforms ) {
			//					$admin = $this->require_lib( "oik-admin", "3.0.1" );
			//					if ( $admin ) {
			//						$depends = $this->require_lib( "oik-depends", "3.1.0" ); 
			//						if ( $depends ) {
			//							$plugins = $this->require_lib( "oik_plugins", "0.1.0" );
			//							if ( $plugins ) {
			if ( $loaded ) { 
			 						
				//oik_l10n_enable_jti();
				oik_lazy_plugins_server_settings();
				bw_flush()	;
			} else {
				$this->show_update_nag( "eh?" );
			}
		}		
	}
	
	/**
	 * Load the dependent libraries
	 * 
	 * @param array of dependent libraries and minumum required versions
	 * @return bool|null 
	 */
	function require_dependencies( $dependencies ) {
		foreach ( $dependencies as $lib => $version ) {
			$loaded = $this->require_lib( $lib, $version );
			if ( !$loaded ) {
				break;
			}
		}
		return( $loaded );
	}
	
	/**
	 * Require a library
	 * 
	 * @param string $lib library name
	 * @param string $version the required minimum version
	 * @return bool true if the required version has been loaded
	 */
	function require_lib( $lib, $version='*' ) {
		$lib_loaded = oik_require_lib( $lib );
		if ( $lib_loaded && !is_wp_error( $lib_loaded ) ) {
			$lib_loaded = $this->version_check( $lib, $version );
		} else {
			bw_trace2( $lib_loaded, "require_lib error", true );
			bw_backtrace();
			$this->show_update_nag( "Failed to load library: $lib. version: $version ");
			
			$lib_loaded = null;
		}
		return( $lib_loaded );
	}
	
	/**
	 * Check minimum required version loaded
	 * 
	 * 
	 */
	function version_check( $lib, $version='*' ) {
		$constant_name = str_replace( "-", "_", $lib );
		$constant_name = strtoupper( $constant_name );
		$constant_name .= '_INCLUDED';
		if ( defined( $constant_name ) ) {
			$current_version = constant( $constant_name );
			$acceptable = $this->compatible_version( $current_version, $version );
		} else {
			$current_version = "unknown";
			$acceptable = false;
		}
		if ( !$acceptable ) { 
			$this->report_error( "Incompatible version already loaded. Library: $lib. Current version: $current_version. Required version: $version" );
		}
		return( $acceptable );
	}

	function report_error( $text ) {
		$this->show_update_nag( $text );
	}

	/**
	 * Checks compatible versions
	 *
	 */
	function compatible_version( $current_version, $required_version ) {
    bw_trace2( null, null, true, BW_TRACE_VERBOSE );
    if ( "*" != $required_version ) {
      $version_compare = version_compare( $current_version, $required_version );
      $acceptable = false;
      bw_trace2( $version_compare, "version compare", false, BW_TRACE_VERBOSE );
      switch ( $version_compare ) {
        case 0:
            $acceptable = true;
          break;
        case -1:
          break;
          
        default:
          // Now we have to check semantic versioning
          // but in the mean time pretend it's acceptable
          $acceptable = true;
      }
        
    } else { 
      $acceptable = true;
    }
    return( $acceptable );
  }

	/**
	 * Bootstrap the shared library system
	 *
	 */
	function bootstrap_oik_libs() {
		$loaded = false;
		if ( function_exists( "oik_require_lib" ) ) {
			$loaded = true;
		} else {
			require_once( __DIR__ . "/oik_boot.php" );
			$loaded = true;
		}
		
		if ( $loaded && function_exists( "oik_lib_fallback" ) ) {
			oik_lib_fallback( __DIR__ );
			oik_init();
		}
		//echo "Loaded!";
		//print_r( get_included_files() );
		return( $loaded );
	}
	
	/**
	 * Implement oik_menu for oik_themes
	 * 
	 * This page should be similar to oik_server
	 */
	function oik_menu() {
		$this->oik_plugins_servers();
		//do_action( "oik_menu_box" );
	}
	
	/**
	 * Display the general messages about Updates
	 */
	function oik_plugins_servers() {
		//oik_l10n_enable_jti();
		BW_::p( __( "Some oik plugins and themes are supported from servers other than WordPress.org", null ) );
		BW_::p( __( "Premium plugin and theme versions require API keys.", null ) );
		
		if ( $this->query_menu_subitem( "oik_plugins", "oik_menu" ) ) {
			BW_::p( __( "Use the Plugins page to manage oik plugins servers and API keys", null ) );
      BW_::alink( "button-secondary", admin_url("admin.php?page=oik_plugins"), __( "Plugins", null) , __( "Manage plugin servers and API keys", null ) );
		}
		if ( $this->query_menu_subitem( "oik_themes", "oik_menu" ) ) {
			BW_::p( __( "Use the Themes page to manage oik themes servers and API keys", null ) );
			BW_::alink( "button-secondary", admin_url("admin.php?page=oik_themes"), __( "Themes", null), __( "Manage theme servers and API keys", null ) );
		}
		bw_flush();
	}

}

} /* end if !defined */
