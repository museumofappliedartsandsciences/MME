$( function () {

  $('.confirm').bind('click', function () {
    return confirm('Really do this? There is no Undo');
  });

  if ( $('#contact-form').length > 0 ) {
    if ( $('#contact-form :input.invalid').length > 0 ) {
      $('#contact-form .invalid:first').focus();
    } else {
      $('#contact-form input[type=text]:first').focus();
    }
  } else {
    $('#search input[type=text]').focus();
  }
  
  var q = $('#search input[type=text]').val();
  if ( q != '' ) {
    $('.simple').highlight(q);
    $('.collection').highlight(q);
    $('#dataset').highlight(q);
  }

  $('#sidebar ul').hide();
  $('#sidebar ul:first').show();

  $('#sidebar h3 a').bind('click', function(e) {
    e.preventDefault();
    $('#sidebar ul:visible').slideUp();
    $(e.target).parent().next('ul').slideDown();
  });

});
