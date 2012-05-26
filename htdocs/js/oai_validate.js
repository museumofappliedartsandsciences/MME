$( function () {
  $('input[name=file]').bind('change', function () {
    $('input[name=url]').val('');
    $('#url').fadeOut();
  });

});
