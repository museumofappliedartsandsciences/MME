jQuery(function ($) {

	var win = window.dialogArguments || opener || parent || top;

	var prefix_fields = 'phm-image-grid-';
	var field_name  = prefix_fields + 'field';
	var filter_name = prefix_fields + 'filter';
	var value_name  = prefix_fields + 'value';
	var num_filters = $('[name="phm-image-grid-filters_count"]').attr('value');

	$('input#phm_image_grid_send_to_editor').click(function () {
    	var argument 	= '';
    	var parameters 	= '';

    	$("div#options input[type='text']").each(function(index) {
    		var option = $(this).attr('name');
    		var value  = $(this).attr('value');

    		argument += create_argument(option, value);
    	});

    	$("div#options input[type='checkbox']").each(function(index) {
    		var option = $(this).attr('name');
    		var value  = $(this).is(':checked');

    		argument += create_argument(option, value);
    	});

    	//Filters
    	$("div#filters p.filter_search").each(function(index) {

    		var field  = $('[name="' + field_name + '-' + index + '"]', this).val();
    		var filter = $('[name="' + filter_name + '-' + index + '"]', this).val();
    		var value  = $('[name="' + value_name + '-' + index + '"]', this).val();

    		if(field && filter && value){
    			field = (filter != 'containts')?field + '_' + filter:field;
    			parameters += field + ':' + value + '|';
    		}
    	});

		argument += create_argument('parameters', '"' + parameters.substring(0, parameters.length - 1) + '"');

    	win.send_to_editor('[phm-grid' +  argument  +']');
    	return false;
    });


    function create_argument(option, value, separator){
    	option = option.replace(prefix_fields, '');
    	return ' ' + option + '=' + value;
    }

    $('a#remove_filter').live('click', remove_filter);

    $('a#add_filter').live('click', function() {
    	create_search_field(num_filters);
    	update_filter_count();
    	return false;
    });

    function create_search_field(id){
    	var filter = $('p.filter_search:first');
    	filter = $($('<div></div>').html(filter.clone()));

    	//Change index on the name of the field
    	filter = clean_values(filter, id);


		//Append new filter fields
    	filter = $('div#filters').append(filter.html());

    }


    function clean_values(context, id){
    	var field_select  = $('[name="' + field_name + '-0"]', context);
		var filter_select = $('[name="' + filter_name + '-0"]', context);
		var value  = $('[name="' + value_name + '-0"]', context);

		if (id != 0){
			field_select.attr('name', field_name  + '-' + id);
			filter_select.attr('name', filter_name + '-' + id);
			value.attr('name',  value_name  + '-' + id);
		}

		$('[selected]', field_select).removeAttr('selected');
		$('[selected]', filter_select).removeAttr('selected');
		value.removeAttr('value');
		$('a#remove_filter', context).attr('name', id);

		return context;
    }


    function remove_filter(){
    	var parent = $(this).parent();
    	if ($(this).attr('name') == '0'){
    		clean_values(parent, 0);
    		return false;
    	}
    	parent.remove();

    	return false;
    }

    function update_filter_count(){
      	num_filters ++;
    	$('[name="phm-image-grid-filters_count"]').attr('value', num_filters);
    }



});