(function ($) {
    "use strict";
	
	/* Theme switcher */
	$("#switcher-toggle").on("click", function() {
        if ($(this).hasClass("switcher-open")) {
            $("#theme-switcher").animate({
                left: "0px"
            }, 500);
            $(this).removeClass("switcher-open").addClass("switcher-close")
        } else {
            $("#theme-switcher").animate({
                left: "-208px"
            }, 500);
            $(this).removeClass("switcher-close").addClass("switcher-open")
        }
    });
	
    $("button[data-theme]").click(function() {
        $("#color-switcher").attr("href", $(this).data("theme"))
    });
	
	$('body').append('<div class="btn-buy-demo"><a href="https://themeforest.net/item/ccico-bitcoin-and-cryptocurrency-html-landing-page/21774949?ref=awaiken"><img src="html-preview/cc-ico/images/buy-now.png" /></a> <a href="https://demo.awaikenthemes.com/html-preview/cc-ico/#landing-page-demo"><img src="html-preview/cc-ico/images/see-more-demo.png" /></a></div><style>.btn-buy-demo{	position: fixed;   bottom: 10px;   right: 0px; text-align: right; z-index:999; } .btn-buy-demo img {	margin-right:10px; }</style>');
	
})(jQuery);