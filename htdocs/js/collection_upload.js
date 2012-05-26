$( function () {

  $('#dataset td.check input[type=checkbox]').click ( function() {
    $(this).blur();
    $(this).parents('form:first').find('input[type=checkbox]').attr('checked', $(this).attr('checked'));
  });

});
