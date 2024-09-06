<?php
function fetch_anime_data()
{

    // URL de la API de animes
    $url = 'https://kitsu.io/api/edge/anime?page[limit]=3';

    // Realizamos la petición a la API
    $response = wp_remote_get($url);

    // Verificamos si hubo un error en la respuesta
    if (is_wp_error($response)) {
        wp_send_json_error('Error fetching data from API');
    }

    // Obtenemos y decodificamos la respuesta en formato JSON
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    foreach ($data['data'] as $anime) {
        $post_title = isset($anime['attributes']['titles']['en']) ? $anime['attributes']['titles']['en'] : 'No title';
        $post_content = isset($anime['attributes']['synopsis']) ? $anime['attributes']['synopsis'] : 'No synopsis';
        $cover_image_url = isset($anime['attributes']['coverImage']['original']) ? $anime['attributes']['coverImage']['original'] : '';

        // Crear el post
        $post_data = array(
            'post_title'   => $post_title,
            'post_content' => $post_content,
            'post_status'  => 'publish',
            'post_type'    => 'mg-anime-list',
        );

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            wp_send_json_error('Failed to create post');
            return;
        }

        // Subir la imagen y asignarla al post
        if ($cover_image_url) {
            $image_id = upload_image_from_url($cover_image_url, $post_id);
            if (!is_wp_error($image_id)) {
                set_post_thumbnail($post_id, $image_id);
            }
        }
    }


    // Enviar los datos al frontend
    wp_send_json_success('Posts created successfully.');
}
add_action('wp_ajax_fetch_anime_data', 'fetch_anime_data');
add_action('wp_ajax_nopriv_fetch_anime_data', 'fetch_anime_data');

// Función para subir una imagen desde una URL
function upload_image_from_url($image_url, $post_id)
{
    $response = wp_remote_get($image_url);

    if (is_wp_error($response)) {
        return $response;
    }

    $image_data = wp_remote_retrieve_body($response);

    $upload = wp_upload_bits(basename($image_url), null, $image_data);

    if ($upload['error']) {
        return new WP_Error('upload_error', $upload['error']);
    }

    $wp_filetype = wp_check_filetype($upload['file']);
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title'     => sanitize_file_name(basename($upload['file'])),
        'post_content'   => '',
        'post_status'    => 'inherit'
    );

    $attachment_id = wp_insert_attachment($attachment, $upload['file'], $post_id);

    if (is_wp_error($attachment_id)) {
        return $attachment_id;
    }

    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
    wp_update_attachment_metadata($attachment_id, $attachment_data);

    return $attachment_id;
}
