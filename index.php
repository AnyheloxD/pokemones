<?php
/*
Plugin Name: Plugin de Pokemon
Description: [buscar_pokemones]
Version: 1.0.0
Author: Anyhelo
*/


if (!defined('ABSPATH')) {
    exit;
}


define('POKEMON_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('POKEMON_PLUGIN_URL', plugin_dir_url(__FILE__));

// Incluye archivos necesarios
require_once POKEMON_PLUGIN_DIR . 'privado/inc/cpt_taxonomias.php';
require_once POKEMON_PLUGIN_DIR . 'privado/inc/api.php';
require_once POKEMON_PLUGIN_DIR . 'privado/inc/shortcode.php';
require_once POKEMON_PLUGIN_DIR . 'privado/inc/shortcode_ajax.php';
require_once POKEMON_PLUGIN_DIR . 'privado/inc/imagenes_procesar.php';
require_once POKEMON_PLUGIN_DIR . 'privado/inc/importacion_admin.php';


// Carga estilos y scripts cliente
function pokemon_cargar_recursos()
{

    if (!is_admin()) {
        wp_enqueue_style('pokemon-plugin-estilo', POKEMON_PLUGIN_URL . 'publico/assets/css/estilo_pokemon.css', [], '');
        wp_enqueue_script('pokemon-plugin-script', POKEMON_PLUGIN_URL . 'publico/assets/js/script_pokemon.js', ['jquery'], '1.0.0', true);
        wp_localize_script('pokemon-plugin-script', 'pokemonAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('pokemon_busqueda_nonce')
        ]);
    }
}
add_action('wp_enqueue_scripts', 'pokemon_cargar_recursos');


// Carga estilos y scripts admin
function pokemon_cargar_recursos_admin()
{
    $pantalla = get_current_screen();
    if ($pantalla->post_type === 'pokemones') {

        wp_enqueue_script('sweetalert', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', [], '1.0.0', true);
        wp_enqueue_style('pokemon-admin-estilo', POKEMON_PLUGIN_URL . 'privado/assets/css/admin_pokemon.css', [], '1.0.0');
        wp_enqueue_script('pokemon-admin-script', POKEMON_PLUGIN_URL . 'privado/assets/js/admin_pokemon.js', ['jquery', 'sweetalert'], '1.0.0', true);
        wp_localize_script('pokemon-admin-script', 'pokemonAdmin', [
            'import_url' => admin_url('admin.php?action=importar_pokemones')
        ]);
    }
}
add_action('admin_enqueue_scripts', 'pokemon_cargar_recursos_admin');
