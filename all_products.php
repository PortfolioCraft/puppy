<?php 
$conn = new mysqli('localhost', 'root', '', 'shop');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Number of products to display per page
$products_per_page = 12;

// Get the current page number from the URL, default is 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the starting row for the query
$offset = ($page - 1) * $products_per_page;

// Set up default query conditions
$search_query = '';
$sort_query = 'ORDER BY name ASC'; // Default order by name ascending

// Check if search term is set
if (!empty($_GET['search'])) {
    $search_term = $conn->real_escape_string($_GET['search']);
    $search_query = "AND (name LIKE '%$search_term%' OR description LIKE '%$search_term%')";
}

// Check if sort option is set
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'name_asc':
            $sort_query = "ORDER BY name ASC";
            break;
        case 'name_desc':
            $sort_query = "ORDER BY name DESC";
            break;
        case 'price_asc':
            $sort_query = "ORDER BY price ASC";
            break;
        case 'price_desc':
            $sort_query = "ORDER BY price DESC";
            break;
        default:
            $sort_query = "ORDER BY name ASC"; // Default order by name ascending
            break;
    }
}

// Fetch the total number of products matching the search query
$total_products_result = $conn->query("SELECT COUNT(*) as total FROM products WHERE 1=1 $search_query");
$total_products_row = $total_products_result->fetch_assoc();
$total_products = $total_products_row['total'];

// Calculate total pages
$total_pages = ceil($total_products / $products_per_page);

// Final query with search, sort, and review count
$query = "
    SELECT products.*, 
           COALESCE(review_counts.review_count, 0) AS review_count
    FROM products
    LEFT JOIN (
        SELECT product_id, COUNT(*) AS review_count
        FROM reviews
        GROUP BY product_id
    ) AS review_counts ON products.id = review_counts.product_id
    WHERE 1=1 $search_query $sort_query 
    LIMIT $products_per_page OFFSET $offset";
    
$result = $conn->query($query);

