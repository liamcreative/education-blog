<?php
/**
 * Disable requests to wp.org repository for this theme.
 *
 * @since 1.0.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 665; /* pixels */

/**
 * Adjusts content_width value for few pages and attachment templates.
 */
function motif_content_width() {
	global $content_width;

	if ( is_page_template( 'page-templates/full-width-page.php' )
	  || is_page_template( 'page-templates/grid-page.php' )
	  || is_attachment() )
		$content_width = 1032;
}
add_action( 'template_redirect', 'motif_content_width' );

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function motif_setup() {

	/**
	 * Make theme available for translation.
	 */
	load_theme_textdomain( 'motif', get_template_directory() . '/languages' );

	/**
	 * Add editor styles.
	 */
	add_editor_style( array( 'editor-style.css', motif_fonts_url() ) );

	/**
	 * Add default posts and comments RSS feed links to head.
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Use valid HTML5 markup for the comments, the search form and the comment form.
	 */
	add_theme_support( 'html5', array( 'comment-list', 'search-form', 'comment-form', ) );

	/**
	 * Add support for post formats.
	 */
	add_theme_support( 'post-formats', array(
		'aside',
		'gallery',
		'image',
		'link',
		'quote',
		'video',
	) );

	/**
	 * This theme uses wp_nav_menu() in two locations.
	 */
	register_nav_menus( array(
		'primary'   => __( 'Primary Menu', 'motif' ),
		'secondary' => __( 'Footer Menu', 'motif' ),
	) );

	/**
	 * Enable support for post thumbnails.
	 */
	add_theme_support( 'post-thumbnails' );

	/**
	 * Add the various thumbnail sizes.
	 */
	// Post thumbnails
	set_post_thumbnail_size( 705, 435, true );
	// Hero Image on the front page template
	add_image_size( 'motif-hero-thumbnail', 1140, 610, true );
	// Full width and grid page template
	add_image_size( 'motif-page-thumbnail', 1072, 435, true );
	// Grid child page thumbnail
	add_image_size( 'motif-grid-thumbnail', 334, 233, true );
	// Testimonial thumbnail
	add_image_size( 'motif-testimonial-thumbnail', 90, 90, true );

	/**
	 * Add manual excerpts support to pages.
	 */
	add_post_type_support( 'page', 'excerpt' );

	/**
	 * Setup the WordPress core custom background feature.
	 */
	add_theme_support( 'custom-background', apply_filters( 'motif_custom_background_args', array(
		'default-color' => 'f3f3f3',
		'default-image' => get_template_directory_uri() . '/images/body-bg.png',
	) ) );
}


add_filter('widget_tag_cloud_args','set_number_tags');
function set_number_tags($args) {
    $args = array('number'    => 100);
    return $args;
}

function new_wp_trim_excerpt($text) {
  $raw_excerpt = $text;
  if ( '' == $text ) {
    $text = get_the_content('');
    $text = strip_shortcodes( $text );
    $text = apply_filters('the_content', $text);
    $text = str_replace(']]>', ']]>', $text);
    $text = strip_tags($text, '<a>');
    $excerpt_length = apply_filters('excerpt_length', 55);
    $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
    $words = preg_split('/(<a.*?a>)|\n|\r|\t|\s/', $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE );
    if ( count($words) > $excerpt_length ) {
      array_pop($words);
      $text = implode(' ', $words);
      $text = $text . $excerpt_more;
      } 
    else {
      $text = implode(' ', $words);
      }
    }
  return apply_filters('new_wp_trim_excerpt', $text, $raw_excerpt);
  }
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'new_wp_trim_excerpt');



/**
 * Returns the Google font stylesheet URL, if available.
 */
function motif_fonts_url() {
	$fonts_url = '';

	/* translators: If there are characters in your language that are not supported
	 * by Droid Serif, translate this to 'off'. Do not translate into your own language.
	 */
	$droid_serif = _x( 'on', 'Droid Serif font: on or off', 'motif' );

	/* translators: If there are characters in your language that are not supported
	 * by Droid Sans, translate this to 'off'. Do not translate into your own language.
	 */
	$droid_sans  = _x( 'on', 'Droid Sans font: on or off',  'motif' );

	if ( 'off' !== $droid_serif || 'off' !== $droid_sans ) {
		$font_families = array();

		if ( 'off' !== $droid_serif )
			$font_families[] = 'Droid Serif:400,400italic,700,700italic';

		if ( 'off' !== $droid_sans )
			$font_families[] = 'Droid Sans:400,700';

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin' ),
		);
		$fonts_url = add_query_arg( $query_args, "//fonts.googleapis.com/css" );
	}

	return $fonts_url;
}

/**
 * Register widgetized area and update sidebar with default widgets.
 */
function motif_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Main Sidebar', 'motif' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
	register_sidebar( array(
		'name'          => __( 'First Footer Sidebar', 'motif' ),
		'id'            => 'sidebar-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
	register_sidebar( array(
		'name'          => __( 'Second Footer Sidebar', 'motif' ),
		'id'            => 'sidebar-3',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
	register_sidebar( array(
		'name'          => __( 'Third Footer Sidebar', 'motif' ),
		'id'            => 'sidebar-4',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
	register_sidebar( array(
		'name'          => __( 'First Front Page Sidebar', 'motif' ),
		'id'            => 'sidebar-5',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
	register_sidebar( array(
		'name'          => __( 'Second Front Page Sidebar', 'motif' ),
		'id'            => 'sidebar-6',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'motif_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function motif_scripts_styles() {
	// Add Droid Serif and Droid Sans fonts.
	wp_enqueue_style( 'motif-fonts', motif_fonts_url(), array(), null );

	// Add Genericons font.
	wp_enqueue_style( 'genericons', get_template_directory_uri() . '/fonts/genericons.css', array(), '2.09' );

	// Load the main stylesheet.
	wp_enqueue_style( 'motif-style', get_stylesheet_uri() );

	wp_enqueue_script( 'motif-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20130922', true );

	wp_enqueue_script( 'motif-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130922', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'motif-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20130922' );
	}
}
add_action( 'wp_enqueue_scripts', 'motif_scripts_styles' );

add_theme_support( 'post-thumbnails' );

add_filter('body_class','add_category_to_single');

function add_category_to_single($classes) {
  if (!is_admin() && is_single() || is_archive() ) {
    global $post;
    foreach((get_the_category($post->ID)) as $category) {
      // add category slug to the $classes array
      $classes[] = $category->slug;
    }
  }
  // return the $classes array
  return $classes;
}

/**
 * Enqueue Google fonts style to admin screen for custom header display.
 */
function motif_admin_fonts() {
	wp_enqueue_style( 'motif-fonts', motif_fonts_url(), array(), null );
}
add_action( 'admin_print_scripts-appearance_page_custom-header', 'motif_admin_fonts' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Custom Header features.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';


