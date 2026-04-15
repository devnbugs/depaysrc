(function ($) {
    "use strict";
	
	var $window = $(window); 
	var $body = $('body'); 
	
	/* Preloader Effect */
	$window.on( "load", function() {
	   $(".preloader").fadeOut(600);
    });
	
	/* Sticky header */
	$window.scroll(function(){
    	if ($window.scrollTop() > 200) {
			$('.navbar').addClass('sticky-header');
		} else {
			$('.navbar').removeClass('sticky-header');
		}
	});
	
	
	/* slick nav */
	$('#main-menu').slicknav({prependTo:'#responsive-menu',label:'', closeOnClick:true});
		
	/* Top Menu */
	$(document).on('click','.navbar-nav li a, #responsive-menu ul li a',function(){
		var id = $(this).attr('href');
		var h = parseFloat($(id).offset().top);
		$('body,html').stop().animate({
			scrollTop: h - 70
		}, 800);
		return false;
	});
	
	/* Add active class to tab panel */
	var $card = $(".card"); 
	$card.on("show.bs.collapse hide.bs.collapse", function(e) {
		if (e.type=='show'){
		  $(this).addClass('active');
		}else{
		  $(this).removeClass('active');
		}
	 });  
	
	/* Testimonial slider */
	var swiper = new Swiper('.testimonial-slider', {
		autoplay: {delay: 5000},
		pagination: {
			el: '.testimonial-pagination',
			clickable: true
		},
    });

	
	/* Init Counter */
    $('.counter').counterUp({ delay: 4, time: 1000 });
	
	/* Popup video */
	$('.popup-video').magnificPopup({
        type: 'iframe',
        preloader: true,
    });
	
	
	/* Contact form validation */
	var $contactform=$("#contactForm");
	$contactform.validator({focus: false}).on("submit", function (event) {
		if (!event.isDefaultPrevented()) {
			event.preventDefault();
			submitForm();
		}
	});

	function submitForm(){
		/* Initiate Variables With Form Content*/
		var name = $("#name").val();
		var email = $("#email").val();
		var subject = $("#subject").val();
		var message = $("#message").val();

		$.ajax({
			type: "POST",
			url: "form-process.php",
			data: "name=" + name + "&email=" + email + "&subject=" + subject + "&message=" + message,
			success : function(text){
				if (text == "success"){
					formSuccess();
				} else {
					submitMSG(false,text);
				}
			}
		});
	}

	function formSuccess(){
		$contactform[0].reset();
		submitMSG(true, "Message Sent Successfully!")
	}

	function submitMSG(valid, msg){
		if(valid){
			var msgClasses = "h3 text-center text-success";
		} else {
			var msgClasses = "h3 text-center text-danger";
		}
		$("#msgSubmit").removeClass().addClass(msgClasses).text(msg);
	}
	/* Contact form validation end */
	
	/* Animate with wow js */
    new WOW({mobile:false}).init(); 
	
})(jQuery);