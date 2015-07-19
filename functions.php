<?php
/**
 * themename functions and definitions
 *
 * @package themename
 */


/*-------------------------------------------------------------------------------------------*/
/* themename Theme Setup
/*-------------------------------------------------------------------------------------------*/


if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}


if ( ! function_exists( 'themename_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function themename_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 */

	load_theme_textdomain( 'themename', get_template_directory() . '/languages' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */

	add_theme_support( 'post-thumbnails' );
	/*  Specify image title, size and positionig */
	add_image_size( 'blog-feat', 300, 300, array( 'center', 'center' ) );


	// This theme uses wp_nav_menu() in one location.
	// Register multiple nav menus this way to hook into the WordPresss API

	register_nav_menus(
        array(
            'primary' => 'Primary Menu',
            'blog' => 'Blog Menu',
            'footer' => 'Footer Menu',
         )
    );


	/*-------------------------------------------------------------------------------------------*/
	/*  Enable Active CLass on Menu remove the messy default classes
	/*-------------------------------------------------------------------------------------------*/

	//Deletes all CSS classes and id's, except for those listed in the array below

	function custom_wp_nav_menu($var) {
		return is_array($var) ? array_intersect($var, array(
			//List of allowed menu classes
			'current_page_item',
			'current_page_ancestor',
			'first',
			'last',
			'vertical',
			'horizontal'
			)
		) : '';
	}

	add_filter('nav_menu_css_class', 'custom_wp_nav_menu');
	add_filter('nav_menu_item_id', 'custom_wp_nav_menu');
	add_filter('page_css_class', 'custom_wp_nav_menu');

	//Replaces "current-menu-item" with "active"

	function current_to_active($text){
		$replace = array(
			//List of menu item classes that should be changed to "active"
			'current_page_item' => 'active',
		);

		$text = str_replace(array_keys($replace), $replace, $text);
			return $text;
		}

	add_filter ('wp_nav_menu','current_to_active');


	/*-------------------------------------------------------------------------------------------*/
	/*  Enable Additional Theme Support
	/*-------------------------------------------------------------------------------------------*/

	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	/*-------------------------------------------------------------------------------------------*/
	/*  Enable Post Format Support
	/*-------------------------------------------------------------------------------------------*/

	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link'
	) );

}

endif;

add_action( 'after_setup_theme', 'themename_setup' );

/*-------------------------------------------------------------------------------------------*/
/* Remove WP version number from header
/*-------------------------------------------------------------------------------------------*/

remove_action('wp_head', 'wp_generator');


/*-------------------------------------------------------------------------------------------*/
/* Register widget area.
/* @link http://codex.wordpress.org/Function_Reference/register_sidebar
/*-------------------------------------------------------------------------------------------*/


function themename_widgets_init() {

    register_sidebar( array(
		'name'          => __( 'Generic Widget', 'themename' ),
		'id'            => 'generic-widget',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	));
}

add_action( 'widgets_init', 'themename_widgets_init' );

/*-------------------------------------------------------------------------------------------*/
/* Enqueue theme scripts and styles.
/*-------------------------------------------------------------------------------------------*/

function themename_scripts() {

	/* Styles */

	/* Base Style */
	$cache_buster = date("YmdHi", filemtime( get_template_directory_uri()));
	wp_enqueue_style( 'themename-style', get_stylesheet_uri(), array(), $cache_buster, 'all' );

	/* Loading External stylesheet via CDn*/
	wp_enqueue_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.0.1/css/font-awesome.css', array(), '4.0.1' );

	// /* Scripts */
	wp_enqueue_script( 'themename-main', get_stylesheet_directory_uri() . '/scripts/themename-main.js', array(), filemtime( get_stylesheet_directory() . '/scripts/themename-main.js' ) , true );

}

add_action( 'wp_enqueue_scripts', 'themename_scripts' );


/*===================================================================================
* Remove automatic p tags in text editor
* =================================================================================*/


remove_filter ('the_content',  'wpautop');


/*===================================================================================
* Load Custom Shortcodes - can add more here
* =================================================================================*/


function button_shortcode( $atts, $content = null ) {
   extract( shortcode_atts( array(
      'username' => 'username',
      'style' => 'style'
      ), $atts ) );

   return '<br><a href="http://twitter.com/' . esc_attr($username) . '" class="twitter-button ' . esc_attr($style) . '" target="_blank"><i class="fa fa-twitter"></i> ' . $content . '</a>';
}
add_shortcode('button', 'button_shortcode');


/*===================================================================================
 * Limit Excerpt Length
 * =================================================================================*/

function custom_excerpt_length( $length ) {
 	return 50;
}

add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );


/*===================================================================================
* Function to check for posts by popularity
* =================================================================================*/

function observePostViews($postID) {
	$count_key = 'post_views_count';
		$count = get_post_meta($postID, $count_key, true);
		if($count==''){
			$count = 0;
			delete_post_meta($postID, $count_key);
			add_post_meta($postID, $count_key, '0');
		} else {
			$count++;
			update_post_meta($postID, $count_key, $count);
		}
}

function fetchPostViews($postID){
	$count_key = 'post_views_count';
	$count = get_post_meta($postID, $count_key, true);
	if($count==''){
		delete_post_meta($postID, $count_key);
		add_post_meta($postID, $count_key, '0');
		return "0 View";
		}
		return $count.' Views';
}


/*===================================================================================
 * Customise Login Logo & link to main website
 * =================================================================================*/


function customise_login_image() { ?>

	<style type="text/css">
		body.login #login h1 a {
			background: url('') 8px 0 no-repeat transparent;
			background-position: center;
			height:100px;
			width:320px; }
		</style>
<?php }

add_action("login_head", "customise_login_image");

add_filter( 'login_headerurl', 'custom_loginlogo_url' );
function custom_loginlogo_url($url) {
	return 'http://themename.com';
}




