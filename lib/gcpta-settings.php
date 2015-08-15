<?php         

/**
 * Genesis Custom Post Types Archives Settings Page
 * Requires Genesis 1.8 or later
 *
 * This file registers all of this child theme's 
 * specific Theme Settings, accessible from
 * Genesis > Portfolio Settings.
 *
 * @package      WPS_GCPTA
 * @author       Travis Smith <travis@wpsmith.net>
 * @copyright    Copyright (c) 2012, Travis Smith
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        0.1
 * @alter        3.1.2012
 *
 */
 
 
add_action( 'admin_menu', 'gcpta_admin_init', 5 );
/**
 * Add the CPT Settings Page
 *
 * @since 1.0.0
 */
function gcpta_admin_init() {
	global $_wps_gcpta;
	
	$pt_args = apply_filters( 'gcpta_pt_args' , array( 'public' => true, 'capability_type' => 'post', '_builtin' => false, 'has_archive' => true, 'show_ui' => true ) );
	$pts = get_post_types( $pt_args , 'names', 'and' );
	
	foreach ( $pts  as $pt ) {
		
		// Specify a unique page ID.
		$page_id = 'gcpta-' . $pt;
		
		// Set it as a child to genesis, and define the menu and page titles
		$menu_ops = array(
			'submenu' => array(
				'parent_slug' => 'edit.php?post_type=' . $pt,
				'page_title'  => __( 'CPTs Archives', GCPTA_DOMAIN ),
				'menu_title'  => __( 'CPTs Archives', GCPTA_DOMAIN ),
				'capability' => 'manage_options',
			)
		);
		
		$pt_var = '_wps_gcpta_'.$pt;
		$post_type = get_post_type_object( $pt );
		
		// Create the Admin Pages	
		${$pt_var} = new WPS_GCPTA( $page_id, $menu_ops , $post_type );

	}
	
}


/**
 * Registers a new admin page, providing content and corresponding menu item
 * for the Child Theme Settings page.
 *
 *
 * @since 1.0.0
 */
class WPS_GCPTA extends Genesis_Admin_Boxes {
	
	protected $post_type;
	/**
	 * Create an admin menu item and settings page.
	 * 
	 * @since 1.0.0
	 */
	function __construct( $page_id, $menu_ops, $pt ) {
		$this->post_type = $pt;
		
		// Set up page options. These are optional, so only uncomment if you want to change the defaults
		$page_ops = array(
			//'screen_icon'       => 'custom',
			'screen_icon'       => 'plugins',
		);		
		
		// Give it a unique settings field. 
		// You'll access them from genesis_get_option( 'option_name', 'gcpta-settings' );
		$settings_field = 'gcpta-settings-' . $pt->name;
		
		// Set the default values
			$default_settings['gcpta_intro_' . $pt->name] = true;
			$default_settings['gcpta_ss_' . $pt->name]    = false;
			$default_settings['gcpta_headline_' . $pt->name] = $pt->label;
			$default_settings['gcpta_intro_content_' . $pt->name] = false;
			$default_settings['gcpta_doctitle_' . $pt->name] = genesis_get_seo_option( 'home_doctitle' ) ? genesis_get_seo_option( 'home_doctitle' ) : '';
			$default_settings['gcpta_description_' . $pt->name] = genesis_get_seo_option( 'home_description' ) ? genesis_get_seo_option( 'home_description' ) : '';
			$default_settings['gcpta_keywords_' . $pt->name] = genesis_get_seo_option( 'home_keywords' ) ? genesis_get_seo_option( 'home_keywords' ) : '';
			$default_settings['gcpta_noindex_' . $pt->name] = genesis_get_seo_option( 'home_noindex' ) ? 1 : 0;
			$default_settings['gcpta_nofollow_' . $pt->name] = genesis_get_seo_option( 'home_nofollow' ) ? 1 : 0;
			$default_settings['gcpta_noarchive_' . $pt->name] = genesis_get_seo_option( 'home_noarchive' ) ? 1 : 0;
			$default_settings['gcpta_remove_post_meta_' . $pt->name] = 0;
			$default_settings['gcpta_remove_post_info_' . $pt->name] = 0;
			$default_settings['gcpta_post_info_' . $pt->name] = '[post_date] ' . __('By', GCPTA_DOMAIN) . ' [post_author_posts_link] [post_comments] [post_edit]';
			$default_settings['gcpta_post_meta_' . $pt->name] = '[post_categories] [post_tags]';
			
			$default_settings['gcpta_features_' . $pt->name] = 0;
			$default_settings['gcpta_features_content_limit_' . $pt->name] = 0;
			$default_settings['gcpta_features_image_size_' . $pt->name] = '';
			$default_settings['gcpta_features_image_class_' . $pt->name] = '';
			$default_settings['gcpta_grid_posts_' . $pt->name] = 6;
			$default_settings['gcpta_grid_content_limit_' . $pt->name] = 250;
			$default_settings['gcpta_grid_image_size_' . $pt->name] = '';
			$default_settings['gcpta_grid_image_class_' . $pt->name] = '';
			$default_settings['gcpta_grid_read_more_' . $pt->name] = __( 'Read', GCPTA_DOMAIN );
			$default_settings['gcpta_grid_columns_' . $pt->name] = 2;
			
			$default_settings['gcpta_loop_' . $pt->name] = '';
			$default_settings['gcpta_content_archive_' . $pt->name] = '';
			$default_settings['gcpta_content_archive_limit_' . $pt->name] = 0;
			$default_settings['gcpta_content_archive_thumbnail_' . $pt->name] = 0;
			$default_settings['gcpta_image_size_' . $pt->name] = '';
			
			$default_settings['gcpta_ss_sidebar_' . $pt->name] = '';
			$default_settings['gcpta_ss_sidebar_alt_' . $pt->name] = '';
		
		// Create the Admin Page
		$this->create( $page_id, $menu_ops, $page_ops, $settings_field, $default_settings );
		
		// Add script
		add_action( 'admin_init', array( $this, 'admin_script' ) );
		
		// Initialize the Sanitization Filter
		add_action( 'genesis_settings_sanitizer_init', array( $this, 'sanitization_filters' ) );
		
		// Add custom screen icon
		//add_action( 'admin_head' , array( $this, 'screen_icon' ) );
			
	}
	