// Check for query errors
if (!$result) {
    die("Query error: " . $conn->error);
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

    <title>All Products</title>
    <style>
        
.navbar-light .navbar-brand {
  color: #fff;
  font-size: 25px;
  text-transform: uppercase;
  font-weight: bold;
  letter-spacing: 2px;
}

.navbar-light .navbar-nav .active > .nav-link, .navbar-light .navbar-nav .nav-link.active, .navbar-light .navbar-nav .nav-link.show, .navbar-light .navbar-nav .show > .nav-link {
  color: #fff;
}

.navbar-light .navbar-nav .nav-link {
  color: #fff;
}

.navbar-toggler {
  background: #fff;
}

.navbar-nav {
  text-align: center;
}

.nav-link {
  padding: .2rem 1rem;
}

.nav-link.active,.nav-link:focus{
  color: #fff;
}

.navbar-toggler {
  padding: 1px 5px;
  font-size: 18px;
  line-height: 0.3;
}

.navbar-light .navbar-nav .nav-link:focus, .navbar-light .navbar-nav .nav-link:hover {
  color: #fff;
}

.btn {
  display: inline-block;
  background-color: #007bff; /* Bootstrap blue color */
  color: white;
  text-decoration: none;
  border-radius: 5px;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn:hover {
  background-color: #0056b3; /* Darker blue on hover */
  color: white;
}



/* Media queries for very small screens (mobile) */
@media (max-width: 576px) {

.btn {
    font-size: 1.8vw; /* Smaller button text */
    padding: 1.5vw 3vw; /* Smaller button padding */
}
}

.pagination .page-link {
    background-color: #8ac926;
    color: white;
    border-color: #8ac926;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.pagination .page-link:hover {
    background-color:  #007BFF; /* Slightly darker green on hover */
    color: white;
}

.pagination .page-item.active .page-link {
    background-color:  #007BFF; /* Darker green for active page */
    border-color:  #007BFF;
}

.pagination .page-link:focus {
    box-shadow: none;
}

/* Responsive Styling for Small Screens */
@media (max-width: 768px) {
    .add-to-cart {
        font-size: 14px;  /* Smaller font size for tablets */
        padding: 10px 18px;  /* Adjust padding */
        width: 100%;  /* Make button full width on smaller screens */
    }
}

@media (max-width: 480px) {
    .add-to-cart {
        font-size: 12px;  /* Smaller font size for phones */
        padding: 12px 20px;  /* Adjust padding to keep button balanced */
    }
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
    <a class="navbar-brand" href="index.php" style="color: white;"> Toy Paradise
    <!--  <img src="uploads/logo.png" alt="Mouri Logo" width="150" class="d-inline-block align-top"> -->
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" style="background-color: white;">
      <span class="navbar-toggler-icon" ></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a class="nav-link" href="index.php" style="color: white; font-size: 1.2rem; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); transition: color 0.3s;">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" style="color: white; font-size: 1.2rem; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); transition: color 0.3s;">Shop</a>
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
<br><br><br><br>

<h2 style="text-align: center; padding: 20px 0; font-family: 'DM Serif Text', serif; font-weight: 400;">All Products</h2>

<!-- Search and Sort Filter -->
<div style="display: flex; justify-content: center; margin-top: 20px; flex-wrap: wrap;">
    <form method="GET" action="" style="display: flex; gap: 10px; width: 100%; max-width: 800px;">
        <!-- Search Input -->
        <input type="text" name="search" placeholder="Search products..." 
               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
               style="flex: 1; padding: 12px 15px; border: 1px solid #ccc; border-radius: 25px; font-size: 16px; transition: border-color 0.3s; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">

        <!-- Sort Dropdown -->
        <select name="sort" style="padding: 12px 15px; border: 1px solid #ccc; border-radius: 25px; font-size: 16px; transition: border-color 0.3s;">
            <option value="">Sort by</option>
            <option value="name_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : ''; ?>>Name: A-Z</option>
            <option value="name_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_desc') ? 'selected' : ''; ?>>Name: Z-A</option>
            <option value="price_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
            <option value="price_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
        </select>

        <!-- Apply Button -->
        <button class="apply-button" type="submit" style="padding: 12px 20px; border: none; border-radius: 25px; background-color:  #007BFF; color: white; font-size: 16px; cursor: pointer; transition: background-color 0.3s;">
            Apply
        </button>
    </form>
</div>

<style>
    /* Additional styles for a modern look */
    input[type="text"],
    select {
        font-family: 'Arial', sans-serif; /* Change to your desired font */
    }

    input[type="text"]:focus,
    select:focus {
        border-color: #007BFF; /* Change to your theme color */
        outline: none; /* Remove default outline */
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Add shadow on focus */
    }

    .apply-button { /* Unique class for the Apply button */
        transition: background-color 0.3s;
        background-color: #FD7C09; /* Apply button specific background color */
    }

    .apply-button:hover {
        background-color: #e86a00; /* Darken the button color on hover */
    }

    @media (max-width: 600px) {
        form {
            flex-direction: column; /* Stack elements on small screens */
            align-items: stretch;
        }

        input[type="text"],
        select,
        .apply-button { /* Ensure apply button is responsive */
            width: 100%; /* Full width on small screens */
        }

        /* Adjust gap for small screens */
        form {
            gap: 8px;
        }
    }
</style>



<br>
<div class="product-container">
    <?php while ($product = $result->fetch_assoc()) { ?>
        <div class="product-card"  style="background-color: #ade8f4;">
        <?php 
        $images = json_decode($product['image']);
        if (!empty($images) && isset($images[0])) {
            $imgPath = htmlspecialchars($images[0]);
            if (file_exists($imgPath)) {
                echo "<img src='$imgPath' alt='Product Image' class='product-image'>";
            } else {
                echo "<img src='uploads/default.png' alt='Default Image' class='product-image'>";
            }
        } else {
            echo "<img src='uploads/default.png' alt='Default Image' class='product-image'>";
        }
        ?>
        <div class="product-details">
        <span class="badge bg-success" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">In Stock</span>
 
            <h3 class="product-name" style="font-family: 'DM Serif Text', serif; font-weight: 400; font-style: normal;"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p style=" font-family: 'Fira Sans', sans-serif; font-weight: 400; font-style: normal;"><?php echo $product['review_count']; ?> reviews</p>

          <!--  <p class="original-price" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">Ksh<?php echo number_format($product['price'], 2); ?></p> -->
            <p class="discounted-price" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">Ksh<?php echo number_format($product['discounted_price'], 2); ?></p>
            <a class="add-to-cart" href="product.php?id=<?php echo $product['id']; ?>" class="btn" style="font-family: 'DM Serif Text', serif; font-weight: 400; font-style: normal;  background-color:  #007BFF;">View Product</a>        </div>
    </div>
    <?php } ?>
</div>


<!-- Pagination Controls -->
<nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous" >
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

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
                    <li><a href="#">Home</a></li>
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

</body>
</html>
