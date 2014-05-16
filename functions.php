<?php
//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', 'Creative Pro Theme' );
define( 'CHILD_THEME_URL', 'http://wpspeak.com/themes/creative' );
define( 'CHILD_THEME_VERSION', '1.0.1' );

//* Enqueue Lato Google font
add_action( 'wp_enqueue_scripts', 'creative_google_fonts' );
function creative_google_fonts() {
	wp_enqueue_style( 'google-custom-font', '//fonts.googleapis.com/css?family=Open+Sans|Cuprum', array(), CHILD_THEME_VERSION );
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_script( 'prefix-responsive-menu', get_stylesheet_directory_uri() . '/js/responsive-menu.js', array( 'jquery' ), '1.0.0', true );
}

//* Add HTML5 markup structure
add_theme_support( 'html5' );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Add new featured image sizes
add_image_size( 'thumb-photo', 293, 293, TRUE );

//* Remove the site description
remove_action( 'genesis_site_description', 'genesis_seo_site_description' );

//* Unregister layout settings
genesis_unregister_layout( 'content-sidebar' );
genesis_unregister_layout( 'sidebar-content' );
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );
genesis_unregister_layout( 'sidebar-content-sidebar' );

//* Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );

//* Add support for structural wraps
add_theme_support( 'genesis-structural-wraps', array(
	'header',
	'nav',
	'site-inner',
	'footer-widgets',
	'footer'
) ); 

//* Unregister sidebars
unregister_sidebar( 'sidebar' );
unregister_sidebar( 'sidebar-alt' );

//* Unregister the header right widget area
unregister_sidebar( 'header-right' );

//* Add support for custom header
add_theme_support( 'custom-header', array(
	'admin-preview-callback' => 'creative_admin_header_callback',
	'default-text-color'     => 'ffffff',
	'header-selector'        => '.site-header .site-avatar img',
	'height'                 => 300,
	'width'                  => 300,
	'wp-head-callback'       => 'creative_header_callback',
) );

function creative_admin_header_callback() {
	echo get_header_image() ? '<img src="' . get_header_image() . '" />' : get_avatar( get_option( 'admin_email' ), 300 );
}

function creative_header_callback() {

	if ( ! get_header_textcolor() )
		return;

	printf( '<style  type="text/css">.site-title a { color: #%s; }</style>' . "\n", get_header_textcolor() );

	if ( get_header_image() )
		return;

	if ( ! display_header_text() )
	add_filter( 'body_class', 'creative_header_image_body_class' );

}

//* Add custom body class for header-text
function creative_header_image_body_class( $classes ) {

	$classes[] = 'header-image';
	return $classes;
	
}

//* Hook site avatar before site title
add_action( 'genesis_header', 'creative_site_gravatar', 5 );
function creative_site_gravatar() {

	$header_image = get_header_image() ? '<img alt="" src="' . get_header_image() . '" />' : get_avatar( get_option( 'admin_email' ), 300 );
	printf( '<div class="site-avatar"><a href="%s">%s</a></div>', home_url( '/' ), $header_image );

}

//* Replace 'Home' text with a home icon in breadcrumb
add_filter ( 'genesis_home_crumb', 'creative_breadcrumb_home_icon' ); 
function creative_breadcrumb_home_icon( $crumb ) {

     $crumb = '<a href="' . home_url() . '" title="' . get_bloginfo('name') . '"><i class="dashicons dashicons-admin-home"></i></a>';
     return $crumb;
	 
}

//* Remove 'You are here' texts in breadcrumb
add_filter( 'genesis_breadcrumb_args', 'creative_breadcrumb_args' );
function creative_breadcrumb_args( $args ) {

	$args['labels']['prefix'] = '';
	return $args;

}

//* Customize the entry meta in the entry header 
add_filter( 'genesis_post_info', 'creative_post_info_filter' );
function creative_post_info_filter($post_info) {

	$post_info = '[post_date] [post_author_posts_link] [post_comments] [post_edit]';
	return $post_info;
	
}

//* Customize the entry meta in the entry footer 
add_filter( 'genesis_post_meta', 'creative_post_meta_filter' );
function creative_post_meta_filter($post_meta) {

	$post_meta = '[post_categories before=""] [post_tags before=""]';
	return $post_meta;
	
}

