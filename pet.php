<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $service = isset($_POST['service']) ? $_POST['service'] : '';
    $date = isset($_POST['date']) ? $_POST['date'] : '';
    $time = isset($_POST['time']) ? $_POST['time'] : '';
    $message = isset($_POST['message']) ? $_POST['message'] : '';

    // Proceed with inserting data into the database
    $conn = new mysqli("localhost", "root", "", "shop");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO appointments (name, phone, service, appointment_date, appointment_time, special_requests) 
            VALUES ('$name', '$phone', '$service', '$date', '$time', '$message')";

    if ($conn->query($sql) === TRUE) {
        echo "Appointment booked successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/pages/icon.png" type="image/png">
    <title>Puppy Paradise</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Bootstrap Css -->
    <link rel="stylesheet" href="vender/bootstrap/css/bootstrap.min.css">
    <!-- Icofont Css -->
    <link rel="stylesheet" href="vender/icofont/icofont.min.css">
    <!-- AOS Css -->
    <link rel="stylesheet" href="vender/aos/dist/aos.css">
    <!-- Remix Icon -->
    <link rel="stylesheet" href="vender/remixicon/remixicon.css">
    <!-- Custom Css -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Common Css -->
    <link rel="stylesheet" href="css/common.css">
</head>

<body>
    <!-- Page loading spinner -->
    <div class="page-loading active">
        <div class="page-loading-inner">
            <div class="page-spinner"></div>
            <span>Loading...</span>
        </div>
    </div>
    <div class="homepage">
        <!-- navbar -->
        <nav class="navbar osahan-main-nav navbar-expand pets-nav p-0">
            <div class="container py-lg-0 py-3">
                <div class="position-relative d-flex align-items-center gap-2 site-brand">
                    <i class="ri-baidu-line fs-2 lh-1 text-danger"></i>
                    <div class="lh-1">
                        <h5 class="fw-bold m-0 text-white">Puppy</h5>
                        <small class="text-white-50">Paradise</small>
                    </div>
                    <a class="stretched-link" href="index.html"></a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ms-auto gap-4 m-none">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.html"><i class="ri-apps-2-line"></i> Home</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    </ul>
                    <div class="d-flex align-items-center gap-4 ms-auto ms-lg-0 ps-4">
                        <!-- Contact Number -->
                        <a href="#" class="link-danger d-none d-lg-block">+254796235870</a>
                        
                        <!-- WhatsApp Icon -->
                        <a href="https://wa.me/254796235870" target="_blank" class="text-success" style="font-size: 1.5rem;">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                        
                        <!-- Mobile Menu Toggle -->
                        <a href="#" class="link-light d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
                            <i class="ri-menu-3-line ri-lg"></i>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Header -->
        <div class="py-0">
            <div class="container">
                <div class="row align-items-center g-4 pb-lg-5">
                    <div class="col-lg-6 col-12 pb-lg-5 pe-lg-5">
                        <div class="text-white text-center text-md-start py-5" data-aos="fade-right" data-aos-duration="1000">
                            <div class="mb-5">
                                <h6 class="fw-light text-white-50 mb-0">Welcome to Puppy Paradise!</h6>
                                <h1 class="fw-bold display-2 py-2">Your Pet’s Favourite Place</h1>
                                <p class="lead pe-lg-5">Located in Malindi, we are committed to making every pet feel special and pampered. From relaxing grooming sessions, we’ve got you covered!</p>
                            </div>
                          <!---  <a href="#" class="btn btn-danger btn-lg rounded-pill">Explore Now</a> -->
                        </div>
                    </div>
                    <div class="col-lg-6 col-12 pb-lg-5">
                        <img src="img/banner.png" alt="header-img" class="img-fluid d-block mx-auto" data-aos="fade-left" data-aos-duration="1000">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- OFF -->
    <div class="mt-n6">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bg-danger text-white p-5 rounded-5">
                        <div class="row align-items-center justify-content-center g-4">
                            <div class="col-lg-1 col-12">
                                <div class="text-center text-md-start"><i class="ri-baidu-line display-3"></i></div>
                            </div>
                            <div class="col-lg-7">
                                <div class="text-center text-md-start">
                                    <h4 class="fw-bold">Pet grooming place in malindi!</h4>
                                    <p class="mb-0 text-white-50"> Let’s create happy moments for your furry friends!</p>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="text-center text-md-end"><a href="#" class="btn btn-light btn-lg rounded-pill">WOOF WOOF</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- category -->
    <div class="py-5 bg-light">
        <div class="container py-4">
            <div data-aos="fade-up" data-aos-duration="500" class="row mb-5">
                <div class="col-6 mx-auto">
                    <div class="text-center">
                        <h1 class="fw-bold display-5" id="services">Our Services</h1>
                        <p class="text-muted mb-3">We offer a wide range of grooming services tailored to meet your pet’s unique needs:</p>
                        <span class="bg-danger px-5 mx-auto d-flex pb-2 justify-content-center col-1 rounded-pill"></span>
                    </div>
                </div>
            </div>

            <div class="row g-4 category">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card border-0 shadow rounded-5 text-center h-100 p-5" data-aos="zoom-in" data-aos-duration="1000">
                        <div class="card-body">
                            <i class="icofont-dog display-1 text-danger"></i>
                            <h4 class="card-title fw-bold py-3 mb-0">
                                Full<br> Grooming
                            </h4>
                            <p class="mb-0 small text-muted">Treat your pet to a complete spa experience, including a bath, brushing, nail trimming, and ear cleaning.</p>
                            <a href="#" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card border-0 shadow rounded-5 text-center h-100 p-5" data-aos="zoom-in" data-aos-duration="1000">
                        <div class="card-body">
                            <i class="icofont-cat-alt-3 display-1 text-danger"></i>
                            <h4 class="card-title fw-bold py-3 mb-0">
                                Nail<br> Trim
                            </h4>
                            <p class="mb-0 small text-muted">Keep those paws neat and comfortable with our gentle nail trimming services.</p>
                            <a href="#" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card border-0 shadow rounded-5 text-center h-100 p-5" data-aos="zoom-in" data-aos-duration="1000">
                        <div class="card-body">
                            <i class="icofont-dog display-1 text-danger"></i>
                            <h4 class="card-title fw-bold py-3 mb-0">
                                Hair <br> Cut
                            </h4>
                            <p class="mb-0 small text-muted">Stylish and comfortable haircuts to suit every breed and personality.</p>
                            <a href="#" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card border-0 shadow rounded-5 text-center h-100 p-5" data-aos="zoom-in" data-aos-duration="1000">
                        <div class="card-body">
                            <i class="icofont-cat-alt-3 display-1 text-danger"></i>
                            <h4 class="card-title fw-bold py-3 mb-0">
                                Bath<br>
                            </h4>
                            <p class="mb-0 small text-muted">Give your furry friend a refreshing and relaxing bath using our pet-safe, high-quality shampoos and conditioners.</p>
                            <a href="#" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <!-- Clients Testimonials -->
    <div class="bg-new-products text-white py-5">
        <div class="container py-4">
            <div data-aos="fade-up" data-aos-duration="500" class="row mb-5">
                <div class="col-6 mx-auto">
                    <div class="text-center">
                        <p class="text-white-50 mb-2 fw-normal text-uppercase">What people say about our shop</p>
                        <h1 class="fw-bold display-5">Clients Testimonials</h1>
                        <span class="bg-danger px-5 mx-auto d-flex pb-2 justify-content-center col-1 rounded-pill"></span>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-12">
                    <div class="card bg-transparent border-0 rounded-0 text-center p-4 h-100" data-aos="fade-up" data-aos-duration="1300">
                        <img src="img/team/member-1.jpg" alt="testimonial" class="img-fluid rounded-circle d-block mx-auto testimonial-img">
                        <div class="card-body mt-3">
                            <p class="card-title lead">Puppy Paradise is the best grooming spot I’ve ever visited. My dog loves it here!.</p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <p class="text-danger mb-0">Glen Sparkle, Malindi</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="card bg-transparent border-0 rounded-0 text-center p-4 h-100" data-aos="fade-up" data-aos-duration="1300">
                        <img src="img/team/member-2.jpg" alt="testimonial" class="img-fluid rounded-circle d-block mx-auto testimonial-img">
                        <div class="card-body mt-3">
                            <p class="card-title lead">Amazing customer service. My cat’s coat has never been shinier.</p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <p class="text-danger mb-0">Glenda Richards, Malindi</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="card bg-transparent border-0 rounded-0 text-center p-4 h-100" data-aos="fade-up" data-aos-duration="1300">
                        <img src="img/team/member-3.jpg" alt="testimonial" class="img-fluid rounded-circle d-block mx-auto testimonial-img">
                        <div class="card-body mt-3">
                            <p class="card-title lead">The grooming team is friendly, and my dog looks adorable after every visit.</p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <p class="text-danger mb-0">Sue Sparkle, Malindi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="text-black py-5" style="background-color: #FFCF01; background-size: cover; background-position: center; background-repeat: no-repeat;">
    
            <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-6" data-aos="fade-up" data-aos-duration="500">
                    <div class="text-center">
                       
                        <h1 class="fw-bold display-1 text-danger" data-aos="fade-up" data-aos-duration="600" id="contact">Contact Us</h1>
                        <p class="lead text-dark">Let’s stay connected! Reach out to us for any inquiries or to book a grooming session.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-3 col-12">
                    <div class="text-center" data-aos="fade-up" data-aos-duration="1300">
                        <i class="ri-map-pin-2-line ri-3x"></i>
                        <div class="pt-2">
                            <h5 class="fw-bold">Our Office</h5>
                            <p class="text-muted mb-0">Mango Plaza, Lamu Road. <br>Malindi</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-12">
                    <div class="text-center" data-aos="fade-up" data-aos-duration="1300">
                        <i class="ri-mail-line ri-3x"></i>
                        <div class="pt-2">
                            <h5 class="fw-bold">Email address</h5>
                            <p class="text-muted mb-0">
                                <a class="__cf_email__" data-cfemail="cda8b5aca0bda1a88daaa0aca4a1e3aea2a0">toysandpuppyparadise@gmail.com</a><br>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-12">
                    <div class="text-center" data-aos="fade-up" data-aos-duration="1000">
                        <i class="ri-time-line ri-3x"></i>
                        <div class="pt-2">
                            <h5 class="fw-bold">Open Hours</h5>
                            <p class="text-muted mb-0">
                                Monday-Saturday 8:30 am – 7:30pm<br> Sunday Closed
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-12">
                    <div class="text-center" data-aos="fade-up" data-aos-duration="1300">
                        <i class="ri-phone-line ri-3x"></i>
                        <div class="pt-2">
                            <h5 class="fw-bold">Phone Number</h5>
                            <p class="text-muted mb-0">
                                +254112809190<br>  +254796235870
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Contact form -->
<div class="py-5">
    <div class="container py-5">
        <div class="row align-items-center g-54 g-md-5">
            <div class="col-lg-6 col-12">
                <img src="img/contact-img.jpg" alt="contacat-img" class="img-fluid rounded-5" data-aos="zoom-in-down" data-aos-duration="1000">
            </div>
            <div class="col-lg-6 col-12">
                <div class="mb-5" data-aos="fade-left" data-aos-duration="1000">
                    <h1 class="fw-bold pb-2">Book a session!</h1>
                    <p class="text-secondary-emphasis mb-0">Fill out the form below to schedule a grooming appointment for your pet:</p>
                </div>
               <!-- Your Existing Form -->
<form id="appointmentForm" class="form">
    <!-- Name -->
    <div class="mb-4">
        <label for="name" class="form-label">First &amp; Last Name</label>
        <input type="text" class="form-control bg-light rounded-0 border-0" name="name" id="name" placeholder="Enter your name" required>
    </div>

    <!-- Phone -->
    <div class="mb-4">
        <label for="phone" class="form-label">Phone Number</label>
        <input type="tel" class="form-control bg-light rounded-0 border-0" name="phone" id="phone" placeholder="Enter your phone number" required>
    </div>

    <!-- Grooming Service -->
    <div class="mb-4">
        <label for="service" class="form-label">Select Grooming Service</label>
        <select class="form-control bg-light rounded-0 border-0" name="service" id="service" required>
            <option value="">Select Service</option>
            <option value="haircut">Haircut</option>
            <option value="bath">Bath</option>
            <option value="nail_trim">Nail Trim</option>
            <option value="full_groom">Full Grooming</option>
        </select>
    </div>

    <!-- Date -->
    <div class="mb-4">
        <label for="date" class="form-label">Preferred Appointment Date</label>
        <input type="date" class="form-control bg-light rounded-0 border-0" name="date" id="date" required>
    </div>

    <!-- Time -->
    <div class="mb-4">
        <label for="time" class="form-label">Preferred Appointment Time</label>
        <input type="time" class="form-control bg-light rounded-0 border-0" name="time" id="time" required>
    </div>

    <!-- Message -->
    <div class="mb-4">
        <label for="message" class="form-label">Special Requests or Comments</label>
        <textarea class="form-control bg-light rounded-0 border-0" name="message" id="message" rows="6"></textarea>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-danger btn-lg rounded-pill">Book Appointment</button>
</form>

<!-- Success Message Placeholder -->
<div id="responseMessage" style="margin-top: 20px;"></div>

<!-- JavaScript Section -->
<script>
document.getElementById('appointmentForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent form from submitting normally

    // Collect form data
    const formData = new FormData(this);

    // Send data to the server using Fetch API
    fetch('submit_form.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const responseMessage = document.getElementById('responseMessage');
        if (data.status === 'success') {
            responseMessage.innerHTML = `<div style="color: green;">${data.message}</div>`;
            document.getElementById('appointmentForm').reset(); // Reset the form
        } else {
            responseMessage.innerHTML = `<div style="color: red;">${data.message}</div>`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
</script>

            </div>
        </div>
    </div>
</div>

    <!-- Newsletter Updates -->
    <div class="py-0 bg-warning-subtle">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="py-5 py-lg-0" data-aos="fade-up" data-aos-duration="1000">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-lg-6 col-12">
                                <div class="text-center text-md-start">
                                    <div class="mb-4">
                                        <h1 class="fw-bold text-black">Newsletter Updates</h1>
                                        <p>Enter your email address below to subscribe to our newsletter.</p>
                                    </div>
                                    <form class="d-grid d-md-flex gap-3">
                                        <input class="form-control rounded-pill py-3 px-4 shadow-none border-0" type="text" placeholder="Email address" aria-label="email">
                                        <button class="btn btn-danger rounded-pill py-2 px-5">Subscribe</button>
                                    </form>
                                    <p class="text-secondary-emphasis pt-3 mb-0">Your privacy is our policy.</p>
                                </div>
                            </div>
                            <div class="col-lg-5 col-12 d-none d-lg-block">
                                <img src="img/newsletter.png" alt="newsletter-dog" class="img-fluid mt-n6">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- footer -->
    <footer class="bg-footer text-white">
        <div class="container py-5 top-footer">
            <div class="row g-4 py-4">
                <div class="col-lg-3">
                    <h5 class="fw-bold pb-3">Services</h5>
                    <ul class="list-unstyled d-grid gap-2 text-white-50">
                        <li class="text-secondary-emphasis"><a href="#">Full Grooming</a></li>
                        <li class="text-secondary-emphasis"><a href="#">Nail Trim</a></li>
                        <li class="text-secondary-emphasis"><a href="#">Haircut</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-6">
                    <h5 class="fw-bold pb-3">Contacts</h5>
                    <ul class="list-unstyled d-grid gap-2 text-white-50">
                        <li>Phone: +254796235870</li>
                        <li>Phone: +254112809190</li>
                        <li>Email: <a class="__cf_email__" data-cfemail="0d68756c607d61684d6a606c6461236e6260">toysandpuppyparadise@gmail.com</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-12">
                    <h5 class="fw-bold pb-3">Opening Hours</h5>
                    <ul class="list-unstyled d-grid gap-2 text-white-50">
                        <li>Mon - Sat: 8:30am - 7:30pm</li>
                        <li>Sun: Closed</li>
                    </ul>
                </div>
                <div class="col-lg-4 col-12 ps-lg-5">
                    <div class="mb-4">
                        <h5 class="fw-bold pb-3">Sign up for our newsletter</h5>
                        <p class="text-white-50">Get the latest deals and offers right to your inbox.</p>
                    </div>
                    <form class="d-flex overflow-hidden rounded-pill">
                        <input class="form-control border-0 rounded-0 py-2 px-4" type="text" placeholder="Enter Email Address" aria-label="subscribe"> <button class="btn btn-danger rounded-0 py-2 px-4" type="submit">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
       
        
    </footer>
    <!--nav Sidebar -->
    <div class="offcanvas offcanvas-end bg-dark border-0 text-white" tabindex="-1" id="sidebar">
        <div class="offcanvas-header d-flex justify-content-between">
            <div class="position-relative d-flex align-items-center gap-2 site-brand">
                <i class="ri-baidu-line fs-2 lh-1 text-white"></i>
                <div class="lh-1">
                    <h5 class="fw-bold m-0 text-white">Puppy</h5>
                    <small class="text-white-50">Paradise</small>
                </div>
                <a class="stretched-link" href="index.html"></a>
            </div>
            <a href="#" class="text-white" data-bs-dismiss="offcanvas" aria-label="Close"><i class="ri-close-line ri-lg"></i></a>
        </div>
        <div class="offcanvas-body">
            <div class="sidebar-nav">
                <ul class="navbar-nav justify-content-end flex-grow-1">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.html"><i class="ri-apps-2-line"></i> Home</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                  
                   
                                        
                                           
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bootsrap Bundle Js -->
    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    <script src="vender/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Jquery Js -->
    <script src="vender/jquery/jquery-3.6.4.min.js"></script>
    <!-- AOS Js -->
    <script src="vender/aos/dist/aos.js"></script>
    <!-- Custom Js -->
    <script src="js/script.js"></script>
</body>

</html>