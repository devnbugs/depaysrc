<?php
header("Content-Type:text/css");
$color = "#f0f"; // Change your Color Here
$secondColor = "#ff8"; // Change your Color Here

function checkhexcolor($color){
    return preg_match('/^#[a-f0-9]{6}$/i', $color);
}

if (isset($_GET['color']) AND $_GET['color'] != '') {
    $color = "#" . $_GET['color'];
}

if (!$color OR !checkhexcolor($color)) {
    $color = "#336699";
}


function checkhexcolor2($secondColor){
    return preg_match('/^#[a-f0-9]{6}$/i', $secondColor);
}

if (isset($_GET['secondColor']) AND $_GET['secondColor'] != '') {
    $secondColor = "#" . $_GET['secondColor'];
}

if (!$secondColor OR !checkhexcolor2($secondColor)) {
    $secondColor = "#336699";
}
?>



.text-primary, .custom--nav-tabs .nav-item .nav-link, .custom--accordion-two .accordion-button:not(.collapsed), .custom--field i, .overview-card__icon, .inline-menu li a:hover, .contact-info p a:hover, .info-card__iconm, .package-card__features li::before, .ratings i, .scroll-to-top .scroll-icon, .page-breadcrumb li:first-child::before, .contact-info i, .dashboard-card .number, .dashboard-card .icon, .header .main-menu li .sub-menu li a:hover, .header .main-menu li a:hover, .header .main-menu li a:focus, .header .main-menu li.menu_has_children > a::before{
    color: <?php echo $color; ?> !important;
}

.btn-primary, .hover,.sidebar,.navbar-wrapper, .scroll-to-top, .preloader .preloader-container .animated-preloader, .footerd, .header-navbar, .btn-primary, .btn, .faqs, .header{
    background-color: <?php echo $secondColor; ?>;
}

.package-card, .custom--nav-tabs .nav-item .nav-link, .custom--nav-tabs .nav-item .nav-link.active, .custom--accordion .accordion-item, .testimonial-item, .contact-info-card:hover, .form--control, .modal .modal-content, .custom--card, .testimonial-item, .feature-card:hover, .form--control:focus{
    border-color: <?php echo $color; ?> !important; 
}

.package-card__title, .how-work-card__step::before, .custom--nav-tabs .nav-item .nav-link.active, .custom--table thead, .inline-social-links li a:hover, .button-nav-tabs .nav-item .nav-link.active, .custom--accordion .accordion-button:not(.collapsed), .preloader .preloader-container .animated-preloader, .balance-card, .bg--base{
    background-color: <?php echo $color; ?> !important;
}

.input-group-text{
    border: none;
}

.subscribe-wrapper{
    box-shadow: 0 0 10px <?php echo $color; ?>80 !important;
} 

.feature-card{
    box-shadow: inset 0 0 15px <?php echo $color; ?>d9 !important;
}

.btn--base:hover{
    background-color: <?php echo $color; ?> !important; 
}
.cookie__wrapper {
    border-top: 2px solid <?php echo $color; ?>;
    box-shadow: 0 0 30px 2px <?php echo $color; ?>4d;
}