	/** 
	 * Custom Admin screen icon
	 *
	 * See /lib/classes/sanitization.php for all available filters.
	 *
	 * @since 1.0.0
	 */	
	public function screen_icon() { ?> 
	<style> 
		#icon-custom { background-image: url('<?php echo WPS_ADMIN_IMAGES . '/settings_32x32.png'; ?>'); background-repeat: no-repeat; }                
	</style> 
	<?php  
 	} 

	/** 
	 * Set up Sanitization Filters
	 *
	 * See /lib/classes/sanitization.php for all available filters.
	 *
	 * @since 1.0.0
	 */	
	function sanitization_filters() {
		// Set arrays
		$no_html = $one_zero = $unfiltered_html = $safe_html = array();
		
		// Set the default values
		$pt = $this->post_type->name;
		
			$no_html_filter[] = 'gcpta_intro_' . $pt;
			$no_html_filter[] = 'gcpta_doctitle_' . $pt;
			$no_html_filter[] = 'gcpta_description_' . $pt;
			$no_html_filter[] = 'gcpta_keywords_' . $pt;
			$no_html_filter[] = 'gcpta_loop_' . $pt;
			$no_html_filter[] = 'gcpta_content_archive_' . $pt;
			$no_html_filter[] = 'gcpta_content_archive_limit_' . $pt;
			$no_html_filter[] = 'gcpta_content_archive_thumbnail_' . $pt;
			$no_html_filter[] = 'gcpta_image_size_' . $pt;
			$no_html_filter[] = 'gcpta_features_' . $pt;
			$no_html_filter[] = 'gcpta_features_content_limit_' . $pt;
			$no_html_filter[] = 'gcpta_features_image_size_' . $pt;
			$no_html_filter[] = 'gcpta_features_image_class_' . $pt;
			$no_html_filter[] = 'gcpta_grid_posts_' . $pt;
			$no_html_filter[] = 'gcpta_grid_content_limit_' . $pt;
			$no_html_filter[] = 'gcpta_grid_image_size_' . $pt;
			$no_html_filter[] = 'gcpta_grid_image_class_' . $pt;
			$no_html_filter[] = 'gcpta_grid_columns_' . $pt;
			$no_html_filter[] = 'gcpta_ss_sidebar_' . $pt;
			$no_html_filter[] = 'gcpta_ss_sidebar_alt_' . $pt;
						
			$one_zero[] = 'gcpta_intro_' . $pt;
			$one_zero[] = 'gcpta_ss_' . $pt;
			$one_zero[] = 'gcpta_noindex_' . $pt;
			$one_zero[] = 'gcpta_nofollow_' . $pt;
			$one_zero[] = 'gcpta_noarchive_' . $pt;
			$one_zero[] = 'gcpta_remove_post_info_' . $pt;
			$one_zero[] = 'gcpta_remove_post_meta_' . $pt;
			
			$unfiltered_html[] = 'header_scripts_' . $pt;
			$unfiltered_html[] = 'footer_scripts_' . $pt;
			$unfiltered_html[] = 'gcpta_post_meta_' . $pt;
			$unfiltered_html[] = 'gcpta_post_info_' . $pt;
			
			$safe_html[] = 'gcpta_intro_headline_' . $pt;
			$safe_html[] = 'gcpta_headline_' . $pt;
			$safe_html[] = 'gcpta_intro_content_' . $pt;
			$safe_html[] = 'gcpta_grid_read_more_' . $pt;
		
		genesis_add_option_filter( 'no_html', $this->settings_field, $no_html_filter );
		genesis_add_option_filter( 'one_zero', $this->settings_field, $one_zero );
		genesis_add_option_filter( 'requires_unfiltered_html', $this->settings_field, $unfiltered_html );
		genesis_add_option_filter( 'safe_html', $this->settings_field, $safe_html );

	}
	
	/**
	 * Register metaboxes on Child Theme Settings page
	 *
	 * @since 1.0.0
	 *
	 * @see WPS_Portfolio_Settings::wps_minfolio_settings() Callback for child theme settings
	 * @see WPS_Portfolio_Settings::wps_portfolio_settings() Callback for portfolio settings
	 */
	function metaboxes() {

		add_meta_box( 'gcpta-settings', __( 'Genesis CPT Archives Settings' , GCPTA_DOMAIN ) , array( $this, 'settings_box' ), $this->pagehook, 'main', 'high' );
		add_meta_box( 'gcpta-seo-settings', __( 'Genesis CPT Archives SEO Settings' , GCPTA_DOMAIN ) , array( $this, 'seo_settings_box' ), $this->pagehook, 'main', 'high' );
		add_meta_box( 'gcpta-scripts', __( 'Genesis CPT Archives Scripts' , GCPTA_DOMAIN ) , array( $this, 'scripts_box' ), $this->pagehook, 'main', 'high' );
		add_meta_box( 'gcpta-layout', __( 'Genesis CPT Archives Layout' , GCPTA_DOMAIN ) , array( $this, 'layout_box' ), $this->pagehook, 'main', 'high' );
		
		// Simple Sidebars Support
		$supports = $this->get_field_value( 'gcpta_ss_' . $this->post_type->name );
		if ( $supports )
			add_post_type_support( $this->post_type->name, 'genesis-simple-sidebars' );
			
		if ( post_type_supports( $this->post_type->name, 'genesis-simple-sidebars' ) )
			add_meta_box( 'ss_inpost_metabox', __('Sidebar Selection', GCPTA_DOMAIN ), array( $this, 'ss_inpost_metabox' ), $this->pagehook, 'main', 'low' );
						
	}
	
