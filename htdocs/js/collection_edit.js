$( function () {

  var collection_id = $('input[name=collection_id]').val();

  $('#editform').bind('submit', function () {
    var s = '';
    $('#subjects li').each ( function () {
      s += $(this).find('select[name=type]').val() + '|' + $(this).find('input[name=value]').val() + "\n";
    });
    $('input[name=collectionSubjects]').val(s);

    var s = '';
    $('#related li').each ( function () {
      s += $(this).find('select[name=type]').val() + '|' + $(this).find('input[name=key]').val() + '|' + $(this).find('input[name=value]').val() + "\n";
    });
    $('input[name=collectionRelated]').val(s);

    var s = '';
    $('#temporal li').each ( function () {
      s += $(this).find('select[name=type]').val() + '|' + $(this).find('input[name=value]').val() + "\n";
    });
    $('input[name=collectionCoverageTemporal]').val(s);

    var s = '';
    $('#spatial li').each ( function () {
      s += $(this).find('select[name=type]').val() + '|' + $(this).find('input[name=value]').val() + "\n";
    });
    $('input[name=collectionCoverageSpatial]').val(s);

    return true;
  });


  var opts = { 'subjects': false, 'related': false, 'spatial': false, 'temporal': false };

  var addOption = function( field, val ) {

    if ( field == undefined ) {
      return false;
    }

    if ( val == undefined ) {
      var val = { 'type': false, 'value':'', 'key': '' };
    }

    var options = opts[field];

    var el = $('#' + field );
    
    var li = $('<li />').appendTo(el);
    var sel = $('<select name="type" />').appendTo(li);
    $('<option value="">Select...</option>').appendTo(sel);
    
    for ( var i in options ) {
      $('<option value="' + options[i] + '">' + options[i] + '</option>').appendTo(sel).attr('selected', ( ( options[i] == val.type ) ? 'selected': '' ) );
    }
    
    $('<option value="_new">New Type...</option>').appendTo(sel);
    
    $('<input type="text" class="new" name="type_new" value=""/>').appendTo(li).hide();

    if ( field == 'related' ) {
      $('<input type="text" name="key" value="' + ( ( val.key != '' ) ? val.key : 'http://' ) + '" size="48" maxlength="127" />').appendTo(li);
    }

    var term = $('<input type="text" name="value" value="' + val.value + '" size="48" maxlength="127" />').appendTo(li);
    
    $('<input type="button" value="&times;" />').appendTo(li).bind('click', function () {
      $(this).parent().fadeOut( 250, function () {
	$(this).remove();
      });
    });

    if ( field == 'subjects' ) {
      $('<a href="#" class="picker">')
        .html('Find Term')
        .appendTo(li)
        .bind('click', function (e) {
          e.preventDefault();
          var t = new BlockTerms ( $(term).val(), function(r) {
            if (r) { 
              $(term).val(r);
            }
          });

        });
    }
  }
  
  $('.options select[name=type]').live('change', function() {
    if ( $(this).val() =='_new' ) {
      $(this).parent().find('input[name=type_new]').show().select();
    } else {
      $(this).parent().find('input[name=type_new]').hide();
    }
  });

  $('.options input[name=type_new]').live('blur', function() {
    $(this).hide();
    if ( $(this).val() == '' ) {
      $(this).parent().find('select').find('option:first').attr('selected','selected');
    } else {
      var val = $(this).val();
      var op = $(this).parent().find('select option[value=' + val + ']');
      if ( op.length > 0 ) {
	op.attr('selected','selected');
      } else {
	var opt = $(this).parents('ul').attr('id');
	opts[opt][val] = val;
	$(this).parents('ul').find('select[name=type]').each( function() { $(this).append('<option value="' + val + '">' + val + '</option>') });
	$(this).parent().find('select option[value=' + val + ']').attr('selected','selected');
      }
    }
  });

  $('.add').bind('click', function () {
    var field = $(this).prev().attr('id');
    $(this).blur();
    addOption( field );
  });	


  var collection_id = $('input[name=collection_id]').val();
  if ( collection_id == '' ) {
    collection_id = '_new';
  }

  $.getJSON('/collection_edit/' + collection_id + '.json', false, function(data) {

    opts.subjects = data.subjects;
    opts.related = data.related;
    opts.temporal = data.temporal;
    opts.spatial = data.spatial;
    
    if ( data.collection.subjects ) {
      for ( var i in data.collection.subjects ) {
	for ( var j in data.collection.subjects[i] ) {
	  addOption( 'subjects', { 'type': i, 'value': data.collection.subjects[i][j] } );
	}
      }
    }

    if ( data.collection.related ) {
      for ( var i in data.collection.related ) {
	addOption( 'related', { 'type': data.collection.related[i].type, 'key': data.collection.related[i].key, 'value': data.collection.related[i].value } );
      }
    }


    if ( data.collection.coverage.spatial ) {
      for ( var i in data.collection.coverage.spatial ) {
	for ( var j in data.collection.coverage.spatial[i] ) {
	  addOption( 'spatial', { 'type': i, 'value': data.collection.coverage.spatial[i][j] } );
	}
      }
    }
    if ( data.collection.coverage.temporal ) {
      for ( var i in data.collection.coverage.temporal ) {
	for ( var j in data.collection.coverage.temporal[i] ) {
	  addOption( 'temporal', { 'type': i, 'value': data.collection.coverage.temporal[i][j] } );
	}
      }
    }

  });

});