//* Display custom tax in entry footer (Genesis Framework)
function creative_display_conditionals() {

	if ( ! is_singular() ) {
        add_filter('genesis_post_info', 'creative_display_custom_tax');
	}
	
}
 
function creative_display_custom_tax($post_meta) {

	$post_info = '[post_date]';
	return $post_info;

}
 
add_action('wp', 'creative_display_conditionals');

/**
 * Archive Post Class
 * @since 1.0.0
 *
 * Breaks the posts into three columns
 * @link http://www.billerickson.net/code/grid-loop-using-post-class
 *
 * @param array $classes
 * @return array
 */
function creative_archive_post_class( $classes ) {
 
	// Don't run on single posts or pages
	if( is_singular() )
		return $classes;
 
	$classes[] = 'one-third';
	global $wp_query;
	if( 0 == $wp_query->current_post || 0 == $wp_query->current_post % 3 )
		$classes[] = 'first';
	return $classes;
	
}
add_filter( 'post_class', 'creative_archive_post_class' );

//* Remove Image Alignment from Featured Image
function creative_remove_image_alignment( $attributes ) {

  $attributes['class'] = str_replace( 'alignleft', 'aligncenter', $attributes['class'] );
	return $attributes;
	
}
add_filter( 'genesis_attr_entry-image', 'creative_remove_image_alignment' );

//* Repositon the entry image
remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
add_action( 'genesis_entry_header', 'genesis_do_post_image', 3 );

//* Remove blog page template
function creative_remove_genesis_page_templates( $page_templates ) {

	unset( $page_templates['page_blog.php'] );
	return $page_templates;
	
}
add_filter( 'theme_page_templates', 'creative_remove_genesis_page_templates' );

//* Remove post meta and post info on single post type
add_action('genesis_before_loop','creative_tax_single');
function creative_tax_single() {

    if ( !is_singular() ) { 
		remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
		remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );
    }  
	
}

//* Remove comment form allowed tags
add_filter( 'comment_form_defaults', 'creative_remove_comment_form_allowed_tags' );
function creative_remove_comment_form_allowed_tags( $defaults ) {
	
	$defaults['comment_notes_after'] = '';
	return $defaults;

}

//* Remove output of primary navigation right extras
remove_filter( 'genesis_nav_items', 'genesis_nav_right', 10, 2 );
remove_filter( 'wp_nav_menu_items', 'genesis_nav_right', 10, 2 );

//* Set default Simple Social Icon Styles
add_filter( 'simple_social_default_styles', 'creative_default_style' );
function creative_default_style( $defaults ) {
 
	$defaults['size']                      = '46';
	$defaults['border_radius']             = '100';
	$defaults['icon_color']	               = '#e96e57';
	$defaults['icon_color_hover']          = '#fff';
	$defaults['background_color']          = '#fff';
	$defaults['background_color_hover']    = '#e96e57';
	$defaults['alignment']                 = 'aligncenter';
 
	return $defaults;
}

//* Hook social widget area in header section
add_action( 'genesis_header', 'creative_widget_section', 12 );
function creative_widget_section() {
	
		genesis_widget_area( 'social-widget-section', array(
			'before' => '<div class="social-widget-section widget-area">',
			'after' => '</div>',
	) );
	
}

//* Hook bottom widget area before widgetized footer
add_action( 'genesis_before_footer', 'creative_bottom_widget', 8 );
function creative_bottom_widget() {

		genesis_widget_area( 'bottom-widget', array(
			'before' => '<div class="bottom-widget widget-area"><div class="wrap">',
			'after' => '</div></div>',
	) );
	
}

//* Register widget areas
genesis_register_sidebar( array(
	'id'            => 'social-widget-section',
	'name'          => __( 'Social widget Area', 'Creative' ),
	'description'   => __( 'This is a widget area that can be placed after header', 'creative' ),
) );

genesis_register_sidebar( array(
	'id'            => 'bottom-widget',
	'name'          => __( 'Bottom widget', 'Creative' ),
	'description'   => __( 'This is a widget area that can be placed before the widgetized widget', 'creative' ),
) );