	/**
	 * Register contextual help on Child Theme Settings page
	 *
	 * @since 1.0.0
	 *
	 */
	function help( ) {	
		global $my_admin_page;
		$screen = get_current_screen();
		
		if ( $screen->id != $this->pagehook )
			return;
		
		$tab1_help = 
			'<h3>' . __( 'H3 Heading' , GCPTA_DOMAIN ) . '</h3>' .
			'<p>' . __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur in odio lacus. Fusce lacinia viverra facilisis. Nunc urna lorem, tempus in sollicitudin ac, fringilla non lacus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Pellentesque lacinia, arcu ut porta laoreet, elit justo volutpat augue, commodo condimentum neque sapien a tellus. Fusce tempus elit sodales dui vehicula tempus. Aliquam lobortis laoreet tortor, facilisis blandit sem viverra at. Ut iaculis, metus ac faucibus aliquam, diam tortor commodo felis, sed fermentum velit nunc ac arcu. Ut in libero ante.' , GCPTA_DOMAIN ) . '</p>';
		
		
		$screen->add_help_tab( 
			array(
				'id'	=> $this->pagehook . '-tab1',
				'title'	=> __( 'Tab 1' , GCPTA_DOMAIN ),
				'content'	=> $tab1_help,
			) );
		
		// Add Genesis Sidebar
		$screen->set_help_sidebar(
                '<p><strong>' . __( 'For more information:', GCPTA_DOMAIN ) . '</strong></p>'.
                '<p><a href="' . __( 'http://www.studiopress.com/support', GCPTA_DOMAIN ) . '" target="_blank" title="' . __( 'Support Forums', GCPTA_DOMAIN ) . '">' . __( 'Support Forums', GCPTA_DOMAIN ) . '</a></p>'.
                '<p><a href="' . __( 'http://www.studiopress.com/tutorials', GCPTA_DOMAIN ) . '" target="_blank" title="' . __( 'Genesis Tutorials', GCPTA_DOMAIN ) . '">' . __( 'Genesis Tutorials', GCPTA_DOMAIN ) . '</a></p>'.
                '<p><a href="' . __( 'http://dev.studiopress.com/', GCPTA_DOMAIN ) . '" target="_blank" title="' . __( 'Genesis Developer Docs', GCPTA_DOMAIN ) . '">' . __( 'Genesis Developer Docs', GCPTA_DOMAIN ) . '</a></p>'.
				'<p><a href="' . __( 'http://wpsmith.net/genesis-plugins', GCPTA_DOMAIN ) . '" target="_blank" title="' . __( 'Genesis Plugins', GCPTA_DOMAIN ) . '">' . __( 'Genesis Plugins', GCPTA_DOMAIN ) . '</a></p>'.
				'<p><a href="' . __( 'http://wpsmith.net/category/genesis/', GCPTA_DOMAIN ) . '" target="_blank" title="' . __( 'Genesis Tutorials by WPSmith', GCPTA_DOMAIN ) . '">' . __( 'Genesis Tutorials by WPSmith', GCPTA_DOMAIN ) . '</a></p>'
        );
	}
	
	/**
	 * Enqueues admin script
	 *
	 * @since 1.0.0
	 *
	 */
	function admin_script() {
		wp_enqueue_script( 'gcpta-js', plugin_dir_url( __FILE__ ) . 'js/gcpta-admin.js', array( 'jquery' ), '', true );
	}
	
