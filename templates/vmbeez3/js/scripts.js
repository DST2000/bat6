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
	
	// introSlider slider
	$(".introSlider .owl-carousel").owlCarousel({
				loop: true,
				margin:0,
				nav: true,
				center: true,
				autoplay: true,
				navText: [
					'<i class="fa fa-angle-left"></i>',
					'<i class="fa fa-angle-right"></i>'
				],
				responsive: {
					0: {
						items: 1
					},
					4000: {
						items: 1
					},
					8000: {
						items: 1
					},
					12000: {
						items: 1
					}
				}
			});	

	// introSlider slider
	$(".bannerSlider .owl-carousel").owlCarousel({
				loop: true,
				margin: 0,
				nav: true,
				center: true,
				autoplay: true,
				navText: [
					'<i class="fa fa-angle-left"></i>',
					'<i class="fa fa-angle-right"></i>'
				],
				responsive: {
					0: {
						items: 1
					},
					600: {
						items: 1
					},
					750: {
						items: 1
					},
					1000: {
						items: 1
					}
				}
			});		

	// newProd slider
	$("#newProd .owl-carousel").owlCarousel({
				loop: true,
				margin: 20,
				nav: true,
				center: true,
				autoplay: true,
				navText: [
					'<i class="fa fa-angle-left"></i>',
					'<i class="fa fa-angle-right"></i>'
				],
				responsive: {
					0: {
						items: 1
					},
					600: {
						items: 2
					},
					750: {
						items: 2
					},
					1000: {
						items: 3
					},
					1100: {
						items: 5
					}
				}
			});

	// toprod slider
	$("#logoBlock .owl-carousel").owlCarousel({
				loop: true,
				margin: 0,
				nav: true,
				center: true,
				autoplay: true,
				navText: [
					'<i class="fa fa-angle-left"></i>',
					'<i class="fa fa-angle-right"></i>'
				],
				responsive: {
					0: {
						items: 1
					},
					600: {
						items: 2
					},
					750: {
						items: 3
					},
					1000: {
						items: 7
					}
				}
			});	
	$("button.filtermoduledisplay").click(
		function() { 
			$("#filter-module").toggleClass(" hidden-xs hidden-sm");
		}
	);
//	$(document).ready(function() {
//		var textcart = $("input.total_products_from_cart").html();
//		if (textcart.length == 0 ) {
//		textcart = "111";
//		}
//		$("span.total_products_from_cart").html(textcart);
//		$("span.cart-order-quantity").html(textcart);
//	}
//	);
	
});