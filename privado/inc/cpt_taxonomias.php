<?php

function pokemon_registrar_cpt_y_taxonomias()
{
    // Registra el Custom Post Type: pokemones
    $etiquetas = [
        'name'               => _x('Pokémon', 'Nombre general del tipo de post', 'pokemon-plugin'),
        'singular_name'      => _x('Pokémon', 'Nombre singular del tipo de post', 'pokemon-plugin'),
        'menu_name'          => _x('Pokémon', 'Texto del menú en el admin', 'pokemon-plugin'),
        'name_admin_bar'     => _x('Pokémon', 'Añadir nuevo en la barra de herramientas', 'pokemon-plugin'),
        'add_new'            => __('Añadir nuevo', 'pokemon-plugin'),
        'add_new_item'       => __('Añadir nuevo Pokémon', 'pokemon-plugin'),
        'new_item'           => __('Nuevo Pokémon', 'pokemon-plugin'),
        'edit_item'          => __('Editar Pokémon', 'pokemon-plugin'),
        'view_item'          => __('Ver Pokémon', 'pokemon-plugin'),
        'all_items'          => __('Todos los Pokémon', 'pokemon-plugin'),
        'search_items'       => __('Buscar Pokémon', 'pokemon-plugin'),
        'not_found'          => __('No se encontraron Pokémon.', 'pokemon-plugin'),
    ];
    $args = [
        'labels'             => $etiquetas,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'pokemones'],
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'supports'           => ['title', 'editor', 'thumbnail'],
        'show_in_rest'       => true,
    ];
    register_post_type('pokemones', $args);

    // Registra la taxonomía: tipo
    $etiquetas_tipo = [
        'name'              => _x('Tipos', 'Nombre general de la taxonomía', 'pokemon-plugin'),
        'singular_name'     => _x('Tipo', 'Nombre singular de la taxonomía', 'pokemon-plugin'),
        'search_items'      => __('Buscar tipos', 'pokemon-plugin'),
        'all_items'         => __('Todos los tipos', 'pokemon-plugin'),
        'edit_item'         => __('Editar tipo', 'pokemon-plugin'),
        'update_item'       => __('Actualizar tipo', 'pokemon-plugin'),
        'add_new_item'      => __('Añadir nuevo tipo', 'pokemon-plugin'),
        'new_item_name'     => __('Nombre del nuevo tipo', 'pokemon-plugin'),
        'menu_name'         => __('Tipos', 'pokemon-plugin'),
    ];
    $args_tipo = [
        'hierarchical'      => true,
        'labels'            => $etiquetas_tipo,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'tipo'],
        'show_in_rest'      => true,
    ];
    register_taxonomy('tipo', ['pokemones'], $args_tipo);

    // Registra la taxonomía: fortaleza
    $etiquetas_fortaleza = [
        'name'              => _x('Fortalezas', 'Nombre general de la taxonomía', 'pokemon-plugin'),
        'singular_name'     => _x('Fortaleza', 'Nombre singular de la taxonomía', 'pokemon-plugin'),
        'search_items'      => __('Buscar fortalezas', 'pokemon-plugin'),
        'all_items'         => __('Todas las fortalezas', 'pokemon-plugin'),
        'edit_item'         => __('Editar fortaleza', 'pokemon-plugin'),
        'update_item'       => __('Actualizar fortaleza', 'pokemon-plugin'),
        'add_new_item'      => __('Añadir nueva fortaleza', 'pokemon-plugin'),
        'new_item_name'     => __('Nombre de la nueva fortaleza', 'pokemon-plugin'),
        'menu_name'         => __('Fortalezas', 'pokemon-plugin'),
    ];
    $args_fortaleza = [
        'hierarchical'      => true,
        'labels'            => $etiquetas_fortaleza,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'fortaleza'],
        'show_in_rest'      => true,
    ];
    register_taxonomy('fortaleza', ['pokemones'], $args_fortaleza);
}
add_action('init', 'pokemon_registrar_cpt_y_taxonomias');


// Columna  para la imagen destacada 
function pokemon_agregar_columna_imagen_destacada($columns)
{

    $new_columns = [];
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'cb') {
            $new_columns['pokemon_imagen_destacada'] = __('Imagen', 'pokemon-plugin');
        }
    }
    return $new_columns;
}
add_filter('manage_pokemones_posts_columns', 'pokemon_agregar_columna_imagen_destacada');


function pokemon_mostrar_columna_imagen_destacada($column_name, $post_id)
{
    if ($column_name === 'pokemon_imagen_destacada') {

        $thumbnail_id = get_post_thumbnail_id($post_id);
        if ($thumbnail_id) {

            $imagen = wp_get_attachment_image($thumbnail_id, 'thumbnail', false, ['style' => 'max-width: 50px; height: auto;']);
            echo $imagen;
        }
    }
}
add_action('manage_pokemones_posts_custom_column', 'pokemon_mostrar_columna_imagen_destacada', 10, 2);