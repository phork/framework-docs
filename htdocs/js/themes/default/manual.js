$(function() {
	$(document).bind('keydown', function(e) {
		switch (e.keyCode) {
			case 37:
				var $link = $('#subnav a.prev');
				if ($link.size()) {
					self.location.href = $link.attr('href');
				}
				break;
			
			case 39:
				var $link = $('#subnav a.next');
				if ($link.size()) {
					self.location.href = $link.attr('href');
				}
				break;
		}
	});
});