<?php
/*
Plugin Name: Beyond Posts
Plugin URI
Description: Related posts under each post with a loader.It s made with WP-API and AJAX
Version:     1.0.0
Author:      Konstantinos Pap
Author URI:  http://roomwithview.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: beyondposts
*/

// Add fields to js
function beyondposts_register_fields() {
	// Add Author Name
	register_api_field( 'post',
		'author_name',
		array(
			'get_callback'		=> 'beyondposts_get_author_name',
			'update_callback'	=> null,
			'schema'			=> null
		)
	);

	// Add Image
	register_api_field( 'post',
		'featured_image_src',
		array(
			'get_callback'		=> 'beyondposts_get_image_src',
			'update_callback'	=> null,
			'schema'			=> null
		)
	);
}

function beyondposts_get_author_name( $object, $field_name, $request ) {
	return get_the_author_meta( 'display_name' );
}

function beyondposts_get_image_src( $object, $field_name, $request ) {
	$feat_img_array = wp_get_attachment_image_src( $object[ 'featured_image' ], 'thumbnail', true );
	return $feat_img_array[0];
}

add_action( 'rest_api_init', 'beyondposts_register_fields');


// Hook them
function beyondposts_scripts() {
	if( is_single() && is_main_query() ) {
    // Get CSS and JS
		wp_enqueue_style( 'beyondposts-styles', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), '', 'all' );
		wp_enqueue_script( 'beyondposts-script', plugin_dir_url( __FILE__ ) . 'js/beyondposts.ajax.js', array('jquery'), '', true );

		// Get the current post ID
		global $post;
		$post_id = $post->ID;

		// Use wp_localize_script to pass to ajax.js
		wp_localize_script( 'beyondposts-script', 'postdata',
			array(
				'post_id' => $post_id,
				'json_url' => beyondposts_get_json_query()
			)
		);

	}
}
add_action( 'wp_enqueue_scripts', 'beyondposts_scripts' );



function beyondposts_get_json_query() {

    // Get all the categories a
    $cats = get_the_category();

    // Make an array of the categories
    $cat_ids = array();

    
    foreach ($cats as $cat) {
        $cat_ids[] = $cat->term_id;
    }

    // Set up the query variables 
    $args = array(
        'filter[cat]' => implode(",", $cat_ids),
        'filter[posts_per_page]' => 5
    );

    // everything together
    $url = add_query_arg( $args, rest_url( 'wp/v2/posts') );

    return $url;

}

// HTML bottom of a post
function beyondposts_base_html() {
	// container
	$base  = '<section id="related-posts" class="related-posts">';
	$base .= '<a href="#" class="get-related-posts">See Beyond Posts</a>';
 	$base .= '<div class="ajax-loader"><img src="' . plugin_dir_url( __FILE__ ) . 'css/loader.svg" width="32" height="32" /></div>';
	$base .= '</section><!-- .related-posts -->';

	return $base;
}

// bottom of single posts
function beyondposts_display($content){
	if( is_single() && is_main_query() ) {
	    $content .= beyondposts_base_html();
	}
	return $content;
}
add_filter('the_content','beyondposts_display');
