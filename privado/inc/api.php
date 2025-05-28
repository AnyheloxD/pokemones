<?php

function pokemon_obtener_datos_pokeapi($endpoint)
{

    $url = 'https://pokeapi.co/api/v2/' . $endpoint;
    $respuesta = wp_remote_get(esc_url_raw($url));


    if (is_wp_error($respuesta)) {
        return false;
    }

    $cuerpo = wp_remote_retrieve_body($respuesta);
    $datos = json_decode($cuerpo, true);


    if (empty($datos)) {
        return false;
    }

    return $datos;
}