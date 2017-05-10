<?php
/*
 * Plugin Name: Podcasting plugin
 * Description: My First task and First Plugin
 * Plugin URI: 
 * Author: Starskie Villanueva
 * Author URI: 
 * Version: 0.1
 * Text Domain: dx-sample-plugin
*/

/**
 * Get some constants ready for paths when your plugin grows 
 * 
 */
 


define( 'DXP_VERSION', '0.1' );
define( 'DXP_PATH', dirname( __FILE__ ) );
define( 'DXP_PATH_INCLUDES', dirname( __FILE__ ) . '/inc' );
define( 'DXP_FOLDER', basename( DXP_PATH ) );
define( 'DXP_URL', plugins_url() . '/' . DXP_FOLDER );
define( 'DXP_URL_INCLUDES', DXP_URL . '/inc' );
require( 'archive.php' );

/*  The plugin base class - the root of all WP goods!  */
class DX_Plugin_Base {
	
	/*  Assign everything as a call from within the constructor */
		public function __construct() {
					
		// register meta boxes for Pages (could be replicated for posts and custom post types)
		add_action( 'add_meta_boxes', array( $this, 'dx_meta_boxes_callback' ) );
		
		// register save_post hooks for saving the custom fields
		add_action( 'save_post', array( $this, 'dx_save_sample_field' ) );
		
		
		// Register custom post types 
		add_action( 'init', array( $this, 'dx_custom_post_types_callback' ), 5 );
		
		
		// Register activation and deactivation hooks
		register_activation_hook( __FILE__, 'dx_on_activate_callback' );
		register_deactivation_hook( __FILE__, 'dx_on_deactivate_callback' );
		
		// Translation-ready
		add_action( 'plugins_loaded', array( $this, 'dx_add_textdomain' ) );
		
						
	}	
	
		/*
	 Adding JavaScript scripts
	 Loading existing scripts from wp-includes or adding custom ones 
	 */
	public function dx_add_JS() {
		wp_enqueue_script( 'jquery' );
		// load custom JSes and put them in footer
		wp_register_script( 'samplescript', plugins_url( '/js/samplescript.js' , __FILE__ ), array('jquery'), '1.0', true );
		wp_enqueue_script( 'samplescript' );
	}
	
	
	/*
	  Adding JavaScript scripts for the admin pages only
	  Loading existing scripts from wp-includes or adding custom ones 
	 */
	public function dx_add_admin_JS( $hook ) {
		wp_enqueue_script( 'jquery' );
		wp_register_script( 'samplescript-admin', plugins_url( '/js/samplescript-admin.js' , __FILE__ ), array('jquery'), '1.0', true );
		wp_enqueue_script( 'samplescript-admin' );
	}
	
	/*  Add CSS styles  */
	public function dx_add_CSS() {
		wp_register_style( 'samplestyle', plugins_url( '/css/samplestyle.css', __FILE__ ), array(), '1.0', 'screen' );
		wp_enqueue_style( 'samplestyle' );
	}
	
	/*  Add admin CSS styles - available only on admin  */
	public function dx_add_admin_CSS( $hook ) {
		wp_register_style( 'samplestyle-admin', plugins_url( '/css/samplestyle-admin.css', __FILE__ ), array(), '1.0', 'screen' );
		wp_enqueue_style( 'samplestyle-admin' );
		
		if( 'toplevel_page_dx-plugin-base' === $hook ) {
			wp_register_style('dx_help_page',  plugins_url( '/help-page.css', __FILE__ ) );
			wp_enqueue_style('dx_help_page');
		}
	}
	
	/* Callback for registering pages 
	  This demo registers a custom page for the plugin and a subpage  
	 */
	public function dx_admin_pages_callback() {
		add_menu_page(__( "Plugin Base Admin", 'dxbase' ), __( "Plugin Base Admin", 'dxbase' ), 'edit_themes', 'podcast-plugin', array( $this, 'dx_plugin_base' ) );		
		add_submenu_page( 'podcast-plugin', __( "Base Subpage", 'dxbase' ), __( "Base Subpage", 'dxbase' ), 'edit_themes', 'dx-base-subpage', array( $this, 'dx_plugin_subpage' ) );
		add_submenu_page( 'podcast-plugin', __( "Remote Subpage", 'dxbase' ), __( "Remote Subpage", 'dxbase' ), 'edit_themes', 'dx-remote-subpage', array( $this, 'dx_plugin_side_access_page' ) );
	}
	
	
	/*  Adding right and bottom meta boxes to Pages   */
	public function dx_meta_boxes_callback() {
		// register side box
		add_meta_box( 
		        'dx_bottom_meta_box',
		        __( "Meta box", 'dxbase' ),
		        array( $this, 'dx_bottom_meta_box' ),
		        ''
		        );
		  	}
	
