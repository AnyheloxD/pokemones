jQuery(document).ready(function ($) {
  $('.btn_importar_poke').on('click', function (e) {
    e.preventDefault();
    console.log('click');
    Swal.fire({
      title: '¿Estás seguro?',
      text: '¿Deseas importar los primeros 20 Pokémon desde la PokéAPI?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, importar',
      cancelButtonText: 'Cancelar',
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = pokemonAdmin.import_url;
      }
    });
  });
});
