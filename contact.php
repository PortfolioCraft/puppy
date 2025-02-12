<?php
session_start();
// Initialize message variable
$message = '';

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input data
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $messageContent = htmlspecialchars(trim($_POST['message']));

    // Email recipient
    $to = "mbitsiedward@gmail.com"; // Replace with your email address

    // Prepare email headers
    $headers = "From: $name <$email>\r\n";
    $headers .= "Reply-To: $email\r\n";

    // Prepare email body
    $body = "Name: $name\n";
    $body .= "Email: $email\n";
    $body .= "Subject: $subject\n";
    $body .= "Message: $messageContent\n";

    // Send email
    if (mail($to, $subject, $body, $headers)) {
        $message = 'Oops! Something went wrong. Please try again later.';
    } else {
        $message = 'Thank you for contacting us, ' . $name . '. We will get back to you shortly!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> <!-- Add Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="frontend/uploads/logo1.png">
        <style>
        body {
            background-color: #f8f9fa;
        }
        .contact-container {
            margin: 50px auto;
            max-width: 600px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .contact-title {
            margin-bottom: 20px;
        }
        .message {
            color: green;
        }
    </style>
    <!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '604397305348499');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=604397305348499&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="background-color: #007BFF;">
  <div class="container">
  <b><a class="navbar-brand" href="index.php" style="color: white; font-size: 30px"> Toy Paradise
    <!--  <img src="uploads/logo.png" alt="Mouri Logo" width="150" class="d-inline-block align-top"> -->
    </a></b>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" style="background-color: white;">
      <span class="navbar-toggler-icon" ></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a class="nav-link" href="index.php" style="color: white; font-size: 1.2rem; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); transition: color 0.3s;">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="all_products.php" style="color: white; font-size: 1.2rem; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); transition: color 0.3s;">Shop</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="contact.php" style="color: white; font-size: 1.2rem; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); transition: color 0.3s;">Contact</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/trial/admin/login.php" style="color: white; font-size: 1.2rem; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); transition: color 0.3s;">Login</a>
        </li>
        <!-- Profile Icon -->
       
      </ul>
    </div>
  </div>
</nav>
<br>



    <div class="contact-container" >
        <h2 class="contact-title" style="font-family: 'DM Serif Text', serif; font-weight: 400; font-style: normal;">Contact Us</h2>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="contact.php" method="POST">
            <div class="form-group">
                <label for="name" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">Name</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="subject" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">Subject</label>
                <input type="text" id="subject" name="subject" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="message" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">Message</label>
                <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn" style="background-color:  #007BFF; color: white; font-family: 'DM Serif Text', serif; font-weight: 400; font-style: normal;"">Send Message</button>
        </form>
    </div>

    <footer class="site-footer" style="background-color: #ade8f4;">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <h6 style="color: rgb(15, 75, 4); font-size: 1.2rem; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); transition: color 0.3s;">About</h6>
                <p class="text-justify" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal; color: black;">
                    Welcome to <strong>Toy Paradise</strong>, your one-stop shop for quality toys that bring smiles to kids and families! 
                    From educational toys to the latest fun gadgets, we ensure every product meets the highest standards of quality. 
                    <a href="https://www.thegreaterrandservices.co.ke/" style="color: red;">Enjoy fast and reliable delivery</a> in less than <strong>48 hours</strong> to make every moment special. 
                    Shop with confidence and let us deliver happiness to your doorstep!
                </p>
                <!-- Social Media Links -->
                <div class="social-icons" style="margin-top: 10px;">
                <a href="https://wa.me/254112809190" target="_blank" style="margin-right: 10px; color: rgb(91, 224, 57); font-size: 1.5rem;">
    <i class="fa-brands fa-whatsapp"></i>
