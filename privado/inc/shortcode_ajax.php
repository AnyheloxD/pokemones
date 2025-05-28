<?php

// Función AJAX para obtener fortalezas según el tipo seleccionado
function pokemon_ajax_obtener_fortalezas()
{

    if (!check_ajax_referer('pokemon_busqueda_nonce', 'nonce', false)) {
        wp_send_json_error('Error de seguridad.');
    }


    $tipos = isset($_POST['tipo']) ? array_map('sanitize_text_field', (array)$_POST['tipo']) : [];
    $fortalezas = [];


    foreach ($tipos as $tipo) {
        $tipo_datos = pokemon_obtener_datos_pokeapi('type/' . $tipo);
        if ($tipo_datos && isset($tipo_datos['damage_relations']['double_damage_to'])) {
            foreach ($tipo_datos['damage_relations']['double_damage_to'] as $fuerte_contra) {
                $fortalezas[] = $fuerte_contra['name'];
            }
        }
    }


    $fortalezas = array_unique($fortalezas);


    $salida = '';
    foreach ($fortalezas as $fortaleza) {
        $salida .= '<option value="' . esc_attr($fortaleza) . '">' . esc_html(ucfirst($fortaleza)) . '</option>';
    }

    wp_send_json_success($salida);
}
add_action('wp_ajax_pokemon_obtener_fortalezas', 'pokemon_ajax_obtener_fortalezas');
add_action('wp_ajax_nopriv_pokemon_obtener_fortalezas', 'pokemon_ajax_obtener_fortalezas');

// Función  para procesar la búsqueda de Pokémon
function pokemon_ajax_buscar_pokemones()
{
    if (!check_ajax_referer('pokemon_busqueda_nonce', 'nonce', false)) {
        wp_send_json_error('Error de seguridad. Por favor, intenta de nuevo.');
    }

    $buscar = isset($_POST['buscar']) ? sanitize_text_field($_POST['buscar']) : '';
    $tipos = isset($_POST['tipo']) ? array_map('sanitize_text_field', (array)$_POST['tipo']) : [];
    $fortalezas = isset($_POST['fortaleza']) ? array_map('sanitize_text_field', (array)$_POST['fortaleza']) : [];

    $pokemon_list = pokemon_obtener_datos_pokeapi('pokemon?limit=20');
    $salida = '';

    $fortalezas_relaciones = [];
    foreach ($fortalezas as $fortaleza) {
        $tipo_datos = pokemon_obtener_datos_pokeapi('type/' . $fortaleza);
        if ($tipo_datos && isset($tipo_datos['damage_relations']['double_damage_to'])) {
            $fortalezas_relaciones[$fortaleza] = array_map(function ($t) {
                return $t['name'];
            }, $tipo_datos['damage_relations']['double_damage_to']);
        }
    }

    if ($pokemon_list && isset($pokemon_list['results'])) {
        foreach ($pokemon_list['results'] as $pokemon) {
            $datos = pokemon_obtener_datos_pokeapi('pokemon/' . $pokemon['name']);
            if ($datos) {
                $coincide_nombre = true;
                $coincide_tipo = true;
                $coincide_fortaleza = true;

                if (!empty($buscar) && stripos($datos['name'], $buscar) === false) {
                    $coincide_nombre = false;
                }

                if (!empty($tipos)) {
                    $tipos_pokemon = array_map(function ($t) {
                        return $t['type']['name'];
                    }, $datos['types']);
                    $coincide_tipo = !empty(array_intersect($tipos, $tipos_pokemon));
                }

                if (!empty($fortalezas)) {
                    $tipos_pokemon = array_map(function ($t) {
                        return $t['type']['name'];
                    }, $datos['types']);
                    $coincide_fortaleza = false;
                    foreach ($tipos_pokemon as $tipo_pokemon) {
                        $tipo_datos = pokemon_obtener_datos_pokeapi('type/' . $tipo_pokemon);
                        if ($tipo_datos && isset($tipo_datos['damage_relations']['double_damage_to'])) {
                            $tipos_fuerte_contra = array_map(function ($t) {
                                return $t['name'];
                            }, $tipo_datos['damage_relations']['double_damage_to']);
                            if (!empty(array_intersect($fortalezas, $tipos_fuerte_contra))) {
                                $coincide_fortaleza = true;
                                break;
                            }
                        }
                    }
                }


                if ($coincide_nombre && $coincide_tipo && $coincide_fortaleza) {
                    $salida .= '<div class="pokemon-tarjeta">';
                    $salida .= '<h3>' . esc_html(ucfirst($datos['name'])) . '</h3>';
                    if (isset($datos['sprites']['front_default']) && !empty($datos['sprites']['front_default'])) {
                        $salida .= '<img src="' . esc_url($datos['sprites']['front_default']) . '" alt="' . esc_attr(ucfirst($datos['name'])) . '" style="max-width: 100px; height: auto;">';
                    } else {
                        $salida .= '<p>' . __('Sin imagen disponible', 'pokemon-plugin') . '</p>';
                    }
                    $salida .= '<p>Tipo: ';
                    foreach ($datos['types'] as $tipo) {
                        $salida .= esc_html(ucfirst($tipo['type']['name'])) . ' ';
                    }
                    $salida .= '</p>';
                    $salida .= '<p>Fuerte contra: ';
                    $tipos_pokemon = array_map(function ($t) {
                        return $t['type']['name'];
                    }, $datos['types']);
                    $tipos_fuerte_contra = [];
                    foreach ($tipos_pokemon as $tipo_pokemon) {
                        $tipo_datos = pokemon_obtener_datos_pokeapi('type/' . $tipo_pokemon);
                        if ($tipo_datos && isset($tipo_datos['damage_relations']['double_damage_to'])) {
                            foreach ($tipo_datos['damage_relations']['double_damage_to'] as $fuerte_contra) {
                                $tipos_fuerte_contra[] = ucfirst($fuerte_contra['name']);
                            }
                        }
                    }
                    $tipos_fuerte_contra = array_unique($tipos_fuerte_contra);
                    $salida .= esc_html(implode(', ', $tipos_fuerte_contra)) . '</p>';
                    $salida .= '</div>';
                }
            }
        }
    }


    if (empty($salida)) {
        $salida = '<p> No se encontraron Pokémon </p>';
    }

    wp_send_json_success($salida);
}
add_action('wp_ajax_pokemon_buscar_pokemones', 'pokemon_ajax_buscar_pokemones');
add_action('wp_ajax_nopriv_pokemon_buscar_pokemones', 'pokemon_ajax_buscar_pokemones');