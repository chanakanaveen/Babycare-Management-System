<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>BabyBloom Care</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="" name="keywords">
        <meta content="" name="description">

        <!-- Google Web Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@600;700&family=Montserrat:wght@200;400;600&display=swap" rel="stylesheet">

        <!-- Icon Font Stylesheet -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Libraries Stylesheet -->
        <link href="front/lib/animate/animate.min.css" rel="stylesheet">
        <link href="front/lib/lightbox/css/lightbox.min.css" rel="stylesheet">
        <link href="front/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

        <!-- Customized Bootstrap Stylesheet -->
        <link href="front/css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="front/css/style.css" rel="stylesheet">



    </head>

    <body>

        <!-- Spinner Start -->
        <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
            <div class="spinner-grow text-primary" role="status"></div>
        </div>
        <!-- Spinner End -->


        <!-- Navbar start -->
        <div class="container-fluid border-bottom  wow fadeIn" data-wow-delay="0.1s">
            <div class="container px-0">
                <nav class="navbar navbar-light navbar-expand-xl py-3">
                    {{-- add logo --}}
                    <img src="front/img/newlogo.jpeg" alt="logo" class="img-fluid logo" style="max-height: 130px;">
                    {{-- <a href="index.html" class="navbar-brand"><h1 class="text-primary display-6">Baby<span class="text-secondary">Care</span></h1></a> --}}
                    {{-- <a href="index.html" class="navbar-brand"><h1 class="text-primary display-6">Baby<span class="text-secondary">Care</span></h1></a> --}}
                    <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                        <span class="fa fa-bars text-primary"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarCollapse">
                        <div class="navbar-nav mx-auto">
                            <a href="index.html" class="nav-item nav-link active">Home</a>
                            <a href="#about" class="nav-item nav-link">About</a>
                            <a href="#contact" class="nav-item nav-link">Contact</a>
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Register</a>
                                <div class="dropdown-menu m-0 bg-secondary rounded-0">
                                    <a href="{{ route('midwife.register') }}" class="dropdown-item">Midwife</a>
                                    <a href="{{ route('parent.register') }}" class="dropdown-item">Parent</a>
                                </div>
                            </div>
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Login</a>
                                <div class="dropdown-menu m-0 bg-secondary rounded-0">
                                    <a href="{{ route('moh.login') }}" class="dropdown-item">MOH</a>
                                    <a href="{{ route('midwife.login') }}" class="dropdown-item">Midwife</a>
                                    <a href="{{ route('parent.login') }}" class="dropdown-item">Parent</a>
                                </div>
                            </div>

                        </div>
                        <div class="d-flex me-4">
                            <div id="phone-tada" class="d-flex align-items-center justify-content-center">
                                <a href="" class="position-relative wow tada" data-wow-delay=".9s" >
                                    <i class="fa fa-phone-alt text-primary fa-2x me-4"></i>
                                    <div class="position-absolute" style="top: -7px; left: 20px;">
                                        <span><i class="fa fa-comment-dots text-secondary"></i></span>
                                    </div>
                                </a>
                            </div>

                        </div>
                        <button class="btn-search btn btn-primary btn-md-square rounded-circle" data-bs-toggle="modal" data-bs-target="#searchModal"><i class="fas fa-search text-white"></i></button>
                    </div>
                </nav>
            </div>
        </div>
        <!-- Navbar End -->


        <!-- Modal Search Start -->
        <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Search by keyword</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex align-items-center">
                        <div class="input-group w-75 mx-auto d-flex">
                            <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">
                            <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Search End -->


        <!-- Hero Start -->
        <div class="container-fluid py-5 hero-header wow fadeIn" data-wow-delay="0.1s">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-lg-7 col-md-12">
                        <h1 class="mb-3 text-primary">Connecting Healthcare For Every Child</h1>
                        <h1 class="mb-5 display-1 text-white">Sri Lanka's Digital Health Record System For Babies </h1>
                        <a href="" class="btn btn-primary px-4 py-3 px-md-5  me-4 btn-border-radius">Register Now</a>
                        <a href="" class="btn btn-primary px-4 py-3 px-md-5 btn-border-radius">How It Works</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero End -->


        <!-- About Start -->
        <div class="container-fluid py-5 about bg-light about" id="about">
            <div class="container py-5">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-5 wow fadeIn" data-wow-delay="0.1s">
                        <div class="video border">
                            <button type="button" class="btn btn-play" data-bs-toggle="modal" data-src="https://www.youtube.com/@gmoa" data-bs-target="#videoModal">
                                <span></span>
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-7 wow fadeIn" data-wow-delay="0.3s">
                        <h4 class="text-primary mb-4 border-bottom border-primary border-2 d-inline-block p-2 title-border-radius">About Us</h4>
                        <h1 class="text-dark mb-4 display-5">We Connect Parents, Midwives, and Healthcare Professionals To Build A Healthier Future For Sri Lankan Children</h1>
                        <p class="text-dark mb-4"> Our Baby Care Management System modernizes Sri Lanka's antenatal and postnatal care by replacing manual record-keeping with intelligent digital solutions. Designed specifically for Sri Lanka's unique healthcare framework, we bridge the gap between domiciliary care provided by Public Health Midwives and clinic care managed by medical officers to ensure every child receives optimal health monitoring and care.
                        </p>
                        <div class="row mb-4">
                            <div class="col-lg-6">
                                <h6 class="mb-3"><i class="fas fa-check-circle me-2"></i>Growth Monitoring</h6>
                                <h6 class="mb-3"><i class="fas fa-check-circle me-2 text-primary"></i>Vaccination Management</h6>
                                <h6 class="mb-3"><i class="fas fa-check-circle me-2 text-secondary"></i>Digital Health Records</h6>
                            </div>
                            <div class="col-lg-6">
                                <h6 class="mb-3"><i class="fas fa-check-circle me-2"></i>Professional Support</h6>
                                <h6 class="mb-3"><i class="fas fa-check-circle me-2 text-primary"></i>Health Analytics</h6>
                                <h6><i class="fas fa-check-circle me-2 text-secondary"></i>Secure Environment</h6>
                            </div>
                        </div>
                        <a href="" class="btn btn-primary px-5 py-3 btn-border-radius">More Details</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Video -->
        <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Youtube Video</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- 16:9 aspect ratio -->
                        <div class="ratio ratio-16x9">
                            <iframe class="embed-responsive-item" src="" id="video" allowfullscreen allowscriptaccess="always"
                                allow="autoplay"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- About End -->


        <!-- Service Start -->
        <div class="container-fluid service py-5">
            <div class="container py-5">
                <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 700px;">
                    <h4 class="text-primary mb-4 border-bottom border-primary border-2 d-inline-block p-2 title-border-radius">What We Do</h4>
                    <h1 class="mb-5 display-3">Comprehensive Tools For Complete Baby Care Management</h1>
                </div>
                <div class="row g-5">
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeIn" data-wow-delay="0.1s">
                        <div class="text-center border-primary border bg-white service-item">
                            <div class="service-content d-flex align-items-center justify-content-center p-4">
                                <div class="service-content-inner">
                                    <div class="p-4"><i class="fas fa-gamepad fa-6x text-primary"></i></div>
                                    <a href="#" class="h4">Health Tracking</a>
                                    <p class="my-3">Our advanced system tracks your baby's growth patterns, vaccination history, and developmental milestones. Healthcare professionals can identify concerns early while parents stay informed about their child's progress through easy-to-understand visualizations and timely reports.</p>
                                    <a href="#" class="btn btn-primary text-white px-4 py-2 my-2 btn-border-radius">Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeIn" data-wow-delay="0.3s">
                        <div class="text-center border-primary border bg-white service-item">
                            <div class="service-content d-flex align-items-center justify-content-center p-4">
                                <div class="service-content-inner">
                                    <div class="p-4"><i class="fas fa-sort-alpha-down fa-6x text-primary"></i></div>
                                    <a href="#" class="h4">Vaccination & Appointment Reminders</a>
                                    <p class="my-3">Never miss important healthcare events with our smart reminder system. Receive timely notifications about upcoming vaccinations, weighing dates, and check-ups. Our scheduling system coordinates between parents and healthcare providers to ensure optimal care timing.</p>
                                    <a href="#" class="btn btn-primary text-white px-4 py-2 my-2 btn-border-radius">Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeIn" data-wow-delay="0.5s">
                        <div class="text-center border-primary border bg-white service-item">
                            <div class="service-content d-flex align-items-center justify-content-center p-4">
                                <div class="service-content-inner">
                                    <div class="p-4"><i class="fas fa-users fa-6x text-primary"></i></div>
                                    <a href="#" class="h4">Seamless Communication</a>
                                    <p class="my-3">Direct connection between parents and healthcare providers ensures timely advice and support. Midwives can send personalized care instructions, while parents can ask questions and receive guidance instantly through our secure messaging system, bridging the gap in care.</p>
                                    <a href="#" class="btn btn-primary text-white px-4 py-2 my-2 btn-border-radius">Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeIn" data-wow-delay="0.7s">
                        <div class="text-center border-primary border bg-white service-item">
                            <div class="service-content d-flex align-items-center justify-content-center p-4">
                                <div class="service-content-inner">
                                    <div class="p-4"><i class="fas fa-user-nurse fa-6x text-primary"></i></div>
                                    <a href="#" class="h4"> Health Analytics</a>
                                    <p class="my-3">Transform health data into actionable insights with our analytical tools. Track growth patterns against national standards, monitor vaccination compliance, and receive personalized recommendations based on your child's unique development profile and health needs.</p>
                                    <a href="#" class="btn btn-primary text-white px-4 py-2 my-2 btn-border-radius">Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Service End -->

        <!-- Footer Start -->
        <div class="container-fluid footer py-5 wow fadeIn" data-wow-delay="0.1s" id="contact">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-md-8 col-lg-6 col-xl-4">
                        <div class="footer-item">
                            <h2 class="fw-bold mb-3"><span class="text-primary mb-0">Baby</span><span class="text-secondary">Care</span></h2>
                            <p class="mb-4">The BabyCare Management System is a collaborative initiative supporting Sri Lanka's public health infrastructure by connecting parents, midwives, and medical officers through technology-enabled care</p>
                            <div class="border border-primary p-3 rounded bg-light">
                                <h5 class="mb-3">Newsletter</h5>
                                <div class="position-relative mx-auto border border-primary rounded" style="max-width: 400px;">
                                    <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email">
                                    <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2 text-white">SignUp</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-6 col-xl-4">
                        <div class="footer-item">
                            <div class="d-flex flex-column p-4 ps-5 text-dark border border-primary"
                            style="border-radius: 50% 20% / 10% 40%;">
                                <p>Monday: 8am to 5pm</p>
                                <p>Tuesday: 8am to 5pm</p>
                                <p>Wednes: 8am to 5pm</p>
                                <p>Thursday: 8am to 5pm</p>
                                <p>Friday: 8am to 5pm</p>
                                <p>Saturday: 8am to 5pm</p>
                                <p class="mb-0">Sunday: Closed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-6 col-xl-4">
                        <div class="footer-item">
                            <h4 class="text-primary mb-4 border-bottom border-primary border-2 d-inline-block p-2 title-border-radius">LOCATION</h4>
                            <div class="d-flex flex-column align-items-start">
                                <a href="" class="text-body mb-4"><i class="fa fa-map-marker-alt text-primary me-2"></i> Ministry of Health Building, Colombo 10, Sri Lanka</a>
                                <a href="" class="text-start rounded-0 text-body mb-4"><i class="fa fa-phone-alt text-primary me-2"></i> (011) 2456-7890</a>
                                <a href="" class="text-start rounded-0 text-body mb-4"><i class="fas fa-envelope text-primary me-2"></i> support@babycare.health.gov.lk</a>
                                <a href="" class="text-start rounded-0 text-body mb-4"><i class="fa fa-clock text-primary me-2"></i> 24/7 Technical Support Available</a>
                                <div class="footer-icon d-flex">
                                    <a class="btn btn-primary btn-sm-square me-3 rounded-circle text-white" href=""><i class="fab fa-facebook-f"></i></a>
                                    <a class="btn btn-primary btn-sm-square me-3 rounded-circle text-white" href=""><i class="fab fa-twitter"></i></a>
                                    <a href="#" class="btn btn-primary btn-sm-square me-3 rounded-circle text-white"><i class="fab fa-instagram"></i></a>
                                    <a href="#" class="btn btn-primary btn-sm-square rounded-circle text-white"><i class="fab fa-linkedin-in"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="footer-item">
                            <h4 class="text-primary mb-4 border-bottom border-primary border-2 d-inline-block p-2 title-border-radius">OUR GALLARY</h4>
                            <div class="row g-3">
                                <div class="col-4">
                                    <div class="footer-galary-img rounded-circle border border-primary">
                                        <img src="img/galary-1.jpg" class="img-fluid rounded-circle p-2" alt="">
                                    </div>
                               </div>
                               <div class="col-4">
                                    <div class="footer-galary-img rounded-circle border border-primary">
                                        <img src="img/galary-2.jpg" class="img-fluid rounded-circle p-2" alt="">
                                    </div>
                               </div>
                                <div class="col-4">
                                    <div class="footer-galary-img rounded-circle border border-primary">
                                        <img src="img/galary-3.jpg" class="img-fluid rounded-circle p-2" alt="">
                                    </div>
                               </div>
                                <div class="col-4">
                                    <div class="footer-galary-img rounded-circle border border-primary">
                                        <img src="img/galary-4.jpg" class="img-fluid rounded-circle p-2" alt="">
                                    </div>
                               </div>
                                <div class="col-4">
                                    <div class="footer-galary-img rounded-circle border border-primary">
                                        <img src="img/galary-5.jpg" class="img-fluid rounded-circle p-2" alt="">
                                    </div>
                               </div>
                                <div class="col-4">
                                    <div class="footer-galary-img rounded-circle border border-primary">
                                        <img src="img/galary-6.jpg" class="img-fluid rounded-circle p-2" alt="">
                                    </div>
                               </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
        <!-- Footer End -->


        <!-- Copyright Start -->
        <div class="container-fluid copyright bg-dark py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <span class="text-light"><a href="#"><i class="fas fa-copyright text-light me-2"></i> ©BabyCare Management System</a>, All right reserved.</span>
                    </div>
                    <div class="col-md-6 my-auto text-center text-md-end text-white">
                        <!--/*** This template is free as long as you keep the below author’s credit link/attribution link/backlink. ***/-->
                        <!--/*** If you'd like to use the template without the below author’s credit link/attribution link/backlink, ***/-->
                        <!--/*** you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". ***/-->
                        Designed By <a class="border-bottom" href="#">Piyumi Divyanjalee</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Copyright End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="front/lib/wow/wow.min.js"></script>
    <script src="front/lib/easing/easing.min.js"></script>
    <script src="front/lib/waypoints/waypoints.min.js"></script>
    <script src="front/lib/lightbox/js/lightbox.min.js"></script>
    <script src="front/lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="front/js/main.js"></script>
    </body>

</html>
