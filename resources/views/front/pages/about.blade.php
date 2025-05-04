<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="/front/assets/images/fav.png">
    <title>TechConnect</title>

    <!-- fontawesome css -->
    <link rel="stylesheet" href="/front/assets/css/plugins/fontawesome-6.css">
    <!-- fontawesome css -->
    <link rel="stylesheet" href="/front/assets/css/plugins/swiper.css">
    <!-- <link rel="stylesheet" href="/front/assets/css/plugins/aos.css"> -->
    <link rel="stylesheet" href="/front/assets/css/plugins/unicons.css">
    <link rel="stylesheet" href="/front/assets/css/plugins/metismenu.css">
    <!-- <link rel="stylesheet" href="/front/assets/css/plugins/hover-revel.css"> -->
    <!-- <link rel="stylesheet" href="/front/assets/css/plugins/timepickers.min.css"> -->
    <!-- bootstrap css -->
    <link rel="stylesheet" href="/front/assets/css/vendor/bootstrap.min.css">
    <!-- main css -->
    <link rel="stylesheet" href="/front/assets/css/style.css">
</head>

<body class="handyman-h">

    <!-- header style two -->
    <header class="rts-header-area header-one">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="header--one-main">
                        {{-- <div class="row align-items-center header-top-one">
                            <div class="col-lg-3">
                                <a href="index.html" class="logo-area">
                                    <img src="/front/assets/images/logo/logo-01.png" alt="logo-area">
                                </a>
                            </div>
                            <div class="col-lg-9">
                                <div class="header-right-area">
                                    <!-- single info wrapper -->
                                    <div class="single-info-contact">
                                        <i class="fa-light fa-clock"></i>
                                        <div class="inner-content">
                                            <span>24/7 Service Alltime</span>
                                            <a href="#">
                                                <h6 class="title">Mon (to) Sun: 9:00-5:00</h6>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- single info wrapper end -->
                                    <!-- single info wrapper -->
                                    <div class="single-info-contact map">
                                        <i class="fa-light fa-location-dot"></i>
                                        <div class="inner-content">
                                            <span>Company Location</span>
                                            <a href="https://www.google.com/maps" target="_blank">
                                                <h6 class="title">16 Berlin, Germany</h6>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- single info wrapper end -->
                                    <!-- button area start -->
                                    <div class="btn-area-header">
                                        <a href="appoinment.html" class="rts-btn btn-primary with-arrow">Request Quote <i class="fa-regular fa-arrow-up-right"></i></a>
                                    </div>
                                    <!-- button area end -->
                                </div>
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="header-nav-area header--sticky">
                                    <div class="logo-md-sm-device">
                                        <a href="#" class="logo">
                                            <img src="/front/assets/images/logo/logo-01.png" alt="logo">
                                        </a>
                                    </div>

                                    <div class="header-nav main-nav-one">
                                        <nav>
                                            <ul>
                                                <li class="mega">
                                                    <a class="nav-link" href="{{ route('home-page') }}">HOME</a>
                                                    {{-- <ul class="submenu">
                                                        <div class="container flex-mega">
                                                            <li class="menu-item">
                                                                <a class="tag" href="#">Multipage</a>
                                                                <ul class="pages">
                                                                    <li><a class="current" href="index.html">Handyman</a></li>
                                                                    <li><a href="https://themewant.com/products/html/drill/plumber/">Plumber</a></li>
                                                                    <li><a href="https://themewant.com/products/html/drill/cleaning/">Cleaning</a></li>
                                                                    <li><a href="https://themewant.com/products/html/drill/air-condition/">Air Condition</a></li>
                                                                    <li><a href="https://themewant.com/products/html/drill/electric//">Electric</a></li>
                                                                </ul>
                                                            </li>
                                                            <li class="menu-item">
                                                                <a class="tag" href="#">Onepage</a>
                                                                <ul class="pages">
                                                                    <li><a href="onepage.html">Handyman Onepage</a></li>
                                                                    <li><a href="https://themewant.com/products/html/drill/plumber/">Plumber Onepage</a></li>
                                                                    <li><a href="https://themewant.com/products/html/drill/cleaning/">Cleaning Onepage</a></li>
                                                                    <li><a href="https://themewant.com/products/html/drill/air-condition/">Air Condition Onepage</a></li>
                                                                    <li><a href="https://themewant.com/products/html/drill/electric//">Electric Onepage</a></li>
                                                                </ul>
                                                            </li>
                                                        </div>
                                                    </ul> --}}
                                                </li>
                                                <li><a class="nav-link" href="{{ route('about-page') }}">ABOUT</a></li>
                                                <li class="has-dropdown">
                                                    <a class="nav-link" href="#">REGISTER</a>
                                                    <ul class="submenu">
                                                        <li><a href="{{ route('client.register') }}">Client</a></li>
                                                        <li><a href="{{ route('seller.register') }}">Selelr</a></li>
                                                    </ul>
                                                </li>
                                                <li class="has-dropdown">
                                                    <a class="nav-link" href="#">LOGIN</a>
                                                    <ul class="submenu">
                                                        <li><a href="{{ route('admin.login') }}">Admin</a></li>
                                                        <li><a href="{{ route('client.login') }}">Client</a></li>
                                                        <li><a href="{{ route('seller.login') }}">Selller</a></li>
                                                    </ul>
                                                </li>

                                                <li><a class="nav-link" href="{{ route('contact-page') }}">CONTACT</a></li>
                                            </ul>
                                        </nav>
                                    </div>
                                    <div class="actions-area">
                                        <div class="search-btn" id="search">

                                            <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M15.75 14.7188L11.5625 10.5312C12.4688 9.4375 12.9688 8.03125 12.9688 6.5C12.9688 2.9375 10.0312 0 6.46875 0C2.875 0 0 2.9375 0 6.5C0 10.0938 2.90625 13 6.46875 13C7.96875 13 9.375 12.5 10.5 11.5938L14.6875 15.7812C14.8438 15.9375 15.0312 16 15.25 16C15.4375 16 15.625 15.9375 15.75 15.7812C16.0625 15.5 16.0625 15.0312 15.75 14.7188ZM1.5 6.5C1.5 3.75 3.71875 1.5 6.5 1.5C9.25 1.5 11.5 3.75 11.5 6.5C11.5 9.28125 9.25 11.5 6.5 11.5C3.71875 11.5 1.5 9.28125 1.5 6.5Z" fill="#1F1F25" />
                                            </svg>

                                        </div>
                                        <div class="menu-btn" id="">

                                            <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect y="14" width="20" height="2" fill="#1F1F25" />
                                                <rect y="7" width="20" height="2" fill="#1F1F25" />
                                                <rect width="20" height="2" fill="#1F1F25" />
                                            </svg>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- header style two End -->

     <!-- rts breadcrumb area -->
     <div class="rts-bread-crumb-area ptb--65 bg_image bg-breadcrumb">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="con-tent-main">
                        <div class="wrapper">
                            <div class="slug">
                                <a href="#index.html">HOME /</a>
                                <a class="active" href="#index.html">ABOUT US</a>
                            </div>
                            <div class="title">
                                <a href="#">About Us</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- rts breadcrumb area end -->





 <!-- rts about area start -->
 <div class="rts-about-area rts-section-gapTop bg-h-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="about-image-left">
                    <div class="thumbnail">
                        <img src="/front/assets/images/about/01.jpg" alt="about-area">
                    </div>
                    <div class="small-image images">
                        <img src="/front/assets/images/about/02.jpg" alt="about-area">
                    </div>
                    <div class="exp-badge">
                        <h4 class="title">2</h4>
                        <span>Years Of <br>
                            Experience</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <!-- about inner content area start-->
                <div class="rts-about-inner-area-content">
                    <div class="title-area-left">
                        {{-- <p class="pre">
                            <span>TechConnect</span> Solution
                        </p> --}}
                        <h2 class="title">
                            A Company That Change <br>
                            And Solve your All Kind Of
                            <span>Solution</span>
                        </h2>
                    </div>
                    <p class="disc">
                        Welcome to TechConnect, your trusted web platform for connecting customers with skilled freelance technicians and service providers across various domains. We understand the challenges of finding reliable, professional help for your electronic, electrical, IT, vehicle, home, beauty, and personalized service needs. Our mission is to simplify this process by bringing together a community of experts and customers on a single, user-friendly platform.
                    </p>

                    <div class="title-area-left">

                        <h4 class="">
                            Our Mission
                        </h4>
                    </div>
                    <p class="disc">
                        At TechConnect, our mission is to bridge the gap between customers seeking quality repair and maintenance services and skilled technicians looking for job opportunities. We aim to provide a seamless, secure, and efficient way for customers to find the right professional for their specific needs while empowering technicians to showcase their skills and expand their reach.
                    </p>

                    <div class="title-area-left">
                        <h4 class="">
                            What We Offer
                        </h4>
                    </div>
                    <p class="disc">
                        TechConnect is designed with both customers and technicians in mind. <br> Here’s what you can expect from our platform:<br>

                        <b>Comprehensive Services:</b> <br>From electronic repairs to beauty services, our platform covers a wide range of domains, ensuring you find the right expert for any job. <br>
                        <b> User-Friendly Interface:</b><br> Our intuitive design makes it easy for customers to search for technicians and for technicians to list their services.<br>
                            <b>Secure Registration and Login:</b><br> We prioritize your security with robust authentication measures, protecting your data and ensuring a safe experience.<br>
                                <b> Detailed Technician Profiles:</b><br> View detailed profiles showcasing technicians' skills, experience, and customer reviews to make informed decisions.<br>
                                    <b> In-App Messaging:</b><br> Communicate securely and efficiently with technicians directly through our platform.<br>
                        Rating and Review System: Evaluate services based on customer ratings and reviews to ensure high-quality service delivery.
                    </p>
                </div>
                <!-- about inner content area end -->
            </div>
        </div>
    </div>
</div>
<!-- rts about area end -->





    <!-- Footer style two -->
    <!-- rts footer area one start -->
    <div class="rts-footer-one footer-bg-one ">
        <div class="container">
            {{-- <div class="row g-0 bg-cta-footer-one">
                <div class="col-lg-12">
                    <div class="bg-cta-footer-one wrapper">
                        <div class="row align-items-center">
                            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                <a href="#" class="logo-area-footer">
                                    <img src="/front/assets/images/logo/logo-02.png" alt="logo">
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                <!-- single contact area start -->
                                <div class="single-cta-area">
                                    <div class="icon">
                                        <i class="fa-solid fa-phone"></i>
                                    </div>
                                    <div class="contact-info">
                                        <p>Phone Number</p>
                                        <a href="tel:+4733378901">(+202) 2156-2145</a>
                                    </div>
                                </div>
                                <!-- single contact area end -->
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                <!-- single contact area start -->
                                <div class="single-cta-area">
                                    <div class="icon">
                                        <i class="fa-solid fa-envelope"></i>
                                    </div>
                                    <div class="contact-info">
                                        <p>Email Us Here</p>
                                        <a href="mailto:yourmail@example.com">info@diyer.com</a>
                                    </div>
                                </div>
                                <!-- single contact area end -->
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                <!-- single contact area start -->
                                <div class="single-cta-area last">
                                    <div class="icon">
                                        <i class="fa-solid fa-phone"></i>
                                    </div>
                                    <div class="contact-info">
                                        <p>Office Address</p>
                                        <a href="https://www.google.com/maps" target="_blank">251 Hilton, Berlin DE</a>
                                    </div>
                                </div>
                                <!-- single contact area end -->
                            </div>
                        </div>
                    </div>
                </div>

            </div> --}}
            <div class="row pt--90">
                <div class="col-lg-12">
                    <div class="single-footer-one-wrapper">
                        <div class="single-footer-component first">
                            <div class="title-area">
                                <h5 class="title">About Company</h5>
                            </div>
                            <div class="body">
                                <p class="disc">
                                    Tech Connect Solution is an innovative web platform bridging the gap between customers needing device repairs and skilled freelance technicians. We simplify the process of finding and hiring qualified professionals.
                                </p>
                                <div class="rts-social-style-one">
                                    <ul>
                                        <li>
                                            <a href="#">
                                                <i class="fa-brands fa-facebook-f"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="fa-brands fa-twitter"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="fa-brands fa-youtube"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="fa-brands fa-linkedin-in"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="single-footer-component">
                            <div class="title-area">
                                <h5 class="title">Useful Links</h5>
                            </div>
                            <div class="body">
                                <div class="pages-footer">
                                    <ul>
                                        <li>
                                            <a href="#">
                                                <i class="fa-solid fa-arrow-right"></i>
                                                <p>About Us</p>
                                            </a>
                                        </li>


                                        <li>
                                            <a href="#">
                                                <i class="fa-solid fa-arrow-right"></i>
                                                <p>Contact Us</p>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="single-footer-component">
                            <div class="title-area">
                                <h5 class="title">What We Do</h5>
                            </div>
                            <div class="body">
                                <div class="pages-footer">
                                    <ul>
                                        <li>
                                            <a href="#">
                                                <i class="fa-solid fa-arrow-right"></i>
                                                <p>Our Service</p>
                                            </a>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="copyright-footer-one">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="wrapper">
                        <p>Copyright 2024. All Rights Reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- rts footer area one end -->
    <!-- Footer style two End -->

    <!-- header style two -->

    <div class="search-input-area">
        <div class="container">
            <div class="search-input-inner">
                <div class="input-div">
                    <input id="searchInput1" class="search-input" type="text" placeholder="Search by keyword or #">
                    <button><i class="far fa-search"></i></button>
                </div>
            </div>
        </div>
        <div id="close" class="search-close-icon"><i class="far fa-times"></i></div>
    </div>

    <div id="anywhere-home" class="">
    </div>


    <!-- progress area start -->
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 307.919;"></path>
        </svg>
    </div>
    <!-- progress area end -->


    <!-- pre loader start -->
    <div id="elevate-load">
        <div class="loader-wrapper">
            <div class="lds-ellipsis">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
    <!-- pre loader end -->



    <!-- jquery js -->
    <script src="/front/assets/js/plugins/jquery.min.js"></script>
    <script src="/front/assets/js/vendor/jqueryui.js"></script>
    <script src="/front/assets/js/plugins/counter-up.js"></script>
    <script src="/front/assets/js/plugins/swiper.js"></script>


    <!-- <script src="/front/assets/js/vendor/twinmax.js"></script> -->
    <!-- <script src="/front/assets/js/vendor/split-text.js"></script> -->
    <!-- <script src="/front/assets/js/plugins/text-plugins.js"></script> -->
    <script src="/front/assets/js/plugins/metismenu.js"></script>


    <script src="/front/assets/js/vendor/waypoint.js"></script>
    <script src="/front/assets/js/vendor/waw.js"></script>


    <script src="/front/assets/js/plugins/gsap.min.js"></script>
    <script src="/front/assets/js/plugins/scrolltigger.js"></script>
    <!-- <script src="/front/assets/js/plugins/aos.js"></script> -->
    <!-- <script src="/front/assets/js/plugins/jquery-ui.js"></script> -->
    <script src="/front/assets/js/plugins/jquery-timepicker.js"></script>
    <!-- <script src="/front/assets/js/vendor/sal.min.js"></script> -->

    <script src="/front/assets/js/plugins/bootstrap.min.js"></script>
    <!-- <script src="/front/assets/js/plugins/jquery-slideNav.js"></script> -->
    <!-- <script src="/front/assets/js/plugins/hover-revel.js"></script> -->
    <!-- <script src="/front/assets/js/plugins/contact-form.js"></script> -->

    <script src="/front/assets/js/main.js"></script>

    <!-- <script src="/front/assets/js/plugins/swip-img.js"></script> -->
    <!-- header style two End -->
</body>

</html>
