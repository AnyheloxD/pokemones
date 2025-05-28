jQuery(document).ready(function ($) {
  $('#tipo').on('change', function () {
    var tipos = $(this).val() || [];

    $.ajax({
      url: pokemonAjax.ajax_url,
      type: 'POST',
      data: {
        action: 'pokemon_obtener_fortalezas',
        nonce: pokemonAjax.nonce,
        tipo: tipos,
      },
      success: function (response) {
        if (response.success) {
          $('#fortaleza').html(response.data);
        } else {
          $('#fortaleza').html(
            '<option value="">Error al cargar fortalezas</option>'
          );
        }
      },
      error: function () {
        $('#fortaleza').html(
          '<option value="">Error al cargar fortalezas</option>'
        );
      },
    });
  });

  $('#pokemon-buscador-form').on('submit', function (e) {
    e.preventDefault();

    var buscar = $('input[name="buscar"]').val();
    var tipo = $('select[name="tipo[]"]').val() || [];
    var fortaleza = $('select[name="fortaleza[]"]').val() || [];

    $.ajax({
      url: pokemonAjax.ajax_url,
      type: 'POST',
      data: {
        action: 'pokemon_buscar_pokemones',
        nonce: pokemonAjax.nonce,
        buscar: buscar,
        tipo: tipo,
        fortaleza: fortaleza,
      },
      success: function (response) {
        if (response.success) {
          $('#resultado').html(response.data);
        } else {
          $('#resultado').html('<p>Error: ' + response.data + '</p>');
        }
      },
      error: function () {
        $('#resultado').html(
          '<p>Error al procesar la solicitud. Por favor, intenta de nuevo.</p>'
        );
      },
    });
  });
});
