<?php
/*
Plugin Name: Custom Meta Fields
Description: Добавляет метаполя title, description и keywords для страниц и вставляет их в заголовки страниц.
Version: 1.0
Author: Ваше Имя
*/

// Создание метаполей
function cmf_add_meta_box() {
    add_meta_box(
        'cmf_meta_box',        // ID метабокса
        'SEO Meta Fields',     // Название метабокса
        'cmf_meta_box_html',   // Функция для вывода HTML содержимого метабокса
        'page',                // Тип экрана (page, post и т.д.)
        'normal',              // Позиция метабокса
        'default'              // Приоритет метабокса
    );
}
add_action('add_meta_boxes', 'cmf_add_meta_box');

function cmf_meta_box_html($post) {
    $title = get_post_meta($post->ID, '_cmf_title', true);
    $description = get_post_meta($post->ID, '_cmf_description', true);
    $keywords = get_post_meta($post->ID, '_cmf_keywords', true);

    ?>
    <label for="cmf_title">Title</label>
    <input type="text" id="cmf_title" name="cmf_title" value="<?php echo esc_attr($title); ?>" style="width:100%;" />
    <br/><br/>
    <label for="cmf_description">Description</label>
    <textarea id="cmf_description" name="cmf_description" style="width:100%;"><?php echo esc_textarea($description); ?></textarea>
    <br/><br/>
    <label for="cmf_keywords">Keywords</label>
    <input type="text" id="cmf_keywords" name="cmf_keywords" value="<?php echo esc_attr($keywords); ?>" style="width:100%;" />
    <?php
}

function cmf_save_postdata($post_id) {
    if (array_key_exists('cmf_title', $_POST)) {
        update_post_meta(
            $post_id,
            '_cmf_title',
            sanitize_text_field($_POST['cmf_title'])
        );
    }
    if (array_key_exists('cmf_description', $_POST)) {
        update_post_meta(
            $post_id,
            '_cmf_description',
            sanitize_textarea_field($_POST['cmf_description'])
        );
    }
    if (array_key_exists('cmf_keywords', $_POST)) {
        update_post_meta(
            $post_id,
            '_cmf_keywords',
            sanitize_text_field($_POST['cmf_keywords'])
        );
    }
}
add_action('save_post', 'cmf_save_postdata');

function cmf_add_meta_to_head() {
    if (is_page()) {
        global $post;
        $title = get_post_meta($post->ID, '_cmf_title', true);
        $description = get_post_meta($post->ID, '_cmf_description', true);
        $keywords = get_post_meta($post->ID, '_cmf_keywords', true);

        if ($title) {
            echo '<title>' . esc_html($title) . '</title>';
        }
        if ($description) {
            echo '<meta name="description" content="' . esc_attr($description) . '">';
        }
        if ($keywords) {
            echo '<meta name="keywords" content="' . esc_attr($keywords) . '">';
        }
    }
}
add_action('wp_head', 'cmf_add_meta_to_head');
