<?php
/*
Plugin Name: Advantages Plugin
Description: Плагин для добавления типа записи "Преимущества" и шорткода для вывода записей.
Version: 1.0
Author: Ваше Имя
*/

// Регистрируем новый тип записи "Преимущества"
function register_advantages_post_type() {
    $labels = array(
        'name'               => 'Преимущества',
        'singular_name'      => 'Преимущество'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'advantage'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'thumbnail')
    );

    register_post_type('advantage', $args);
}
add_action('init', 'register_advantages_post_type');

// Создаем шорткод для вывода преимуществ
function advantages_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'count' => 5,
        ),
        $atts,
        'advantages'
    );

    $query = new WP_Query(array(
        'post_type'      => 'advantage',
        'posts_per_page' => $atts['count']
    ));

    if ($query->have_posts()) {
        $output = '<div class="advantages-list">';
        while ($query->have_posts()) {
            $query->the_post();
            $output .= '<div class="advantage-item">';
            $output .= '<h3>' . get_the_title() . '</h3>';
            $output .= '<div>' . get_the_content() . '</div>';
            if (has_post_thumbnail()) {
                $output .= get_the_post_thumbnail(get_the_ID(), 'thumbnail');
            }
            $output .= '</div>';
        }
        $output .= '</div>';
    } else {
        $output = '<p>Преимущества не найдены.</p>';
    }

    wp_reset_postdata();

    return $output;
}
add_shortcode('advantages', 'advantages_shortcode');
// [advantages count="3"]
?>
