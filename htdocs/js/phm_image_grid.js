jQuery(function ($) {
	$('ul#phm_image_grid li').hover(function (){
		/* don't display caption with small thumbnails */
		var img_width = $('img', this).attr('width');

		if (img_width < 80){
			$(this).children('div#caption').css('display', 'none');
		}else{
			$(this).children('div#caption').stop().fadeTo(500, 0.7);
		}
	},function(){
		$(this).children('div#caption').stop().fadeTo(800, 0);
	});

	$('ul#phm_image_grid li').click(function (){
		remove_popup();
		create_popup($(this));
		return false;
	});

	function remove_popup(){
		$('div#phm_popup').stop().fadeTo(200, 0, function(){
			$(this).remove();
		});

	}

	function create_popup(parent){
		$('body').append('<div id="phm_popup"></div>');
		var popup          = $('div#phm_popup');
		var parent_postion = parent.offset();

		//Content
		var content = parent.clone();
		//clean styles from the grid
		$('div#caption, img, a', content).removeAttr('style');

		//Fullsize thumbnail
		$('img', content).attr('width',  '160');
		$('img', content).attr('height', '160');

		//add content to popup
		$(content.html()).appendTo('div#phm_popup');

		//Create link more and close
		var href   = $('a', popup).attr('href');
		var target =  $('a', popup).attr('target');
		var links = '<div id="links">';
		links += '<a href="' + href + '" target="' + target + '">More</a>';
		links += '<a id="btn_close_popup" href="#close">Close</a>';
		links += '</div>';

		popup.append(links);

		$('div#phm_popup a#btn_close_popup').click(remove_popup);

		//Position popup
		var top  = parent_postion.top;
		var left = parent_postion.left;

		//top  += ((popup.height() *.5) - ( parent.height() * .5));
		top  =  (top +  (parent.height() *.5)) - ( popup.height() * .5);
		left =  (left +  (parent.width() *.5)) - ( popup.width() * .5);

		popup.css('top',  top);
		popup.css('left', left);
		popup.mouseleave(remove_popup);

		var scroll_top    = $(window).scrollTop();
		var scroll_bottom = $(window).height();

		if(scroll_top > top){
			$('html,body').animate({scrollTop: top - 10}, 200);
		}

		popup.hide();
		//Animation fade in
		popup.stop().fadeTo(300, 1);
	}


	//$('ul#phm_image_grid li').colorbox({href:"thankyou.html"});

});