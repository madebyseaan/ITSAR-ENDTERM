<?php 
session_start(); 
$is_logged_in = isset($_SESSION['user_id']) ? 'true' : 'false';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>BookHive | Storefront</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
    <style>
      body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
      .navbar-custom { background-color: #ffffff; border: none; margin-bottom: 0; padding: 10px 0;}
      .navbar-custom .navbar-brand { color: #2c3e50; font-weight: 600; font-size: 26px; }
      .navbar-custom .nav>li>a { color: #2c3e50; font-weight: 500; font-size: 15px;}
      
      .menu-bar { background-color: #2c3e50; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
      .menu-bar .nav>li>a { color: #ecf0f1; font-weight: 400; padding: 15px 20px; transition: 0.3s;}
      .menu-bar .nav>li>a:hover, .menu-bar .nav>li.active>a { background-color: #34495e; color: #3498db; }
      
      .hero-banner { background: linear-gradient(135deg, #3498db, #2c3e50); color: white; padding: 60px 0; text-align: center; }
      
      .book-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; transition: 0.3s; position: relative; text-align: center;}
      .book-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
      .category-badge { position: absolute; top: 15px; left: 15px; background: #f1c40f; color: #2c3e50; font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 600;}
      .book-price { font-size: 24px; font-weight: 600; color: #2ecc71; margin: 15px 0;}
      
      .btn { border-radius: 8px; font-weight: 500; transition: 0.3s; padding: 10px;}
      .btn-primary { background-color: #3498db; color: white; border: none;}
      .btn-primary:hover { background-color: #2980b9; color: white;}
      .btn-success { background-color: #2ecc71; color: white; border: none;}
      .btn-success:hover { background-color: #27ae60; color: white;}

      .cart-badge { background-color: #e74c3c; border-radius: 20px; padding: 4px 8px;}
      .modal-content { border-radius: 12px; border: none; overflow: hidden; }
      .modal-header .close { opacity: 1; color: white; }
    </style>
  </head>
  <body>

    <nav class="navbar navbar-custom">
      <div class="container">
        <div class="navbar-header"><a class="navbar-brand" href="index.php"><i class="fa fa-book text-primary"></i> BookHive</a></div>
        <ul class="nav navbar-nav navbar-right">
          <?php if($is_logged_in == 'true'): ?>
            <li><a href="my_orders.php"><i class="fa fa-truck"></i> Track Order</a></li>
            <li><a href="#" data-toggle="modal" data-target="#cartModal"><i class="fa fa-shopping-cart"></i> Cart <span class="badge cart-badge" id="cartCount">0</span></a></li>
            <li style="border-left: 1px solid #eee; margin-left: 10px;"><a href="login.php?logout=true"><i class="fa fa-sign-out"></i> Logout</a></li>
          <?php else: ?>
            <li><a href="#" data-toggle="modal" data-target="#cartModal"><i class="fa fa-shopping-cart"></i> Cart <span class="badge cart-badge" id="cartCount">0</span></a></li>
            <li style="border-left: 1px solid #eee; margin-left: 10px;"><a href="login.php"><i class="fa fa-user"></i> Login/Register</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </nav>

    <nav class="navbar menu-bar" style="border-radius:0; margin-bottom: 0;">
        <div class="container">
            <ul class="nav navbar-nav" id="categoryMenu">
                <li class="active"><a href="#" class="cat-filter" data-category="All">All Books</a></li>
                <li><a href="#" class="cat-filter" data-category="Programming">Programming & Tech</a></li>
                <li><a href="#" class="cat-filter" data-category="Fiction">Fiction</a></li>
                <li><a href="#" class="cat-filter" data-category="Business">Business</a></li>
            </ul>
        </div>
    </nav>

    <div class="hero-banner">
      <div class="container">
        <h1 style="font-weight: 600;">Discover Your Next Great Read</h1>
        <p style="font-weight: 300; font-size: 18px;">Browse freely. Sign in to place an Over-The-Counter order.</p>
      </div>
    </div>

    <div class="container" style="margin-top: 50px;">
        <div class="row" id="storeCatalog">
            <div class="col-md-12 text-center text-muted" id="loadingMsg" style="padding: 50px 0;"><i class="fa fa-spinner fa-spin fa-3x"></i><br><br><h4>Loading books from inventory...</h4></div>
        </div>
    </div>

    <div class="modal fade" id="loginRequiredModal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content text-center">
          <div class="modal-header" style="background-color: #e74c3c; color: white;">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" style="font-weight: 500;"><i class="fa fa-lock"></i> Authentication Required</h4>
          </div>
          <div class="modal-body" style="padding: 30px;">
            <p class="text-muted" style="font-size: 14px;">Please log in or register first to buy or add items to your cart.</p>
          </div>
          <div class="modal-footer" style="background-color: white; border-top: none;">
            <button type="button" class="btn btn-default btn-block" data-dismiss="modal">Keep Browsing</button>
            <button type="button" class="btn btn-primary btn-block" id="modalGoToLogin" style="background-color: #3498db; border: none; margin-top: 10px;">Log In / Register</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="quantityModal" tabindex="-1">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header" style="background-color: #3498db; color: white;">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" style="font-weight: 500;">Select Quantity</h4>
          </div>
          <div class="modal-body text-center">
            <h4 id="qtyBookTitle" style="font-weight: 600; color: #2c3e50;"></h4>
            <h3 id="qtyBookPrice" style="color: #2ecc71; font-weight: 600; margin-bottom: 20px;"></h3>
            
            <div class="form-group text-left">
                <label>Quantity to order:</label>
                <input type="number" id="qtyInput" class="form-control" value="1" min="1" style="font-size: 18px; text-align: center;">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-success" id="qtyConfirmBtn">Confirm</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="cartModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header" style="background-color: #2c3e50; color: white;">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" style="font-weight: 500;"><i class="fa fa-shopping-cart"></i> Your Over-The-Counter Cart</h4>
          </div>
          <div class="modal-body" style="background-color: #f8f9fa;">
            <ul class="list-group" id="cartItems" style="border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <li class="list-group-item text-muted" style="border: none;">Your cart is empty.</li>
            </ul>
            <h3 class="text-right" style="font-weight: 600; color: #2c3e50; margin-top: 20px;">Total: ₱<span id="cartTotal">0.00</span></h3>
          </div>
          <div class="modal-footer" style="background-color: white;">
            <button type="button" class="btn btn-default" data-dismiss="modal">Keep Shopping</button>
            <button type="button" class="btn btn-success" id="btnCheckout" disabled>Confirm Order (OTC) <i class="fa fa-check"></i></button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="orderSuccessModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-sm">
        <div class="modal-content text-center" style="padding: 20px;">
          <div style="font-size: 50px; color: #2ecc71; margin-bottom: 10px;">
            <i class="fa fa-check-circle"></i>
          </div>
          <h3 style="margin-top:0;">Success!</h3>
          <p id="orderSuccessMsg" style="font-size: 16px; color: #7f8c8d;"></p>
          <button type="button" class="btn btn-success btn-block" id="btnSuccessClose" style="margin-top: 15px;">OK</button>
        </div>
      </div>
    </div>

    <script>
    $(document).ready(function(){
        let cart = [];
        let totalAmount = 0.00;
        let allBooks = []; 
        let isLoggedIn = <?php echo $is_logged_in; ?>;

        // Use relative path for fetching books
        $.get("/api/gateway.php?route=books", function(response){
            $('#loadingMsg').hide();
            if(response.status === "success") {
                allBooks = response.data;
                renderCatalog(allBooks);
            }
        });

        function renderCatalog(booksData) {
            let catalogHtml = '';
            $.each(booksData, function(index, book){
                let catName = book.category ? book.category : "Uncategorized";
                let stockStatus = book.stock_quantity > 0 ? `<p class="text-success"><small>In Stock: ${book.stock_quantity}</small></p>` : `<p class="text-danger"><small>Out of Stock</small></p>`;
                let disableBtns = book.stock_quantity > 0 ? '' : 'disabled';

                catalogHtml += `
                <div class="col-md-3 col-sm-6">
                    <div class="book-card">
                        <span class="category-badge">${catName}</span>
                        <i class="fa fa-book fa-5x text-muted" style="margin: 20px 0; color: #bdc3c7;"></i>
                        <h4 style="height: 45px; overflow:hidden; font-weight: 600; color: #2c3e50; font-size: 16px;">${book.title}</h4>
                        ${stockStatus}
                        
                        <div class="book-price">₱${parseFloat(book.price).toFixed(2)}</div>
                        
                        <div class="row" style="margin: 0;">
                            <div class="col-xs-6" style="padding: 0 5px 0 0;">
                                <button class="btn btn-primary btn-block btn-open-qty" data-action="cart" data-id="${book.id}" data-title="${book.title}" data-price="${book.price}" data-stock="${book.stock_quantity}" ${disableBtns}>
                                  Add to Cart
                                </button>
                            </div>
                            <div class="col-xs-6" style="padding: 0 0 0 5px;">
                                <button class="btn btn-success btn-block btn-open-qty" data-action="buy" data-id="${book.id}" data-title="${book.title}" data-price="${book.price}" data-stock="${book.stock_quantity}" ${disableBtns}>
                                  Buy Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
            });
            $('#storeCatalog').html(catalogHtml);
        }

        $('.cat-filter').click(function(e){
            e.preventDefault();
            $('#categoryMenu li').removeClass('active');
            $(this).parent().addClass('active');
            let selectedCategory = $(this).data('category');
            if (selectedCategory === "All") { renderCatalog(allBooks); } 
            else { renderCatalog(allBooks.filter(book => book.category === selectedCategory)); }
        });

        $('#modalGoToLogin').click(function(){
            window.location.href = "login.php";
        });

        $(document).on('click', '.btn-open-qty', function(){
            if(!isLoggedIn) {
                $('#quantityModal, #cartModal').modal('hide');
                $('#loginRequiredModal').modal('show');
                return;
            }
            
            let id = $(this).data('id');
            let title = $(this).data('title');
            let price = $(this).data('price');
            let stock = $(this).data('stock');
            let action = $(this).data('action');

            $('#qtyBookTitle').text(title);
            $('#qtyBookPrice').text('₱' + parseFloat(price).toFixed(2));
            $('#qtyInput').attr('max', stock).val(1);
            $('#qtyConfirmBtn').data('id', id).data('title', title).data('price', price).data('action', action);

            $('#quantityModal').modal('show');
        });

        $('#qtyConfirmBtn').click(function(){
            let id = $(this).data('id');
            let title = $(this).data('title');
            let price = parseFloat($(this).data('price'));
            let action = $(this).data('action');
            let qty = parseInt($('#qtyInput').val());

            if(qty < 1) { alert("Quantity must be at least 1."); return; }

            let existingItem = cart.find(item => item.id === id);
            if(existingItem) {
                existingItem.qty += qty;
                existingItem.subtotal = existingItem.qty * existingItem.price;
            } else {
                cart.push({ id: id, title: title, price: price, qty: qty, subtotal: price * qty });
            }

            updateCartModal();
            $('#quantityModal').modal('hide');

            if(action === 'buy') {
                setTimeout(function(){ $('#cartModal').modal('show'); }, 400); 
            }
        });

        function updateCartModal() {
            let cartHtml = '';
            totalAmount = 0;
            $.each(cart, function(index, item){
                totalAmount += item.subtotal;
                cartHtml += `
                <li class="list-group-item" style="border-left: none; border-right: none; font-weight: 500; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        ${item.title} <span class="text-muted" style="font-size: 13px;">(Qty: ${item.qty})</span>
                    </div>
                    <div>
                        <span class="text-success" style="margin-right: 15px; font-weight: 600;">₱${item.subtotal.toFixed(2)}</span>
                        <button class="btn btn-xs btn-danger btn-remove-item" data-id="${item.id}" title="Remove Item">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </li>`;
            });
            
            if(cart.length === 0) {
                cartHtml = '<li class="list-group-item text-muted text-center" style="border: none; padding: 20px;">Your cart is empty.</li>';
            }
            
            $('#cartItems').html(cartHtml);
            $('#cartTotal').text(totalAmount.toFixed(2));
            $('#cartCount').text(cart.reduce((sum, item) => sum + item.qty, 0)); 
            $('#btnCheckout').prop('disabled', cart.length === 0);
        }

        // Remove individual item from cart
        $(document).on('click', '.btn-remove-item', function(){
            let idToRemove = $(this).data('id');
            cart = cart.filter(item => item.id !== idToRemove);
            updateCartModal();
        });

        $('#btnCheckout').click(function(){
            let btn = $(this);
            btn.html('<i class="fa fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
            
            let payload = {
                customer_id: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>,
                total: totalAmount,
                items: cart
            };

            // ---- FIX: Grab the JWT token from localStorage ----
            let token = localStorage.getItem('jwt_token');
            // ---------------------------------------------------
            
            $.ajax({
                url: '/api/gateway.php?route=checkout',
                type: 'POST',
                contentType: 'application/json',
                dataType: 'json', 
                // ---- FIX: Attach the token to the Headers ----
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                // ----------------------------------------------
                data: JSON.stringify(payload),
                success: function(response){
                    if(response.status === "success") {
                        $('#cartModal').modal('hide');
                        $('#orderSuccessMsg').text("Order placed successfully! (Order ID: " + response.order_id + ")");
                        $('#orderSuccessModal').modal('show');
                        
                        cart = [];
                        updateCartModal();
                        btn.html('Confirm Order (OTC) <i class="fa fa-check"></i>');
                    } else {
                        alert("Error: " + (response.message || "Could not process order."));
                        btn.html('Confirm Order (OTC) <i class="fa fa-check"></i>').prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Backend Error Details:", xhr.responseText);
                    alert("System Error: The backend API crashed. Press F12 and check the Console tab to see the exact PHP error.");
                    btn.html('Confirm Order (OTC) <i class="fa fa-check"></i>').prop('disabled', false);
                }
            });
        });

        // Reload the page only when the user clicks OK on the success modal
        $('#btnSuccessClose').click(function(){
            $('#orderSuccessModal').modal('hide');
            location.reload();
        });
    });
    </script>
  </body>
</html>