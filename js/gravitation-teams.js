jQuery(document).ready(function($) {
	
	if($('div').hasClass('owl-carousel')){
		$('#teams-carousel').owlCarousel({
		    loop:false,
		    dots: true,
		    nav: false,
		    margin:10,
		    lazyLoad: true,
		    responsiveClass:true,
		    responsive:{
		        0:{
		            items:1,
		            nav:true
		        },
		        600:{
		            items:3,
		            
		        },
		        1000:{
		            items:3,
		        }
		    },
		    responsiveRefreshRate:100,
    
		});
	}
	  
});