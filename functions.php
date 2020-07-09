<?php
	
		//remove all annoying WP header info
		remove_action( 'wp_head', 'feed_links' );
		remove_action( 'wp_head', 'rsd_link');
		remove_action( 'wp_head', 'wlwmanifest_link');
		remove_action( 'wp_head', 'index_rel_link');
		remove_action( 'wp_head', 'parent_post_rel_link');
		remove_action( 'wp_head', 'start_post_rel_link');
		remove_action( 'wp_head', 'adjacent_posts_rel_link');
		remove_action( 'wp_head', 'wp_generator');
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
	
		// Load jQuery by hand
		function my_admin_scripts() {
			   wp_deregister_script('jquery');
			   wp_register_script('jquery', ("https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"), false, null);
			   wp_enqueue_script('jquery');
			   wp_enqueue_script( 'jquery-migrate', 'https://code.jquery.com/jquery-migrate-3.0.1.min.js', array('jquery'), '3.0.1', false );
		}
		add_action( 'wp_enqueue_scripts', 'my_admin_scripts' );	
		
		/**
		* Remove the query strings from the static resources
		*/
		function _remove_script_version( $src ){
			$parts = explode( '?ver', $src );
			return $parts[0];
		}
		add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
		add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );
		
		function get_the_slug( $id=null ){
		  if( empty($id) ):
		    global $post;
		    if( empty($post) )
		      return ''; // No global $post var available.
		    $id = $post->ID;
		  endif;
		
		  $slug = basename( get_permalink($id) );
		  return $slug;
		}
		
		add_action('get_header', 'my_filter_head');

		  function my_filter_head() {
		    remove_action('wp_head', '_admin_bar_bump_cb');
		  }
  
		function the_slug( $id=null ){
		  echo apply_filters( 'the_slug', get_the_slug($id) );
		}
		
		function custom_login_logo() {
		    echo '<style type="text/css">
		        h1 a { 
		        	background-image:url(https://mogmachine.com/images/mogmachine-logo.png) !important;
					width:240px!important;
					height:185px!important;
					-webkit-background-size: 240px!important;
					background-size: 240px!important;
					}
					body {background: #17253D;color: #444;}
		    </style>';
		}
		
		function my_login_logo_url_title() {
		    return 'mogmachine';
		}
		
		function my_login_logo_url() {
		    return "https://www.mogmachine.com";
		}
		/*
		function custom_login_logo() {
		    echo '<style type="text/css">
		        h1 a { 
		        	background-image:url(http://basemogmachine.flywheelsites.com/sbl-logo.jpg) !important;
					width:320px!important;
					height:220px!important;
					-webkit-background-size: 320px!important;
					background-size: 320px!important;
					}
					body {background: #FFF;color: #444;}
		    </style>';
		}
		
		function my_login_logo_url_title() {
		    return 'Super Being Labs';
		}
		
		function my_login_logo_url() {
		    return "http://superbeinglabs.org/";
		}
		*/
		
		add_action('login_head', 'custom_login_logo');
		add_filter( 'login_headerurl', 'my_login_logo_url' );		
		add_filter( 'login_headertitle', 'my_login_logo_url_title' );
		
		function no_wp_logo_admin_bar_remove() {
		    global $wp_admin_bar;
		    $wp_admin_bar->remove_menu('wp-logo');
		}
		add_action('wp_before_admin_bar_render', 'no_wp_logo_admin_bar_remove', 0);
	

		// add is_child function
	    function is_child($post_id) {
			global $wp_query;
			$ancestors = $wp_query->post->ancestors;
			if(isset($ancestors)){
				if (in_array($post_id, $ancestors) ) {
					$return = true;
				} else {
					$return = false;
				} 
			} else {
				$return = false;
			}
			return $return;
		}
		
		add_theme_support( 'title-tag' );
		
		//add thumbnails functionality
		add_theme_support( 'post-thumbnails' );
		
		add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10, 3 );
		
		function remove_thumbnail_dimensions( $html, $post_id, $post_image_id ) {
		    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
		    return $html;
		}
		
		// add expanding category menu
		add_action('admin_head', 'categories_selection_jquery');
		function categories_selection_jquery() {
			echo'
			<script type="text/javascript">
				jQuery(function($){
					$("#category-all.tabs-panel").height($("#categorychecklist").height());
				});
			</script>
			';
		}
			
		// add excerpt to pages
		add_action( 'init', 'my_add_excerpts_to_pages' );
		function my_add_excerpts_to_pages() {
		     add_post_type_support( 'page', 'excerpt' );
		}
		
		// remove links menu
		function my_admin_menu() {
		     remove_menu_page('link-manager.php');
		}
		
		// add footer dev link
		add_filter( 'admin_footer_text', 'my_admin_footer_text' );
		function my_admin_footer_text( $default_text ) {
		     return '<span id="footer-thankyou">Website managed by <a href="http://www.mogmachine.com" target="_blank">mogmachine</a><span> | Powered by <a href="http://www.wordpress.org" target="_blank">WordPress</a>';
		}	
		
		function wpse126301_dashboard_columns() {
		    add_screen_option(
		        'layout_columns',
		        array(
		            'max'     => 2,
		            'default' => 1
		        )
		    );
		}
		add_action( 'admin_head-index.php', 'wpse126301_dashboard_columns' );
					
					
    
    if (function_exists('register_sidebar')) {
    	register_sidebar(array(
    		'name' => 'Sidebar Widgets',
    		'id'   => 'sidebar-widgets',
    		'description'   => 'These are widgets for the sidebar.',
    		'before_widget' => '<div id="%1$s" class="widget %2$s">',
    		'after_widget'  => '</div>',
    		'before_title'  => '<h2>',
    		'after_title'   => '</h2>'
    	));
    }
    
    add_filter('manage_upload_columns', 'size_column_register');
	function size_column_register($columns) {
	  $columns['dimensions'] = 'Dimensions';
	  return $columns;
	}
	add_action('manage_media_custom_column', 'size_column_display', 10, 2);
	function size_column_display($column_name, $post_id) {
	  if( 'dimensions' != $column_name || !wp_attachment_is_image($post_id))
	    return;
	    list($url, $width, $height) = wp_get_attachment_image_src($post_id, 'full');
	    echo esc_html("{$width}&times;{$height}");
}

// truncate string at word
function trim_excerpt_to_word($string, $limit, $break = ".", $pad = "...") {  
	if (strlen($string) <= $limit) return $string;
	if (false !== ($max = strpos($string, $break, $limit))) {
		if ($max < strlen($string) - 1) {
			$string = substr($string, 0, $max) . $pad;
		}
	}
	return $string;
}

//allow <span> in content editor
function override_mce_options($initArray) 
{
  $opts = '*[*]';
  $initArray['valid_elements'] = $opts;
  $initArray['extended_valid_elements'] = $opts;
  return $initArray;
 }
 add_filter('tiny_mce_before_init', 'override_mce_options'); 



if(function_exists('acf_add_options_page')) { 
 
	acf_add_options_page();
	//acf_add_options_sub_page('Frontpage Slider');
	//acf_add_options_sub_page('Site Options');
 
}

//add_image_size( 'header_image', 1600, 525, array( 'center', 'center' ) );
//add_image_size( '800_600_slider', 800, 600, true );

function add_file_types_to_uploads($file_types){
$new_filetypes = array();
$new_filetypes['svg'] = 'image/svg+xml';
$file_types = array_merge($file_types, $new_filetypes );
return $file_types;
}
add_action('upload_mimes', 'add_file_types_to_uploads');
 

/*
function revcon_change_post_label() {
    global $menu;
    global $submenu;
    $menu[5][0] = 'Case studies';
    $submenu['edit.php'][5][0] = 'Case studies';
    $submenu['edit.php'][10][0] = 'Add Case study';
    $submenu['edit.php'][16][0] = 'Case study Tags';
}
function revcon_change_post_object() {
    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name = 'Case studies';
    $labels->singular_name = 'Case study';
    $labels->add_new = 'Add Case study';
    $labels->add_new_item = 'Add Case study';
    $labels->edit_item = 'Edit Case study';
    $labels->new_item = 'Case study';
    $labels->view_item = 'View Case study';
    $labels->search_items = 'Search Case studies';
    $labels->not_found = 'No Case studies found';
    $labels->not_found_in_trash = 'No Case studies found in Trash';
    $labels->all_items = 'All Case studies';
    $labels->menu_name = 'Case studies';
    $labels->name_admin_bar = 'Case studies';
}
 
add_action( 'admin_menu', 'revcon_change_post_label' );
add_action( 'init', 'revcon_change_post_object' );


function my_custom_post_news() {
	$labels = array(
		'name'               => _x( 'News', 'post type general name' ),
		'singular_name'      => _x( 'News', 'post type singular name' ),
		'add_new'            => _x( 'Add News','news' ),
		'add_new_item'       => __( 'Add News' ),
		'edit_item'          => __( 'Edit News' ),
		'new_item'           => __( 'New News' ),
		'all_items'          => __( 'All News' ),
		'view_item'          => __( 'View News' ),
		'search_items'       => __( 'Search News' ),
		'not_found'          => __( 'No News found' ),
		'not_found_in_trash' => __( 'No News found in the Trash' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'News'
	);
	$args = array(
		'labels'        => $labels,
		'description'   => 'Holds our news and news specific data',
		'public'        => true,
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'has_archive'   => true,
		'hierarchical' => false,
	);
	register_post_type( 'news', $args );	
	
	
	register_taxonomy( 'categories', array('news'), array(
        'hierarchical' => true, 
        'label' => 'Categories', 
        'singular_label' => 'Category', 
        'rewrite' => array( 'slug' => 'categories', 'with_front'=> false )
        )
    );

    register_taxonomy_for_object_type( 'categories', 'news' ); 
    
}
add_action( 'init', 'my_custom_post_news' );

*/



?>