	/**
	 * Callback for minFolio Settings metabox
	 *
	 * @since 1.0.0
	 *
	 * @see WPS_GCPTA::metaboxes()
	 */
	function seo_settings_box() { 
		?>
		<fieldset>
			<p><span class="description"><?php _e( 'The Document Title is the single most important SEO tag in your document source. It succinctly informs search engines of what information is contained in the document. The doctitle changes from page to page, but these options will help you control what it looks by default.', GCPTA_DOMAIN ); ?></span></p>

			<p><span class="description"><?php _e( '<b>By default</b>, the ' . $this->post_type->label . ' Archives Page doctitle will contain the site title.', GCPTA_DOMAIN ); ?></span></p>
		
			<p>
				<label for="<?php echo $this->get_field_id( 'gcpta_doctitle_' . $this->post_type->name ); ?>"><?php _e( $this->post_type->label . ' Doctitle:', GCPTA_DOMAIN ); ?></label><br />
				<input type="text" name="<?php echo $this->get_field_name( 'gcpta_doctitle_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_doctitle_' . $this->post_type->name ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'gcpta_doctitle_' . $this->post_type->name ) ); ?>" size="80" /><br />
				<span class="description"><?php _e( 'If you leave the doctitle field blank, your site&rsquo;s title will be used instead.', GCPTA_DOMAIN ); ?></span>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'gcpta_description_' . $this->post_type->name ); ?>"><?php _e( $this->post_type->label . ' META Description:', GCPTA_DOMAIN ); ?></label><br />
				<textarea name="<?php echo $this->get_field_name( 'gcpta_description_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_description_' . $this->post_type->name ); ?>" rows="3" cols="70"><?php echo esc_textarea( $this->get_field_value( 'gcpta_description_' . $this->post_type->name ) ); ?></textarea><br />
				<span class="description"><?php _e( 'The META Description can be used to determine the text used under the title on search engine results pages.', GCPTA_DOMAIN ); ?></span>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'gcpta_keywords_' . $this->post_type->name ); ?>"><?php _e( $this->post_type->label . ' META Keywords (comma separated):', GCPTA_DOMAIN ); ?></label><br />
				<input type="text" name="<?php echo $this->get_field_name( 'gcpta_keywords_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_keywords_' . $this->post_type->name ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'gcpta_keywords_' . $this->post_type->name ) ); ?>" size="80" /><br />
				<span class="description"><?php _e( 'Keywords are generally ignored by Search Engines.', GCPTA_DOMAIN ); ?></span>
			</p>

			<h4><?php _e( $this->post_type->label . ' Robots Meta Tags:', GCPTA_DOMAIN ); ?></h4>

			<p>
				<input type="checkbox" name="<?php echo $this->get_field_name( 'gcpta_noindex_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_noindex_' . $this->post_type->name ); ?>" value="1" <?php checked( $this->get_field_value( 'gcpta_noindex_' . $this->post_type->name ) ); ?> />
				<label for="<?php echo $this->get_field_id( 'gcpta_noindex_' . $this->post_type->name ); ?>"><?php printf( __( 'Apply %s to the ' . $this->post_type->label . ' archives page?', GCPTA_DOMAIN ), '<code>noindex</code>' ); ?> <a href="http://www.robotstxt.org/meta.html" target="_blank">[?]</a></label>
				<br />
				<input type="checkbox" name="<?php echo $this->get_field_name( 'gcpta_nofollow_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_nofollow_' . $this->post_type->name ); ?>" value="1" <?php checked( $this->get_field_value( 'gcpta_nofollow_' . $this->post_type->name ) ); ?> />
				<label for="<?php echo $this->get_field_id( 'gcpta_nofollow_' . $this->post_type->name ); ?>"><?php printf( __( 'Apply %s to the ' . $this->post_type->label . ' archives page?', GCPTA_DOMAIN ), '<code>nofollow</code>' ); ?> <a href="http://www.robotstxt.org/meta.html" target="_blank">[?]</a></label>
				<br />
				<input type="checkbox" name="<?php echo $this->get_field_name( 'gcpta_noarchive_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_noarchive_' . $this->post_type->name ); ?>" value="1" <?php checked( $this->get_field_value( 'gcpta_noarchive_' . $this->post_type->name ) ); ?> />
				<label for="<?php echo $this->get_field_id( 'gcpta_noarchive_' . $this->post_type->name ); ?>"><?php printf( __( 'Apply %s to the ' . $this->post_type->label . ' archives page?', GCPTA_DOMAIN ), '<code>noarchive</code>' ); ?> <a href="http://www.ezau.com/latest/articles/no-archive.shtml" target="_blank">[?]</a></label>
			</p>
		</fieldset>
		<?php

	}
	
	/**
	 * Callback for Theme Settings Header / Footer Scripts meta box.
	 *
	 * @since 1.0.0
	 *
	 * @uses Genesis_Admin::get_field_name() Construct full field name
	 * @uses Genesis_Admin::get_field_value() Retrieve value of key under $this->settings_field
	 *
	 * @see Genesis_Admin_Settings::metaboxes()
	 */
	function scripts_box() {
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'header_scripts_' . $this->post_type->name ); ?>"><?php printf( __( 'Enter scripts or code you would like output to %s:', GCPTA_DOMAIN ), '<code>wp_head()</code>' ); ?></label>
		</p>

		<textarea name="<?php echo $this->get_field_name( 'header_scripts_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'header_scripts_' . $this->post_type->name ); ?>" cols="78" rows="8"><?php echo esc_textarea( $this->get_field_value( 'header_scripts_' . $this->post_type->name ) ); ?></textarea>

		<p><span class="description"><?php printf( __( 'The %1$s hook executes immediately before the closing %2$s tag in the document source.', GCPTA_DOMAIN ), '<code>wp_head()</code>', '<code>&lt;/head&gt;</code>' ); ?></span></p>

		<hr class="div" />

		<p>
			<label for="<?php echo $this->get_field_id( 'footer_scripts_' . $this->post_type->name ); ?>"><?php printf( __( 'Enter scripts or code you would like output to %s:', GCPTA_DOMAIN ), '<code>wp_footer()</code>' ); ?></label>
		</p>

		<textarea name="<?php echo $this->get_field_name( 'footer_scripts_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'header_scripts_' . $this->post_type->name ); ?>" cols="78" rows="8"><?php echo esc_textarea( $this->get_field_value( 'footer_scripts_' . $this->post_type->name ) ); ?></textarea>

		<p><span class="description"><?php printf( __( 'The %1$s hook executes immediately before the closing %2$s tag in the document source.', GCPTA_DOMAIN ), '<code>wp_footer()</code>', '<code>&lt;/body&gt;</code>' ); ?></span></p>
		<?php

	}
	
	/**
	 * Callback for minFolio Settings metabox
	 *
	 * @since 1.0.0
	 *
	 * @see WPS_GCPTA::metaboxes()
	 */
	function settings_box() { 	
		?>
		<p>
		<?php printf( __( 'Here is a <a href="%1$s">Link</a> to your ' . $this->post_type->label . ' archives page.' , GCPTA_DOMAIN ) , get_post_type_archive_link( $this->post_type->name ) ); ?>
		</p>
		
		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name( 'gcpta_intro_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_intro_' . $this->post_type->name ); ?>" value="1" <?php checked( 1, $this->get_field_value( 'gcpta_intro_' . $this->post_type->name ) ); ?> /> <label for="<?php echo $this->get_field_id( 'gcpta_intro_' . $this->post_type->name ); ?>"><?php _e( 'Insert Introduction Content to the ' . $this->post_type->label . ' Archives Page?', GCPTA_DOMAIN ); ?></label>
		</p>
		
		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name( 'gcpta_ss_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_ss_' . $this->post_type->name ); ?>" value="1" <?php checked( 1, $this->get_field_value( 'gcpta_ss_' . $this->post_type->name ) ); ?> /> <label for="<?php echo $this->get_field_id( 'gcpta_ss_' . $this->post_type->name ); ?>"><?php _e( 'Add Support for Simple Sidebars for ' . $this->post_type->label . ' Post Type? You will need to click save for the Simple Sidebars metabox to appear below.', GCPTA_DOMAIN ); ?></label>
		</p>
						
		<p>	
			<label for="<?php echo $this->get_field_id( 'gcpta_headline_' . $this->post_type->name ); ?>"><?php _e( $this->post_type->label . ' Headline:', GCPTA_DOMAIN ); ?></label><br />
			<input type="text" name="<?php echo $this->get_field_name( 'gcpta_headline_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_headline_' . $this->post_type->name ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'gcpta_headline_' . $this->post_type->name ) ); ?>" size="80" /><br />
			<span class="description"><?php printf( __( 'The %s tag is, arguably, the second most important SEO tag in the document source. If you leave the headline field blank, ' . $this->post_type->label . ' will be used instead.', GCPTA_DOMAIN ), '<code>&lt;h1&gt;</code>' ); ?></span>
		</p>
		
		<?php
		
		wp_editor( $this->get_field_value( 'gcpta_intro_content_' . $this->post_type->name ), $this->get_field_id( 'gcpta_intro_content_' . $this->post_type->name ) );
		?>
		
		<h4><?php _e( 'Loop Settings' , GCPTA_DOMAIN ); ?></h4>
		<div id="loop_settings">
			<label for="<?php echo $this->get_field_id( 'gcpta_loop_' . $this->post_type->name ); ?>"><?php _e( 'Select one of the following:', GCPTA_DOMAIN ); ?></label>
			<select class="loop_selector" name="<?php echo $this->get_field_name( 'gcpta_loop_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_loop_' . $this->post_type->name ); ?>">
			<?php
			$archive_loop = apply_filters(
				'gcpta_loop_options',
				array(
					'standard'     => __( 'Standard Loop', GCPTA_DOMAIN ),
					'grid' => __( 'Grid Loop', GCPTA_DOMAIN ),
				)
			);
			foreach ( (array) $archive_loop as $value => $name )
				echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->get_field_value( 'gcpta_loop_' . $this->post_type->name ), esc_attr( $value ), false ) . '>' . esc_html( $name ) . '</option>' . "\n";
			?>
			</select>
		</div>
		<div id="genesis_grid_loop_settings">
			<p>
				<label for="<?php echo $this->get_field_id( 'gcpta_grid_columns_' . $this->post_type->name ); ?>"><?php _e( 'Grid Columns:', GCPTA_DOMAIN ); ?>
				<input type="text" name="<?php echo $this->get_field_name( 'gcpta_grid_columns_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_grid_columns_' . $this->post_type->name ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'gcpta_grid_columns_' . $this->post_type->name ) ); ?>" size="3" /></label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'gcpta_features_' . $this->post_type->name ); ?>"><?php _e( 'Number of Full Posts to Show:', GCPTA_DOMAIN ); ?></label>
				<input type="text" name="<?php echo $this->get_field_name( 'gcpta_features_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_features_' . $this->post_type->name ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'gcpta_features_' . $this->post_type->name ) ); ?>" size="2" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'gcpta_features_content_limit_' . $this->post_type->name ); ?>"><?php _e( 'Limit features content to', GCPTA_DOMAIN ); ?>
				<input type="text" name="<?php echo $this->get_field_name( 'gcpta_features_content_limit_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_features_content_limit_' . $this->post_type->name ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'gcpta_features_content_limit_' . $this->post_type->name ) ); ?>" size="3" />
				<?php _e( 'characters', GCPTA_DOMAIN ); ?></label><br />
				<span class="description"><?php _e( 'Enter 0 for full content.', GCPTA_DOMAIN ); ?></span>
			</p>
			
			<p id="genesis_image_size features">
				<label for="<?php echo $this->get_field_id( 'gcpta_features_image_size_' . $this->post_type->name ); ?>"><?php _e( 'Features Image Size:', GCPTA_DOMAIN ); ?></label>
				<select name="<?php echo $this->get_field_name( 'gcpta_features_image_size_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_features_image_size_' . $this->post_type->name ); ?>">
				<?php
				$sizes = genesis_get_image_sizes();
				foreach ( (array) $sizes as $name => $size )
					echo '<option value="' . $name . '"' . selected( $this->get_field_value( 'gcpta_features_image_size_' . $this->post_type->name ), $name, FALSE ) . '>' . $name . ' (' . $size['width'] . ' &#215; ' . $size['height'] . ')</option>' . "\n";
				?>
				</select>
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id( 'gcpta_features_image_class_' . $this->post_type->name ); ?>"><?php _e('Features Image Classes', GCPTA_DOMAIN); ?>:</label><br />
				<input type="text" name="<?php echo $this->get_field_name( 'gcpta_features_image_class_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_features_image_class_' . $this->post_type->name ); ?>" value="<?php echo $this->get_field_value( 'gcpta_features_image_class_' . $this->post_type->name ); ?>" size="80" /><br />
				<span class="description"><?php _e( 'Separate with spaces (e.g. \'my-class my-class-2\')', GCPTA_DOMAIN ); ?></span>
			</p>
				
			<p>
				<label for="<?php echo $this->get_field_id( 'gcpta_grid_posts_' . $this->post_type->name ); ?>"><?php _e( 'Number of Grid Posts to Show:', GCPTA_DOMAIN ); ?></label>
				<input type="text" name="<?php echo $this->get_field_name( 'gcpta_grid_posts_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_grid_posts_' . $this->post_type->name ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'gcpta_grid_posts_' . $this->post_type->name ) ); ?>" size="2" /><br />
				<span class="description"><?php _e( 'To get all posts, enter -1 here.', GCPTA_DOMAIN ); ?></span>
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id( 'gcpta_grid_content_limit_' . $this->post_type->name ); ?>"><?php _e( 'Limit grid post content to', GCPTA_DOMAIN ); ?>
				<input type="text" name="<?php echo $this->get_field_name( 'gcpta_grid_content_limit_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_grid_content_limit_' . $this->post_type->name ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'gcpta_grid_content_limit_' . $this->post_type->name ) ); ?>" size="3" />
				<?php _e( 'characters', GCPTA_DOMAIN ); ?></label><br />
				<span class="description"><?php _e( 'Enter 0 for full content. Enter 1 for no content. Enter 2 for excerpt.', GCPTA_DOMAIN ); ?></span>
			</p>
			
			<p id="genesis_image_size grid">
				<label for="<?php echo $this->get_field_id( 'gcpta_grid_image_size_' . $this->post_type->name ); ?>"><?php _e( 'Grid Image Size:', GCPTA_DOMAIN ); ?></label>
				<select name="<?php echo $this->get_field_name( 'gcpta_grid_image_size_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_grid_image_size_' . $this->post_type->name ); ?>">
				<?php
				$sizes = genesis_get_image_sizes();
				foreach ( (array) $sizes as $name => $size )
					echo '<option value="' . $name . '"' . selected( $this->get_field_value( 'gcpta_grid_image_size_' . $this->post_type->name ), $name, FALSE ) . '>' . $name . ' (' . $size['width'] . ' &#215; ' . $size['height'] . ')</option>' . "\n";
				?>
				</select>
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id( 'gcpta_grid_image_class_' . $this->post_type->name ); ?>"><?php _e('Grid Image Classes', GCPTA_DOMAIN); ?>:</label><br />
				<input type="text" name="<?php echo $this->get_field_name( 'gcpta_grid_image_class_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_grid_image_class_' . $this->post_type->name ); ?>" value="<?php echo $this->get_field_value( 'gcpta_grid_image_class_' . $this->post_type->name ); ?>" size="80" /><br />
				<span class="description"><?php _e( 'Separate with spaces (e.g. \'my-class my-class-2\')', GCPTA_DOMAIN ); ?></span>
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id( 'gcpta_grid_read_more_' . $this->post_type->name ); ?>"><?php _e('Read More Text', GCPTA_DOMAIN); ?>:</label><br />
				<input type="text" name="<?php echo $this->get_field_name( 'gcpta_grid_read_more_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_grid_read_more_' . $this->post_type->name ); ?>" value="<?php echo $this->get_field_value( 'gcpta_grid_read_more_' . $this->post_type->name ); ?>" size="80" /><br />
				<span class="description"><?php _e( 'This will be over-written by get_the_content_more_link filter.', GCPTA_DOMAIN ); ?></span>
			</p>
		</div>
		
		<div id="genesis_standard_loop_settings">
			<p>
				<label for="<?php echo $this->get_field_id( 'gcpta_content_archive_' . $this->post_type->name ); ?>"><?php _e( 'Select one of the following:', GCPTA_DOMAIN ); ?></label>
				<select name="<?php echo $this->get_field_name( 'gcpta_content_archive_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_content_archive_' . $this->post_type->name ); ?>">
				<?php
				$archive_display = apply_filters(
					'genesis_archive_display_options',
					array(
						'full'     => __( 'Display post content', GCPTA_DOMAIN ),
						'excerpts' => __( 'Display post excerpts', GCPTA_DOMAIN ),
					)
				);
				foreach ( (array) $archive_display as $value => $name )
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->get_field_value( 'gcpta_content_archive_' . $this->post_type->name ), esc_attr( $value ), false ) . '>' . esc_html( $name ) . '</option>' . "\n";
				?>
				</select>
			</p>
			
			<div id="genesis_content_limit_setting">
				<p>
					<label for="<?php echo $this->get_field_id( 'gcpta_content_archive_limit_' . $this->post_type->name ); ?>"><?php _e( 'Limit content to', GCPTA_DOMAIN ); ?>
					<input type="text" name="<?php echo $this->get_field_name( 'gcpta_content_archive_limit_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_content_archive_limit_' . $this->post_type->name ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'gcpta_content_archive_limit_' . $this->post_type->name ) ); ?>" size="3" />
					<?php _e( 'characters', GCPTA_DOMAIN ); ?></label>
				</p>

				<p><span class="description"><?php _e( 'Using this option will limit the text and strip all formatting from the text displayed. To use this option, choose "Display post content" in the select box above.', GCPTA_DOMAIN ); ?></span></p>
			</div>

			<p>
				<input type="checkbox" class="genesis_featured_image_selector" name="<?php echo $this->get_field_name( 'gcpta_content_archive_thumbnail_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_content_archive_thumbnail_' . $this->post_type->name ); ?>" value="1"<?php checked( $this->get_field_value( 'gcpta_content_archive_thumbnail_' . $this->post_type->name ) ); ?> />
				<label for="<?php echo $this->get_field_id( 'gcpta_content_archive_thumbnail_' . $this->post_type->name ); ?>"><?php _e( 'Include the Featured Image?', GCPTA_DOMAIN ); ?></label>
			</p>

			<div id="genesis_image_size">
				<p>
					<label for="<?php echo $this->get_field_id( 'gcpta_image_size_' . $this->post_type->name ); ?>"><?php _e( 'Image Size:', GCPTA_DOMAIN ); ?></label>
					<select name="<?php echo $this->get_field_name( 'gcpta_image_size_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_image_size_' . $this->post_type->name ); ?>">
					<?php
					$sizes = genesis_get_image_sizes();
					foreach ( (array) $sizes as $name => $size )
						echo '<option value="' . $name . '"' . selected( $this->get_field_value( 'gcpta_image_size_' . $this->post_type->name ), $name, FALSE ) . '>' . $name . ' (' . $size['width'] . ' &#215; ' . $size['height'] . ')</option>' . "\n";
					?>
					</select>
				</p>
				
				<p>
					<label for="<?php echo $this->get_field_id( 'gcpta_image_alignment_' . $this->post_type->name ); ?>"><?php _e( 'Image Alignment', GCPTA_DOMAIN ); ?>:</label>
					<select id="<?php echo $this->get_field_id( 'gcpta_image_alignment_' . $this->post_type->name ); ?>" name="<?php echo $this->get_field_name( 'gcpta_image_alignment_' . $this->post_type->name ); ?>">
						<option value="alignnone">- <?php _e( 'None', GCPTA_DOMAIN ); ?> -</option>
						<option value="alignleft" <?php selected( 'alignleft', $this->get_field_value( 'gcpta_image_alignment_' . $this->post_type->name ) ); ?>><?php _e( 'Left', GCPTA_DOMAIN ); ?></option>
						<option value="alignright" <?php selected( 'alignright', $this->get_field_value( 'gcpta_image_alignment_' . $this->post_type->name ) ); ?>><?php _e( 'Right', GCPTA_DOMAIN ); ?></option>
					</select>
				</p>
				
			</div>
		</div>
		
		<h4><?php _e( 'Archive Settings' , GCPTA_DOMAIN ); ?></h4>
		<p>
			<input type="checkbox" class="post_info_selector" name="<?php echo $this->get_field_name( 'gcpta_remove_post_info_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_remove_post_info_' . $this->post_type->name ); ?>" value="1" <?php checked( 1, $this->get_field_value( 'gcpta_remove_post_info_' . $this->post_type->name ) ); ?> /> <label for="<?php echo $this->get_field_id( 'gcpta_remove_post_info_' . $this->post_type->name ); ?>"><?php _e( 'Remove Post Info on the ' . $this->post_type->label . ' Archives Page?', GCPTA_DOMAIN ); ?></label>
		</p>
		
		<p class="post_info">
			<label for="<?php echo $this->get_field_id( 'gcpta_post_info_' . $this->post_type->name ); ?>"><?php _e('Post Info', GCPTA_DOMAIN); ?>:</label><br />
			<input type="text" name="<?php echo $this->get_field_name( 'gcpta_post_info_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_post_info_' . $this->post_type->name ); ?>" value="<?php echo $this->get_field_value( 'gcpta_post_info_' . $this->post_type->name ); ?>" size="80" />
		</p>
		
		<p>
			<input type="checkbox" class="post_meta_selector" name="<?php echo $this->get_field_name( 'gcpta_remove_post_meta_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_remove_post_meta_' . $this->post_type->name ); ?>" value="1" <?php checked( 1, $this->get_field_value( 'gcpta_remove_post_meta_' . $this->post_type->name ) ); ?> /> <label for="<?php echo $this->get_field_id( 'gcpta_remove_post_meta_' . $this->post_type->name ); ?>"><?php _e( 'Remove Post Meta on the ' . $this->post_type->label . ' Archives Page?', GCPTA_DOMAIN ); ?></label>
		</p>
		
		<p class="post_meta">
			<label for="<?php echo $this->get_field_id( 'gcpta_post_meta_' . $this->post_type->name ); ?>"><?php _e('Post Meta', GCPTA_DOMAIN); ?>:</label><br />
			<input type="text" name="<?php echo $this->get_field_name( 'gcpta_post_meta_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_post_meta_' . $this->post_type->name ); ?>" value="<?php echo $this->get_field_value( 'gcpta_post_meta_' . $this->post_type->name ); ?>" size="80" /><br />
			<span class="description">
				<?php _e( 'To change the default (category) taxonomy change the [post_categories] shortcode to something like [post_terms taxonomy="my-tax"].' , GCPTA_DOMAIN ); ?><br />
				<?php _e( '<b>NOTE</b>: For a more comprehensive shortcode usage guide, <a href="http://dev.studiopress.com/shortcode-reference" target="_blank">see this page</a>.' , GCPTA_DOMAIN ); ?></span>
		</p>
		
		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name( 'gcpta_post_title_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_post_title_' . $this->post_type->name ); ?>" value="1" <?php checked( 1, $this->get_field_value( 'gcpta_post_title_' . $this->post_type->name ) ); ?> /> <label for="<?php echo $this->get_field_id( 'gcpta_post_title_' . $this->post_type->name ); ?>"><?php _e( 'Remove Post Titles on the ' . $this->post_type->label . ' Archives Page?', GCPTA_DOMAIN ); ?></label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'gcpta_posts_nav_' . $this->post_type->name ); ?>"><?php _e( 'Select Post Navigation Technique:', GCPTA_DOMAIN ); ?></label>
			<select name="<?php echo $this->get_field_name( 'gcpta_posts_nav_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_posts_nav_' . $this->post_type->name ); ?>">
				<option value="older-newer"<?php selected( 'older-newer', $this->get_field_value( 'gcpta_posts_nav_' . $this->post_type->name ) ); ?>><?php _e( 'Older / Newer', GCPTA_DOMAIN ); ?></option>
				<option value="prev-next"<?php selected( 'prev-next', $this->get_field_value( 'gcpta_posts_nav_' . $this->post_type->name ) ); ?>><?php _e( 'Previous / Next', GCPTA_DOMAIN ); ?></option>
				<option value="numeric"<?php selected( 'numeric', $this->get_field_value( 'gcpta_posts_nav_' . $this->post_type->name ) ); ?>><?php _e( 'Numeric', GCPTA_DOMAIN ); ?></option>
			</select>
		</p>

		<p><span class="description"><?php _e( 'These options will affect only the applicable custom post type archive.', GCPTA_DOMAIN ); ?></span></p>
		
		
		<?php
	}
	
	/**
	 * Callback for Theme Settings Default Layout meta box.
	 *
	 * A version of a site layout setting has been in Genesis since at least 0.2.0,
	 * but it was moved to its own meta box in 1.7.0.
	 *
	 * @since 1.7.0
	 *
	 * @uses genesis_layout_selector() Outputs form elements for layout picker
	 * @uses Genesis_Admin::get_field_name() Construct full field name
	 * @uses Genesis_Admin::get_field_value() Retrieve value of key under $this->settings_field
	 *
	 * @see Genesis_Admin_Settings::metaboxes()
	 */
	function layout_box() {
		$default = genesis_get_option( 'site_layout' );
		?>
		<p>
			<input type="radio" name="<?php echo $this->get_field_name( 'gcpta_layout_' . $this->post_type->name ); ?>" id="default-layout" value="" <?php checked( $this->get_field_value( 'gcpta_layout_' . $this->post_type->name ), '' ); ?> /> <label class="default" for="default-layout"><?php printf( __( 'Default Layout set in <a href="%s">Theme Settings</a>', 'genesis' ), menu_page_url( 'genesis', 0 ) ); ?></label>
		</p>
			
		<p class="genesis-layout-selector">
			
		<?php
		genesis_layout_selector( array( 'name' => $this->get_field_name( 'gcpta_layout_' . $this->post_type->name ), 'selected' => $this->get_field_value( 'gcpta_layout_' . $this->post_type->name ), 'type' => 'site' ) );
		?>
		</p>

		<br class="clear" />
		<?php

	}
	
	function ss_inpost_metabox() {
		global $wp_registered_sidebars; // why not use the global?
		$_sidebars = $wp_registered_sidebars; 
		?>
		<p>
			<label class="howto" for="<?php echo $this->get_field_id( 'gcpta_ss_sidebar_' . $this->post_type->name ); ?>"><span><?php _e('Primary Sidebar', GCPTA_DOMAIN); ?><span></label>
			<select name="<?php echo $this->get_field_name( 'gcpta_ss_sidebar_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_ss_sidebar_' . $this->post_type->name ); ?>">
				<option value=""><?php _e('Default', GCPTA_DOMAIN); ?></option>
				<?php
				foreach ( (array)$_sidebars as $id => $info ) {
					printf( '<option value="%s" %s>%s</option>', $id, selected( $this->get_field_value( 'gcpta_ss_sidebar_' . $this->post_type->name ), $id), esc_html( $info['name'] ) );
				}
				?>
			</select>
			</p>
		</p>
		<?php
		
		// don't show the option if there are no 3 column layouts registered
		if ( ! ss_has_3_column_layouts() )
			return;
		?>
		<p>
			<label class="howto" for="<?php echo $this->get_field_id( 'gcpta_ss_sidebar_alt_' . $this->post_type->name ); ?>"><span><?php _e('Secondary Sidebar', GCPTA_DOMAIN); ?><span></label>
			
			<select name="<?php echo $this->get_field_name( 'gcpta_ss_sidebar_alt_' . $this->post_type->name ); ?>" id="<?php echo $this->get_field_id( 'gcpta_ss_sidebar_alt_' . $this->post_type->name ); ?>">
				<option value=""><?php _e('Default', GCPTA_DOMAIN); ?></option>
				<?php
				foreach ( (array)$_sidebars as $id => $info ) {
					printf( '<option value="%s" %s>%s</option>', $id, selected( $this->get_field_value( 'gcpta_ss_sidebar_alt_' . $this->post_type->name ), $id), esc_html( $info['name'] ) );
				}
				?>
			</select>
		</p>

		<?php
	}
		
	
}

/**
 * Does this Genesis install have the 3 column layouts deactivated?
 *
 * This function checks to see if the Genesis install still has active 3 column layouts. Since
 * child themes and plugins can deregister layouts, we need to know if they have deregistered the 3 column layouts.
 *
 * @since 0.9.2
 */
if ( !function_exists( 'ss_has_3_column_layouts' ) ) {
function ss_has_3_column_layouts() {

	$_layouts = (array) genesis_get_layouts();
	$_layouts = array_keys( $_layouts );
	$_3_column = array_intersect( $_layouts, array( 'content-sidebar-sidebar', 'sidebar-content-sidebar', 'sidebar-sidebar-content' ) );

	return ! empty( $_3_column );

}
}