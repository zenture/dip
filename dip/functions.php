<?php
/**
 * Dip Framework Bootstrap
 *
 * @package dip
*/

/** define include path */
$include_path  = TEMPLATEPATH != STYLESHEETPATH ? PATH_SEPARATOR . STYLESHEETPATH : '';
$include_path .= PATH_SEPARATOR . TEMPLATEPATH;

set_include_path(get_include_path() . $include_path);
require('includes/bootstrap.php');

/* Set globals */
global $dip, $loader;

/** Init the framework... */
$dip = new DP_Bootstrap();
$dip->start();

/** Template tags */
function is_login_page() {
  return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}

/**
 * Returns true if a site has more than 1 category
 */
function is_categorized() {
  if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
    // Create an array of all the categories that are attached to posts
    $all_the_cool_cats = get_categories( array(
      'hide_empty' => 1,
    ) );

    // Count the number of categories that are attached to the posts
    $all_the_cool_cats = count( $all_the_cool_cats );

    set_transient( 'all_the_cool_cats', $all_the_cool_cats );
  }

  if ( '1' != $all_the_cool_cats ) {
    // This blog has more than 1 category so dip_categorized_blog should return true
    return true;
  } else {
    // This blog has only 1 category so dip_categorized_blog should return false
    return false;
  }
}


/* include and init loader class */
/*




$loader->post_types = array('Article');

require('helper/twitter.php');
// Adjust
require('panel.php');
//require('panel/settings.php');
//$panel = new Dip_Panel_Settings();


/*require('modules/orbit.php');
require('helper/orbit.php');
$panel = new Dip_Panel_Orbit();
*/
/*require('modules/single.php');
$panel = new DP_Panel_Single();





/*$loader = new DP_Loader();
$loader->load($resources);





















// translations
load_theme_textdomain( 'deep', get_template_directory() . '/lang' );





// core

require_once('helper.php');
require_once('forms.php');

add_theme_support( 'post-thumbnails' );


// extensions
require_once('router.php');
require_once('helper/date.php');
//require_once('helper/navbar.php');
require_once('helper/breadcrumb.php');
require_once('shortcodes.php');

global $routes;

if( !is_null($routes) ) {
    $router = new DP_Router($routes);
    $router->run();
}


// enable menus
register_nav_menus();

// hide admin bar
add_filter('show_admin_bar', '__return_false');

// Foundation grid on admin
function admin_stylesheet() { echo '<link rel="stylesheet" type="text/css" href="' . get_template_directory_uri() . '/styles/wp-admin.custom.css">'; }
add_action('admin_head', 'admin_stylesheet');


function custom_editor_style() {
    global $current_screen;
    switch ($current_screen->post_type) {
         case 'post':
         add_editor_style('editor-style-post.css');
         break;
         case 'page':
         add_editor_style('editor-style-page.css');
         break;
         case '[POSTTYPE]':
         add_editor_style('editor-style-[POSTTYPE].css');
         break;
    }
}
add_action( 'admin_head', 'custom_editor_style' );
 

/**
 * Set the content width based on the theme's design and stylesheet.
 */
/*if ( ! isset( $content_width ) )
	$content_width = 640; /* pixels */

//if ( ! function_exists( 'dip_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */
//function dip_setup() {

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 * If you're building a theme based on dip, use a find and replace
	 * to change 'dip' to the name of your theme in all the template files
	 */
//	load_theme_textdomain( 'dip', get_template_directory() . '/languages' );

	/**
	 * Add default posts and comments RSS feed links to head
	 */
//	add_theme_support( 'automatic-feed-links' );

	/**
	 * Enable support for Post Thumbnails on posts and pages
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	//add_theme_support( 'post-thumbnails' );

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
/*	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'dip' ),
	) ); */

	/**
	 * Enable support for Post Formats
	 */
//	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );

	/**
	 * Setup the WordPress core custom background feature.
	 */
/*	add_theme_support( 'custom-background', apply_filters( 'dip_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // dip_setup
add_action( 'after_setup_theme', 'dip_setup' ); */

/**
 * Register widgetized area and update sidebar with default widgets
 */
/*function dip_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'dip' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'dip_widgets_init' ); */

/**
 * Enqueue scripts and styles
 */
/*function dip_scripts() {
	wp_enqueue_style( 'dip-style', get_stylesheet_uri() );

	wp_enqueue_script( 'dip-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'dip-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'dip-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
	}
}
add_action( 'wp_enqueue_scripts', 'dip_scripts' ); */

/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
//require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
//require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
//require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
//require get_template_directory() . '/inc/jetpack.php';

