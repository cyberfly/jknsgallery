(function($){
	var $et_content_area = $('#content-area'),
		et_content_area_height = $et_content_area.innerHeight(),
		$et_sidebar = $('#sidebar'),
		$et_footer = $('#main_footer');

	$(document).ready(function(){
		var $et_image_entry = $('.entry.image'),
			$et_audio_play = $('a.jp-play'),
			$et_audio_pause = $('a.jp-pause'),
			image_entry_element = 'span.zoom',
			main_speed = 700;

		$et_image_entry.find('.photo').hover(function(){
			$(this).find(image_entry_element).css({ opacity : 0, 'display' : 'block' }).stop(true,true).animate( { opacity: 1 }, main_speed );
		}, function(){
			$(this).find(image_entry_element).stop(true,true).animate( { opacity: 0 }, main_speed );
		});

		$et_audio_play.click( function(){
			var $jp_progress = $(this).parents('.jp-controls').siblings('.jp-progress');

			if ( ! $jp_progress.is(':visible') ){
				$(this).parents('.jp-audio').siblings('.audio_info').find('p.meta').animate( { opacity : 0 }, 700 );
				$jp_progress.css( { 'opacity' : 0, 'display' : 'block' } ).animate( { opacity : 1 }, 700 );
			}

			$(this).parents('.entry.audio').find('> img').addClass('et_playing');
		} );

		$et_audio_pause.click( function(){
			$(this).parents('.entry.audio').find('> img').removeClass('et_playing');
		} );
	});

	$(window).load(function(){
		$('#et_posts').masonry({
			itemSelector : '.entry',
			isAnimated : true
		});
		$('#footer-widgets').masonry({
			itemSelector : '.footer-widget',
			isAnimated : true
		});

		et_calculate_blocks();
	});

	function et_calculate_blocks(){
		var $et_content_area_right = $('#content_right'),
			$et_left_area = $('#left-area'),
			et_sidebar_top = parseInt( $et_sidebar.css('padding-top') ),
			et_content_height = $et_content_area_right.innerHeight(),
			et_left_area_height = $et_left_area.innerHeight();

		if ( et_left_area_height > et_content_height ) $et_footer.css( 'height', $et_footer.height() + et_left_area_height - et_content_height );
		else $et_sidebar.css( 'height', $et_content_area.height() - $et_left_area.find('> header').height() - et_sidebar_top );
	}

	$(window).bind( 'smartresize.masonry', function() {
		setTimeout(function() {
			$et_sidebar.css( 'height', 'auto' );
			$et_footer.css( 'height', 'auto' );
			et_calculate_blocks();
		}, 1000);
	});

	var $comment_form = $('form#commentform'),
		$comment_form_textarea = $('p.comment-form-comment');

	$comment_form.find('input, textarea').each(function(index,domEle){
		var $et_current_input = $(domEle),
			$et_comment_label = $et_current_input.siblings('label'),
			et_comment_label_value = $et_current_input.siblings('label').text();
		if ( $et_comment_label.length ) {
			$et_comment_label.hide();
			if ( $et_current_input.siblings('span.required') ) {
				et_comment_label_value += $et_current_input.siblings('span.required').text();
				$et_current_input.siblings('span.required').hide();
			}
			$et_current_input.val(et_comment_label_value);
		}
	}).live('focus',function(){
		var et_label_text = $(this).siblings('label').text();
		if ( $(this).siblings('span.required').length ) et_label_text += $(this).siblings('span.required').text();
		if ($(this).val() === et_label_text) $(this).val("");
	}).live('blur',function(){
		var et_label_text = $(this).siblings('label').text();
		if ( $(this).siblings('span.required').length ) et_label_text += $(this).siblings('span.required').text();
		if ($(this).val() === "") $(this).val( et_label_text );
	});

	// remove placeholder text before form submission
	$comment_form.submit(function(){
		$comment_form.find('input:text, textarea').each(function(index,domEle){
			var $et_current_input = jQuery(domEle),
				$et_comment_label = $et_current_input.siblings('label'),
				et_comment_label_value = $et_current_input.siblings('label').text();

			if ( $et_comment_label.length && $et_comment_label.is(':hidden') ) {
				if ( $et_comment_label.text() == $et_current_input.val() )
					$et_current_input.val( '' );
			}
		});
	});

	$comment_form_textarea.insertBefore('p.comment-form-author');
})(jQuery)