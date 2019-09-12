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
	
	$(window).scroll(function() {
	
	if ($(this).scrollTop() > 10){
		$('.navbar-nav').addClass("sticky-navbar-nav");
		$('.topLogoWhite').addClass("sticky-topLogoWhite");
		$('.navbar-catalog-button').addClass("sticky-navbar-catalog-button");
		$('.topTelDub').addClass("sticky-topTelDub");
		$('.topInfo').addClass("sticky-topInfo");
		$('.blockTopIco').addClass("sticky-blockTopIco");
	}
	else{
		$('.topLogoWhite').removeClass("sticky-topLogoWhite");
		$('.navbar-catalog-button').removeClass("sticky-navbar-catalog-button");
		$('.topTelDub').removeClass("sticky-topTelDub");
		$('.topInfo').removeClass("sticky-topInfo");
		$('.navbar-nav').removeClass("sticky-navbar-nav");
		$('.blockTopIco').removeClass("sticky-blockTopIco");
	}
	});
	
	
});