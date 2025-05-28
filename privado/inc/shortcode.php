<?php
// Shortcode para buscar y mostrar Pokemon
function shortcode_buscar_pokemones()
{
    $tipos = pokemon_obtener_datos_pokeapi('type');
    $salida = '<div class="pokemon-buscador">';

    $salida .= '<form id="pokemon-buscador-form" action="">';
    $salida .= '<input type="text" name="buscar" placeholder="Buscar Pokémon por nombre" value="">';
    $salida .= '<div class="row2">';
    $salida .= '<select name="tipo[]" id="tipo" multiple>';
    if ($tipos && isset($tipos['results'])) {
        foreach ($tipos['results'] as $tipo) {
            $nombre = esc_html(ucfirst($tipo['name']));
            $salida .= '<option value="' . esc_attr($tipo['name']) . '">' . $nombre . '</option>';
        }
    }
    $salida .= '</select>';

    $salida .= '<select name="fortaleza[]" id="fortaleza" multiple >';
    $salida .= '</select>';
    $salida .= '</div>';
    $salida .= '<button type="submit">Buscar</button>';
    $salida .= '</form>';

    $salida .= '<div id="resultado">';

    $pokemon_list = pokemon_obtener_datos_pokeapi('pokemon?limit=20');
    if ($pokemon_list && isset($pokemon_list['results'])) {
        foreach ($pokemon_list['results'] as $pokemon) {
            $datos = pokemon_obtener_datos_pokeapi('pokemon/' . $pokemon['name']);
            if ($datos) {
                $salida .= '<div class="pokemon-tarjeta">';

                if (isset($datos['sprites']['front_default']) && !empty($datos['sprites']['front_default'])) {
                    $salida .= '<img src="' . esc_url($datos['sprites']['front_default']) . '" alt="' . esc_attr(ucfirst($datos['name'])) . '" style="max-width: 100px; height: auto;">';
                } else {
                    $salida .= '<p>' . __('Sin imagen disponible', 'pokemon-plugin') . '</p>';
                }
                $salida .= '<h3>' . esc_html(ucfirst($datos['name'])) . '</h3>';
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
    $salida .= '</div>';
    $salida .= '</div>';

    return $salida;
}
add_shortcode('buscar_pokemones', 'shortcode_buscar_pokemones');