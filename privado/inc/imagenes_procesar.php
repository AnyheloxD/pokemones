<?php

function subir_imagen_pokemon($url_imagen, $nombre_pokemon, $post_id)
{
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $respuesta = wp_remote_get(esc_url_raw($url_imagen));
    if (is_wp_error($respuesta)) {
        error_log('Error al descargar la imagen del Pokémon: ' . $nombre_pokemon);
        return false;
    }

    $cuerpo = wp_remote_retrieve_body($respuesta);
    $tipo = wp_remote_retrieve_header($respuesta, 'content-type');
    $extension = 'png';
    if ($tipo === 'image/jpeg') {
        $extension = 'jpg';
    }

    $nombre_archivo = sanitize_file_name($nombre_pokemon) . '.' . $extension;
    $upload_dir = wp_upload_dir();
    $ruta_archivo = $upload_dir['path'] . '/' . $nombre_archivo;

    $guardado = file_put_contents($ruta_archivo, $cuerpo);
    if (!$guardado) {
        error_log('Error al guardar la imagen del Pokémon: ' . $nombre_pokemon);
        return false;
    }

    $filetype = wp_check_filetype($nombre_archivo, null);
    $attachment = [
        'post_mime_type' => $filetype['type'],
        'post_title'     => sanitize_text_field($nombre_pokemon),
        'post_content'   => '',
        'post_status'    => 'inherit'
    ];

    $attachment_id = wp_insert_attachment($attachment, $ruta_archivo, $post_id);
    if (is_wp_error($attachment_id)) {
        error_log('Error al subir la imagen a la biblioteca: ' . $nombre_pokemon);
        return false;
    }

    $metadata = wp_generate_attachment_metadata($attachment_id, $ruta_archivo);
    wp_update_attachment_metadata($attachment_id, $metadata);

    return $attachment_id;
}