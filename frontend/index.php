<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Welcome to BookHive | Your Online Bookstore</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        
        /* Navbar */
        .navbar-custom { background-color: #ffffff; border: none; margin-bottom: 0; padding: 15px 0; box-shadow: 0 2px 15px rgba(0,0,0,0.05); }
        .navbar-custom .navbar-brand { color: #2c3e50; font-weight: 700; font-size: 28px; letter-spacing: -0.5px;}
        .navbar-custom .nav>li>a { color: #576574; font-weight: 500; font-size: 15px; transition: 0.3s;}
        .navbar-custom .nav>li>a:hover { color: #3498db; }

        /* Hero Banner */
        .hero-section { background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white; padding: 100px 0; text-align: center; position: relative; overflow: hidden; }
        .hero-section h1 { font-size: 56px; font-weight: 700; margin-bottom: 20px; letter-spacing: -1px; }
        .hero-section p { font-size: 20px; font-weight: 300; opacity: 0.9; margin-bottom: 40px; }
        .btn-hero { background-color: #2ecc71; color: white; padding: 15px 40px; font-size: 18px; font-weight: 600; border-radius: 50px; border: none; transition: 0.3s; box-shadow: 0 10px 20px rgba(46, 204, 113, 0.3); text-decoration: none; display: inline-block;}
        .btn-hero:hover { background-color: #27ae60; color: white; transform: translateY(-3px); box-shadow: 0 15px 25px rgba(46, 204, 113, 0.4); text-decoration: none;}

        /* Feature Cards */
        .features { padding: 60px 0; background: white; }
        .feature-box { text-align: center; padding: 30px 20px; transition: 0.3s; }
        .feature-box:hover { transform: translateY(-5px); }
        .feature-icon { font-size: 40px; color: #3498db; margin-bottom: 20px; }
        .feature-box h4 { font-weight: 600; color: #2c3e50; }

        /* Featured Books Section */
        .featured-books { padding: 80px 0; }
        .section-title { text-align: center; font-weight: 700; color: #2c3e50; margin-bottom: 50px; font-size: 32px; }
        .book-card { background: white; padding: 30px 20px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 30px; transition: 0.3s; text-align: center;}
        .book-card:hover { transform: translateY(-10px); box-shadow: 0 15px 40px rgba(0,0,0,0.1); }
        .book-price { font-size: 22px; font-weight: 600; color: #2ecc71; margin: 15px 0;}

        /* Footer */
        .footer { background-color: #2c3e50; color: white; padding: 40px 0; text-align: center; margin-top: 40px; }
        .footer p { margin: 0; opacity: 0.7; font-size: 14px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-custom">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php"><i class="fa fa-book text-primary" style="color: #3498db;"></i> BookHive</a>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="shop.php">Browse Catalog</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="my_orders.php">Track Order</a></li>
                    <li style="border-left: 1px solid #ecf0f1; margin-left: 15px; padding-left: 5px;">
                        <a href="login.php?logout=true" style="color: #e74c3c;"><i class="fa fa-sign-out"></i> Logout</a>
                    </li>
                <?php else: ?>
                    <li style="border-left: 1px solid #ecf0f1; margin-left: 15px; padding-left: 5px;">
                        <a href="login.php" style="color: #3498db;"><i class="fa fa-user"></i> Login / Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="hero-section">
        <div class="container">
            <h1>Your Next Great Adventure Awaits</h1>
            <p>Explore thousands of books, from gripping fiction to cutting-edge tech manuals, available over the counter.</p>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="shop.php" class="btn-hero"><i class="fa fa-shopping-bag"></i> Browse Catalog</a>
            <?php else: ?>
                <a href="login.php" class="btn-hero"><i class="fa fa-user-plus"></i> Sign In / Register to Get Started</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="features">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-box"><i class="fa fa-shopping-basket feature-icon"></i><h4>Over The Counter</h4><p class="text-muted">Place your order online and pick it up fresh from the counter.</p></div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box"><i class="fa fa-shield feature-icon"></i><h4>Secure Checkout</h4><p class="text-muted">Shop with confidence using our microservice-backed system.</p></div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box"><i class="fa fa-refresh feature-icon"></i><h4>Live Inventory</h4><p class="text-muted">No more out-of-stock surprises. Our system reflects real-time shelves.</p></div>
                </div>
            </div>
        </div>
    </div>

    <div class="featured-books container">
        <h2 class="section-title">Trending Now</h2>
        <div class="row" id="featuredGrid">
            <div class="col-md-12 text-center text-muted" id="loadingMsg">
                <i class="fa fa-spinner fa-spin fa-3x"></i><br><br><h4>Waking up the microservices...</h4>
            </div>
        </div>
        <div class="text-center" style="margin-top: 20px;">
            <a href="shop.php" class="btn btn-default" style="border-radius: 50px; padding: 10px 30px; font-weight: 500; border: 2px solid #3498db; color: #3498db;">View Full Catalog <i class="fa fa-arrow-right"></i></a>
        </div>
    </div>

    <div class="footer"><div class="container"><h4><i class="fa fa-book"></i> BookHive ERP</h4><p>&copy; 2026 BookHive Microservices Project.</p></div></div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function(){
        // Fetch inventory via API Gateway to populate the front page
        $.get("/api/gateway.php?route=books", function(response){
            $('#loadingMsg').hide();
            
            if(response.status === "success") {
                let featuredBooks = response.data.slice(0, 4);
                let html = '';
                
                $.each(featuredBooks, function(index, book){
                    html += `
                    <div class="col-md-3 col-sm-6">
                        <div class="book-card">
                            <i class="fa fa-book fa-4x" style="color: #bdc3c7; margin-bottom: 15px;"></i>
                            <h4 style="height: 45px; overflow:hidden; font-weight: 600; color: #2c3e50; font-size: 16px;">${book.title}</h4>
                            <p class="text-muted" style="font-size: 13px; margin-bottom:0;">${book.author}</p>
                            <div class="book-price">₱${parseFloat(book.price).toFixed(2)}</div>
                            <a href="shop.php" class="btn btn-primary btn-block" style="border-radius: 8px;">View Details</a>
                        </div>
                    </div>`;
                });
                
                $('#featuredGrid').html(html);
            } else {
                $('#featuredGrid').html('<div class="col-md-12 text-center text-danger">Failed to load featured books: ' + (response.message || 'Unknown error') + '</div>');
            }
        }).fail(function() {
            $('#loadingMsg').hide();
            $('#featuredGrid').html('<div class="col-md-12 text-center text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><br><br>Error connecting to API Gateway. Please check your network or server status.</div>');
        });
    });
    </script>
</body>
</html>