<?php
/*
Plugin Name: Custom Services
Description: Adds a custom post type for Services with custom fields for price, duration, and a featured checkbox. Includes shortcodes for displaying services.
Version: 1.1
Author: Your Name
*/

// Register Custom Post Type
function create_services_cpt() {
    $labels = array(
        'name'                  => _x( 'Services', 'Post Type General Name', 'textdomain' ),
        'singular_name'         => _x( 'Service', 'Post Type Singular Name', 'textdomain' ),
        'menu_name'             => __( 'Services', 'textdomain' ),
        'name_admin_bar'        => __( 'Service', 'textdomain' ),
        'archives'              => __( 'Service Archives', 'textdomain' ),
        'attributes'            => __( 'Service Attributes', 'textdomain' ),
        'parent_item_colon'     => __( 'Parent Service:', 'textdomain' ),
        'all_items'             => __( 'All Services', 'textdomain' ),
        'add_new_item'          => __( 'Add New Service', 'textdomain' ),
        'add_new'               => __( 'Add New', 'textdomain' ),
        'new_item'              => __( 'New Service', 'textdomain' ),
        'edit_item'             => __( 'Edit Service', 'textdomain' ),
        'update_item'           => __( 'Update Service', 'textdomain' ),
        'view_item'             => __( 'View Service', 'textdomain' ),
        'view_items'            => __( 'View Services', 'textdomain' ),
        'search_items'          => __( 'Search Service', 'textdomain' ),
        'not_found'             => __( 'Not found', 'textdomain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'textdomain' ),
        'featured_image'        => __( 'Featured Image', 'textdomain' ),
        'set_featured_image'    => __( 'Set featured image', 'textdomain' ),
        'remove_featured_image' => __( 'Remove featured image', 'textdomain' ),
        'use_featured_image'    => __( 'Use as featured image', 'textdomain' ),
        'insert_into_item'      => __( 'Insert into service', 'textdomain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this service', 'textdomain' ),
        'items_list'            => __( 'Services list', 'textdomain' ),
        'items_list_navigation' => __( 'Services list navigation', 'textdomain' ),
        'filter_items_list'     => __( 'Filter services list', 'textdomain' ),
    );
    $args = array(
        'label'                 => __( 'Service', 'textdomain' ),
        'description'           => __( 'Custom post type for services', 'textdomain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
        'taxonomies'            => array( 'category', 'post_tag' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );
    register_post_type( 'service', $args );
}
add_action( 'init', 'create_services_cpt', 0 );

// Add meta boxes for price, duration, and featured
function add_services_meta_boxes() {
    add_meta_box(
        'service_details',
        __( 'Service Details', 'textdomain' ),
        'render_service_details_box',
        'service',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'add_services_meta_boxes' );

function render_service_details_box($post) {
    wp_nonce_field( basename( __FILE__ ), 'service_nonce' );
    $service_stored_meta = get_post_meta( $post->ID );
    ?>

    <p>
        <label for="meta-price" class="service-row-title"><?php _e( 'Price', 'textdomain' ) ?></label>
        <input type="text" name="meta-price" id="meta-price" value="<?php if ( isset ( $service_stored_meta['meta-price'] ) ) echo $service_stored_meta['meta-price'][0]; ?>" />
    </p>

    <p>
        <label for="meta-duration" class="service-row-title"><?php _e( 'Duration', 'textdomain' ) ?></label>
        <input type="text" name="meta-duration" id="meta-duration" value="<?php if ( isset ( $service_stored_meta['meta-duration'] ) ) echo $service_stored_meta['meta-duration'][0]; ?>" />
    </p>

    <p>
        <label for="meta-featured" class="service-row-title"><?php _e( 'Featured', 'textdomain' ) ?></label>
        <input type="checkbox" name="meta-featured" id="meta-featured" <?php if ( isset ( $service_stored_meta['meta-featured'] ) ) checked( $service_stored_meta['meta-featured'][0], 'yes' ); ?> />
    </p>

    <?php
}

function save_service_meta( $post_id ) {
    // Check save status
    if ( ! isset( $_POST['service_nonce'] ) || ! wp_verify_nonce( $_POST['service_nonce'], basename( __FILE__ ) ) ) {
        return $post_id;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return $post_id;
    }

    // Save meta fields
    if ( isset( $_POST['meta-price'] ) ) {
        update_post_meta( $post_id, 'meta-price', sanitize_text_field( $_POST['meta-price'] ) );
    }
    if ( isset( $_POST['meta-duration'] ) ) {
        update_post_meta( $post_id, 'meta-duration', sanitize_text_field( $_POST['meta-duration'] ) );
    }
    if ( isset( $_POST['meta-featured'] ) ) {
        update_post_meta( $post_id, 'meta-featured', 'yes' );
    } else {
        update_post_meta( $post_id, 'meta-featured', 'no' );
    }
}
add_action( 'save_post', 'save_service_meta' );

// Shortcode to display services
function services_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'number' => '5',
            'featured' => 'false',
        ),
        $atts,
        'services'
    );

    $meta_query = array();

    if ( 'true' === $atts['featured'] ) {
        $meta_query[] = array(
            'key'     => 'meta-featured',
            'value'   => 'yes',
            'compare' => '=',
        );
    }

    $query = new WP_Query( array(
        'post_type'      => 'service',
        'posts_per_page' => $atts['number'],
        'meta_query'     => $meta_query,
    ) );

    $output = '<div class="services">';

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $output .= '<div class="service">';
            $output .= '<a href="' . get_permalink() . '">';
            if ( has_post_thumbnail() ) {
                $output .= get_the_post_thumbnail( get_the_ID(), 'medium' );
            }
            $output .= '</a>';
            $output .= '<h2><a href="' . get_permalink() . '">' . get_the_title() . '</a></h2>';
            $output .= '<div class="service-excerpt">' . get_the_excerpt() . '</div>';
            $output .= '</div>';
        }
        wp_reset_postdata();
    }

    $output .= '</div>';

    return $output;
}
add_shortcode( 'services', 'services_shortcode' );
// [services number="2" featured="true"]


// Shortcode to display service meta fields
function service_meta_shortcode( $atts ) {
    global $post;

    $price = get_post_meta( $post->ID, 'meta-price', true );
    $duration = get_post_meta( $post->ID, 'meta-duration', true );

    $output = '<div class="service-meta">';
    if ( $price ) {
        $output .= '<p><strong>Price:</strong> ' . esc_html( $price ) . '</p>';
    }
    if ( $duration ) {
        $output .= '<p><strong>Duration:</strong> ' . esc_html( $duration ) . '</p>';
    }
    $output .= '</div>';

    return $output;
}
add_shortcode( 'service_meta', 'service_meta_shortcode' );
// [service_meta]
?>