	public function dx_bottom_meta_box( $post, $metabox) {
		_e("", 'dxbase');
		
		// Add some test data here - a custom field, that is
		$dx_test_input = '';
		if ( ! empty ( $post ) ) {
		$dx_test_input = get_post_meta( $post->ID, 'dx_test_input', true );
			
		//   Adding input text for audio and text area for Episode notes
		}
		?>
		<label for="dx-test-input">Audio Input<label for="dx-test-input">
		</br>
		<input type="text" id="dx-test-input" name="dx_test_input" value="<?php echo $dx_test_input; ?>" />
		</br></br>
		<label for="dx-test-input">Episode Notes<label for="dx-test-input">
		</br>
		<textarea name="TextArea1" cols="100" rows="3" value="<?php echo $dx_test_input; ?>"></textarea>		
		<?php
		
		var_dump($dx_test_input);
	}
		
	/*
	  Save the custom field from the side metabox
	  @param $post_id the current post ID
	  @return post_id the post ID from the input arguments
	  
	 */
	public function dx_save_sample_field( $post_id ) {
		// Avoid autosaves
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$slug = 'pluginbase';
		// If this isn't a 'book' post, don't update it.
		if ( ! isset( $_POST['post_type'] ) || $slug != $_POST['post_type'] ) {
			return;
		}
		
		// If the custom field is found, update the postmeta record
		// Also, filter the HTML just to be safe
		if ( isset( $_POST['dx_test_input']  ) ) {
			update_post_meta( $post_id, 'dx_test_input',  esc_html( $_POST['dx_test_input'] ) );
		}
	}
	
	/*   Register custom post types */
	public function dx_custom_post_types_callback() {
		register_post_type( 'pluginbase', array(
			'labels' => array(
				'name' => __("Podcast", 'post type general name' , 'dxbase'),
				'singular_name' => __('Podcast', 'post type singular name' , 'dxbase'),
				'add_new' => _x("Add New", 'pluginbase', 'dxbase' ),
				'add_new_item' => sprintf( __( 'Add New %s' , 'dxbase' ), __( 'Episode' , 'dxbase' ) ),
				'edit_item' => sprintf( __( 'Edit %s' , 'dxbase' ), __( 'Episode' , 'dxbase' ) ),				
				'new_item' => sprintf( __( 'New %s' , 'dxbase' ), __( 'Episode' , 'dxbase' ) ),
				'all_items' => sprintf( __( 'All %s' , 'dxbase' ), __( 'Episodes' , 'dxbase' ) ),
				'view_item' => sprintf( __( 'View %s' , 'dxbase' ), __( 'Episode' , 'dxbase' ) ),
				'search_items' => sprintf( __( 'Search %a' , 'dxbase' ), __( 'Episodes' , 'dxbase' ) ),
				'not_found' =>  sprintf( __( 'No %s Found' , 'dxbase' ), __( 'Episodes' , 'dxbase' ) ),
				'not_found_in_trash' => sprintf( __( 'No %s Found In Trash' , 'dxbase' ), __( 'Episodes' , 'dxbase' ) ),
				'parent_item_colon' => '',
				'menu_name' => __( 'Podcast' , 'dxbase' ),
				'filter_items_list' => sprintf( __( 'Filter %s list' , 'dxbase' ), __( 'Episode' , 'dxbase' ) ),
				'items_list_navigation' => sprintf( __( '%s list navigation' , 'dxbase' ), __( 'Episode' , 'dxbase' ) ),
				'items_list' => sprintf( __( '%s list' , 'dxbase' ), __( 'Episode' , 'dxbase' ) ),
			),
			
			'menu_icon' => 'dashicons-microphone',
			'public' => true,
			'publicly_queryable' => true,
			'query_var' => true,
			'rewrite' => true,
			'exclude_from_search' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'has_archive' => true,
			'menu_position' => 40,
			'supports' => array(
				'title',
				'editor',
				'thumbnail',
				'custom-fields',
				'page-attributes',
			),
			
		));	
	}
	

	/* 
		Initialize the Settings class
		Register a settings section with a field for a secure WordPress admin option creation. 
	*/
	public function dx_register_settings() {
		require_once( DXP_PATH . '/dx-plugin-settings.class.php' );
		new DX_Plugin_Settings();
	}
	/*   Add textdomain for plugin  */
	public function dx_add_textdomain() {
		load_plugin_textdomain( 'dxbase', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}
		
	/*  Callback for getting a URL and fetching it's content in the admin page  */
}


/**
 * Register activation hook
 *
 */
function dx_on_activate_callback() {
	// do something on activation
}

/**
 * Register deactivation hook
 *
 */
function dx_on_deactivate_callback() {
	// do something when deactivated
}

// Initialize everything
$dx_plugin_base = new DX_Plugin_Base();
