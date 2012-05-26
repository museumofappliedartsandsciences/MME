$( function () {

  $('#dataset thead td.check input[type=checkbox]').click ( function() {
    $(this).blur();
    $(this).parents('form:first').find('input[type=checkbox]').attr('checked', $(this).attr('checked'));
  });

  $('#selector select').change ( function() {
    if ( '' != $(this).val() ) {
      $(this).parents('form:first').submit();
    }
  });

});
