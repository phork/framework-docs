$(function() {
	
	//remove outline from clicked links and set up external links open a new window
	$('a[rel=external]').attr('target','_blank');
	$('a').click(function() { this.blur(); });
	
	//add the javascript stylesheet
	$('<link>').appendTo('head').attr({
		rel: 'stylesheet',
		type: 'text/css',
		href: '/css/themes/default/common/javascript.css'
	});
	
	//set up the footer table of contents as an overlay
	var $toc = $('#footer-toc');
	if ($toc.size()) {
		if ($(window).height() > $toc.outerHeight()) {
			$('body')
				.removeClass('has-toc')
				.bind('click', function() {
					$toc.trigger('hide');
				})
			;
			
			$toc
				.addClass('subnav')
				.bind('show', function(e) {
					var $this = $(this);
					$this
						.css({
							position: 'fixed',
							top: 0,
							left: Math.round(($(window).width() - $this.outerWidth()) / 2)
						})
						.show('blind', 200)
					;
				})
				.bind('hide', function(e) {
					var $this = $(this);
					if ($this.is(':visible')) {
						$this.hide('blind', 200);
					}
				})
				.bind('click', function(e) {
					e.stopPropagation();
				})
			;
			
			$('#nav-contents')
				.addClass('arrow bottom')
				.bind('click', function(e) {
					e.preventDefault();
					e.stopPropagation();
					$toc.trigger($toc.is(':visible') ? 'hide' : 'show');
				})
			;
			
			$(document).bind('keydown', function(e) {
				if (e.keyCode == 27) {
					$toc.trigger('hide');
				}
			});
		} else {
			$toc.show();
		}
	}
});