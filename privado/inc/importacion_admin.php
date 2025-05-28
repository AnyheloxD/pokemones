<?php

// boton importacion 
function pokemon_agregar_boton_importacion_admin()
{

    $pantalla = get_current_screen();
    if ($pantalla->post_type === 'pokemones') {
        echo '<a href="" class="button btn_importar_poke">Importar 20 Pokémon</a>';
    }
}
add_action('admin_notices', 'pokemon_agregar_boton_importacion_admin');



// importar Pokemon 
function pokemon_importar_pokemones()
{

    if (!current_user_can('manage_options')) {
        wp_die('No tienes permisos para realizar esta acción.');
    }


    $pokemon_list = pokemon_obtener_datos_pokeapi('pokemon?limit=20');
    if ($pokemon_list && isset($pokemon_list['results'])) {
        foreach ($pokemon_list['results'] as $pokemon) {
            $datos = pokemon_obtener_datos_pokeapi('pokemon/' . $pokemon['name']);
            if ($datos) {

                $post_id = wp_insert_post([
                    'post_title'   => sanitize_text_field(ucfirst($datos['name'])),
                    'post_content' => 'Pokémon importado desde PokéAPI.',
                    'post_type'    => 'pokemones',
                    'post_status'  => 'publish',
                ]);

                if ($post_id) {

                    $tipos = [];
                    foreach ($datos['types'] as $tipo) {
                        $tipos[] = sanitize_text_field($tipo['type']['name']);
                    }
                    wp_set_object_terms($post_id, $tipos, 'tipo');


                    $fortalezas = [];
                    foreach ($tipos as $tipo) {
                        $tipo_datos = pokemon_obtener_datos_pokeapi('type/' . $tipo);
                        if ($tipo_datos && isset($tipo_datos['damage_relations']['double_damage_to'])) {
                            foreach ($tipo_datos['damage_relations']['double_damage_to'] as $fuerte_contra) {
                                $fortalezas[] = sanitize_text_field($fuerte_contra['name']);
                            }
                        }
                    }
                    $fortalezas = array_unique($fortalezas); 
                    if (!empty($fortalezas)) {
                        wp_set_object_terms($post_id, $fortalezas, 'fortaleza');
                    } else {
                        error_log('No se encontraron fortalezas para el Pokémon: ' . $datos['name']);
                    }

                    if (isset($datos['sprites']['front_default']) && !empty($datos['sprites']['front_default'])) {
                        $attachment_id = subir_imagen_pokemon(
                            $datos['sprites']['front_default'],
                            $datos['name'],
                            $post_id
                        );
                        if ($attachment_id) {
                            set_post_thumbnail($post_id, $attachment_id);
                        } else {
                            error_log('No se pudo subir la imagen para el Pokémon: ' . $datos['name']);
                        }
                    } else {
                        error_log('No se encontró imagen front_default para el Pokémon: ' . $datos['name']);
                    }
                } else {
                    error_log('No se pudo crear el post para el Pokémon: ' . $datos['name']);
                }
            } else {
                error_log('No se pudieron obtener datos de la API para el Pokémon: ' . $pokemon['name']);
            }
        }
        wp_redirect(admin_url('edit.php?post_type=pokemones&imported=1'));
        exit;
    } else {
        error_log('No se pudieron obtener los Pokémon de la PokéAPI');
        wp_redirect(admin_url('edit.php?post_type=pokemones&imported=0'));
        exit;
    }
}
add_action('admin_action_importar_pokemones', 'pokemon_importar_pokemones');