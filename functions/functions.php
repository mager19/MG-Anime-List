<?php
function fetch_anime_data()
{
    // URL de la API de animes
    $url = 'https://kitsu.io/api/edge/anime';

    // Realizamos la petición a la API
    $response = wp_remote_get($url);

    // Verificamos si hubo un error en la respuesta
    if (is_wp_error($response)) {
        wp_send_json_error('Error fetching data from API');
    }

    // Obtenemos y decodificamos la respuesta en formato JSON
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    // Seleccionamos 5 animes aleatorios
    $random_animes = array_rand($data['data'], 5);
    $selected_animes = array();

    foreach ($random_animes as $index) {
        $anime = $data['data'][$index];

        // Recogemos los datos requeridos
        $anime_data = array(
            'id' => $anime['id'],
            'synopsis' => $anime['attributes']['synopsis'],
            'title' => isset($anime['attributes']['titles']['en']) ? $anime['attributes']['titles']['en'] : 'Title not available',
            'posterImage' => $anime['attributes']['posterImage']['medium'],
            'coverImage' => isset($anime['attributes']['coverImage']['original']) ? $anime['attributes']['coverImage']['original'] : '',
            'categories' => array() // Aquí almacenaremos las categorías
        );

        // Obtener categorías de la API del anime
        $categories_url = 'https://kitsu.io/api/edge/anime/' . $anime['id'] . '/categories';
        $categories_response = wp_remote_get($categories_url);

        if (!is_wp_error($categories_response)) {
            $categories_body = wp_remote_retrieve_body($categories_response);
            $categories_data = json_decode($categories_body, true);

            // Extraemos solo las dos primeras categorías
            $categories = array_slice(array_map(function ($category) {
                return $category['attributes']['title'];
            }, $categories_data['data']), 0, 2);

            $anime_data['categories'] = $categories;
        }

        // Añadimos los datos de este anime al array final
        $selected_animes[] = $anime_data;
    }

    // Enviar los datos al frontend
    wp_send_json_success($selected_animes);
}
add_action('wp_ajax_fetch_anime_data', 'fetch_anime_data');
add_action('wp_ajax_nopriv_fetch_anime_data', 'fetch_anime_data');