</a>
                   <a href="https://www.instagram.com/toys_puppyparadise/profilecard/?igsh=MTduYzE5dHBiOXlhZg==" target="_blank" style="margin-right: 10px; color: #E1306C; font-size: 1.5rem;">
                        <i class="fa-brands fa-instagram"></i>
                    </a> 
                    <a href="https://www.tiktok.com/@toys_puppy_paradise?_t=ZM-8sI5b7NOELd&_r=1" target="_blank" style="margin-right: 10px; color:rgb(241, 9, 9); font-size: 1.5rem;">
                        <i class="fa-brands fa-tiktok"></i>
                    </a>
                    
                </div>
            </div>

            <div class="col-xs-6 col-md-3">
                <h6 style="color: rgb(15, 75, 4); font-size: 1.2rem; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); transition: color 0.3s;">Legal Informations</h6>
                <ul class="footer-links" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal; color: white;">
                    <li><a href="#">Privacy Policies</a></li>
                    <li><a href="#">Refund Policy</a></li>
                    <li><a href="#">Shipping Policy</a></li>
                    <li><a href="#">Terms Of Service</a></li>
                    <li><a href="#">Contact Infomation</a></li>
                </ul>
            </div>

            <div class="col-xs-6 col-md-3">
                <h6 style="color:rgb(15, 75, 4); font-size: 1.2rem; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); transition: color 0.3s;">Quick Links</h6>
                <ul class="footer-links" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="all_products.php">Shop</a></li>
                    <li><a href="Contact.php">Contact Us</a></li>
                    <li><a href="pet.html">Puppy Paradise</a></li>
                </ul>
            </div>
        </div>
        <hr>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-sm-6 col-xs-12" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">
                <p class="copyright-text" style="text-align: center; color: black;">Copyright &copy; <span id="current-year">2024</span> All Rights Reserved by 
                    <a href="#">Toy Paradise</a>.
                </p>
            </div>
        </div>
    </div>
</footer>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#review-form').on('submit', function(event) {
            event.preventDefault(); // Prevent default form submission
            
            $.ajax({
                url: 'submit_review.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#notification').html("<div class='notification success'>Thank you for your review! It is awaiting approval.</div>").show();
                    $('#review-form')[0].reset(); // Clear the form
                },
                error: function() {
                    $('#notification').html("<div class='notification error' style='text-align: center; color: blue; padding-top: 20px; '>Thank you for your review! It is awaiting approval.</div>").show();
                }
            });
        });
    });
</script>


<script src="script.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>

<!-- Popper.js, required for Bootstrap's JavaScript components -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJtKU8A8SMDxgIb2M5bqgr1gs2L1S3UsSkf3S50GwvqZVDk5E" crossorigin="anonymous"></script>

<!-- Bootstrap 4 JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

<!-- Bootstrap 4 JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Calculate total price dynamically
    $('#quantity').on('input', function () {
        var quantity = $(this).val();
        var price = <?php echo $product['discounted_price']; ?>;
        $('#total-price').text('$' + (quantity * price).toFixed(2));
    });

    // Handle tab switching
    $('.tab-button').on('click', function () {
        var tabId = $(this).data('tab');
        $('.tab-button').removeClass('active');
        $(this).addClass('active');
        $('.tab-content').removeClass('active');
        $('#' + tabId).addClass('active');
    });

    function changeMainImage(img) {
        document.getElementById('mainImage').src = img; // Change the main image
    }
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Calculate total price dynamically
    $('#quantity').on('input', function () {
        var quantity = $(this).val();
        var price = <?php echo $product['discounted_price']; ?>;
        $('#total-price').text('Ksh' + (quantity * price).toFixed(2));
    });

    // Handle tab switching
    $('.tab-button').on('click', function () {
        var tabId = $(this).data('tab');
        $('.tab-button').removeClass('active');
        $(this).addClass('active');
        $('.tab-content').removeClass('active');
        $('#' + tabId).addClass('active');
    });

    function changeMainImage(img) {
    // Change the main image source
    document.getElementById('mainImage').src = img;

    // Remove the 'selected' class from all thumbnails
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach(thumb => thumb.classList.remove('selected'));

    // Add the 'selected' class to the currently clicked thumbnail
    // Match against the original image URL
    const selectedThumbnail = Array.from(thumbnails).find(thumb => thumb.src === img);
    if (selectedThumbnail) {
        selectedThumbnail.classList.add('selected');
    }
}

function toggleMenu() {
    var navLinks = document.getElementById("navLinks");
    if (navLinks.style.display === "flex") {
        navLinks.style.display = "none";
    } else {
        navLinks.style.display = "flex";
    }
}

</script>

</body>
</html>
