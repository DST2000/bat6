jQuery(function($){
	
	/* my */
	$('.dropdown').on('shown.bs.dropdown', function(e) {
		var menu = e.target.parentNode.querySelector('.dropdown-menu');
		if(menu) {
		menu.style.maxHeight =  'calc(100vh - ' + menu.getBoundingClientRect().top + 'px)';
		}
	});
	$('#filter-mobile').click(function() {
		$('#filter-mobile-content').toggle();
	});
	
	
});