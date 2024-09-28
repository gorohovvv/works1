<?php
/*
Plugin Name: Banner Slider Plugin
Description: Плагин для создания баннеров и отображения их в слайдере через шорткод.
Version: 1.0
Author: Ваше имя
*/

// Регистрация кастомного типа записи "Баннеры"
function bsp_register_post_type() {
    register_post_type('banners', array(
        'labels' => array(
            'name' => 'Баннеры',
            'singular_name' => 'Баннер',
            'add_new' => 'Добавить новый',
            'add_new_item' => 'Добавить новый баннер',
            'edit_item' => 'Редактировать баннер',
            'new_item' => 'Новый баннер',
            'view_item' => 'Просмотреть баннер',
            'search_items' => 'Найти баннеры',
            'not_found' => 'Баннеров не найдено',
            'not_found_in_trash' => 'В корзине баннеров не найдено',
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'menu_position' => 5,
        'menu_icon' => 'dashicons-images-alt2',
    ));
}
add_action('init', 'bsp_register_post_type');

// Добавление метабокса для выбора количества баннеров
function bsp_add_meta_boxes() {
    add_meta_box(
        'bsp_meta_box',
        'Настройки слайдера',
        'bsp_meta_box_callback',
        'page',
        'side'
    );
}
add_action('add_meta_boxes', 'bsp_add_meta_boxes');

function bsp_meta_box_callback($post) {
    $value = get_post_meta($post->ID, '_bsp_num_banners', true);
    ?>
    <label for="bsp_num_banners">Количество баннеров для отображения:</label>
    <select name="bsp_num_banners" id="bsp_num_banners">
        <?php for ($i = 1; $i <= 10; $i++) : ?>
            <option value="<?php echo $i; ?>" <?php selected($value, $i); ?>><?php echo $i; ?></option>
        <?php endfor; ?>
    </select>
    <?php
}

function bsp_save_meta_box_data($post_id) {
    if (array_key_exists('bsp_num_banners', $_POST)) {
        update_post_meta(
            $post_id,
            '_bsp_num_banners',
            $_POST['bsp_num_banners']
        );
    }
}
add_action('save_post', 'bsp_save_meta_box_data');

// Добавление чекбокса для выбора отображения баннеров в слайдере
function bsp_add_banner_meta_boxes() {
    add_meta_box(
        'bsp_banner_featured_meta_box',
        'Выбор слайдера',
        'bsp_banner_featured_meta_box_callback',
        'banners',
        'side'
    );
}
add_action('add_meta_boxes', 'bsp_add_banner_meta_boxes');

function bsp_banner_featured_meta_box_callback($post) {
    $value = get_post_meta($post->ID, '_bsp_featured', true);
    ?>
    <label for="bsp_featured">Отображать в слайдере:</label>
    <input type="checkbox" name="bsp_featured" id="bsp_featured" value="yes" <?php checked($value, 'yes'); ?> />
    <?php
}

function bsp_save_banner_meta_box_data($post_id) {
    if (array_key_exists('bsp_featured', $_POST)) {
        update_post_meta($post_id, '_bsp_featured', $_POST['bsp_featured']);
    } else {
        delete_post_meta($post_id, '_bsp_featured');
    }
}
add_action('save_post', 'bsp_save_banner_meta_box_data');

// Шорткод для вывода баннеров в слайдере
function bsp_banner_slider_shortcode($atts) {
    $atts = shortcode_atts(array(
        'num' => 5,
    ), $atts, 'banner_slider');

    $args = array(
        'post_type' => 'banners',
        'posts_per_page' => $atts['num'],
        'meta_query' => array(
            array(
                'key' => '_bsp_featured',
                'value' => 'yes',
                'compare' => '='
            )
        )
    );

    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        ob_start();
        ?>
        <div class="banner-slider-container">
            <button class="slide-nav slide-prev" id="prevSlide">&#10094;</button>
            <div class="banner-slider" id="banner-slider">
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <div class="banner-slide">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="banner-image"><?php the_post_thumbnail('full'); ?></div>
                        <?php endif; ?>
                        <div class="banner-content">
                            <div><?php the_content(); ?></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <button class="slide-nav slide-next" id="nextSlide">&#10095;</button>
        </div>
        <style>
            .banner-slider-container {
                position: relative;
                width: 100%;
                overflow: hidden;
            }
            .banner-slider {
                display: flex;
                transition: transform 0.5s ease-in-out;
            }
            .banner-slide {
                min-width: 100%;
                box-sizing: border-box;
            }
            .banner-slide img {
                width: 100%;
                max-height: 300px;
            }
            .slide-nav {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                background-color: rgba(0,0,0,0.5);
                color: white;
                border: none;
                padding: 10px;
                cursor: pointer;
                z-index: 10;
            }
            .slide-prev {
                left: 10px;
            }
            .slide-next {
                right: 10px;
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var slider = document.getElementById('banner-slider');
                var slides = slider.getElementsByClassName('banner-slide');
                var totalSlides = slides.length;
                var currentSlide = 0;

                function showSlide(index) {
                    slider.style.transform = 'translateX(' + (-index * 100) + '%)';
                }

                document.getElementById('prevSlide').addEventListener('click', function() {
                    currentSlide = (currentSlide > 0) ? currentSlide - 1 : totalSlides - 1;
                    showSlide(currentSlide);
                });

                document.getElementById('nextSlide').addEventListener('click', function() {
                    currentSlide = (currentSlide < totalSlides - 1) ? currentSlide + 1 : 0;
                    showSlide(currentSlide);
                });

                showSlide(currentSlide);
            });
        </script>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    } else {
        return '<p>Баннеров не найдено.</p>';
    }
}
add_shortcode('banner_slider', 'bsp_banner_slider_shortcode');
// [banner_slider num="2"]