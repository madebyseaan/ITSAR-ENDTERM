<?php
session_start();
$role = $_SESSION['role_name'] ?? '';
// Kick out anyone who isn't a Cashier or Supervisor
if ($role !== 'Cashier' && $role !== 'Supervisor') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>POS Register | BookHive</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; margin: 0; }
        .sidebar { height: 100vh; background: #34495e; color: white; position: fixed; width: 240px; padding-top: 20px; z-index: 100;}
        .sidebar h3 { text-align: center; color: #fff; margin-bottom: 30px; font-weight: 600; }
        .sidebar .nav>li>a { color: #bdc3c7; padding: 15px 25px; font-weight: 500; transition: 0.3s; }
        .sidebar .nav>li>a:hover, .sidebar .nav>li.active>a { background: #2c3e50; color: #fff; border-left: 4px solid #3498db; }
        
        .main-content { margin-left: 240px; padding: 30px; height: 100vh; display: flex; flex-direction: column; }
        .card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        .pos-grid { height: calc(100vh - 180px); overflow-y: auto; padding-right: 10px; }
        .receipt-panel { height: calc(100vh - 110px); display: flex; flex-direction: column; }
        .receipt-items { flex-grow: 1; overflow-y: auto; border-bottom: 2px dashed #eee; margin-bottom: 15px; padding-bottom: 10px;}
        
        .book-item { border: 1px solid #eee; border-radius: 8px; padding: 15px; text-align: center; cursor: pointer; transition: 0.2s; background: white; margin-bottom: 15px;}
        .book-item:hover { border-color: #3498db; box-shadow: 0 4px 10px rgba(52,152,219,0.2); transform: translateY(-3px);}
        .book-item h5 { font-weight: 600; font-size: 14px; height: 35px; overflow: hidden; margin-top: 10px;}
        .book-item .price { color: #2ecc71; font-weight: 600; font-size: 18px; }
        
        /* Updated receipt line for the remove button */
        .receipt-line { display: flex; justify-content: space-between; align-items: center; font-size: 14px; margin-bottom: 10px; border-bottom: 1px solid #f9f9f9; padding-bottom: 5px;}
        .btn-remove { color: #e74c3c; cursor: pointer; border: none; background: transparent; padding: 0 5px; font-size: 16px; transition: 0.2s;}
        .btn-remove:hover { color: #c0392b; transform: scale(1.1); }
        
        .btn-checkout { background: #3498db; color: white; border: none; padding: 15px; border-radius: 8px; font-weight: 600; font-size: 18px; width: 100%; transition: 0.3s;}
        .btn-checkout:hover { background: #2980b9; }
        .modal-content { border-radius: 12px; border: none; overflow: hidden; }
    </style>
</head>
<body>
    <?php include 'includes/staff_sidebar.php'; ?>

    <div class="main-content">
        <div class="row" style="flex-grow: 1;">
            <div class="col-md-8">
                <div class="card" style="margin-bottom: 15px; padding: 15px;">
                    <div class="input-group">
                        <span class="input-group-addon" style="background:transparent; border-right:none;"><i class="fa fa-search text-muted"></i></span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Search book title or author..." style="border-left:none; box-shadow:none;">
                    </div>
                </div>
                <div class="pos-grid">
                    <div class="row" id="productGrid">
                        <div class="col-md-12 text-center text-muted" style="margin-top: 50px;">
                            <i class="fa fa-spinner fa-spin fa-2x"></i><br>Loading Inventory...
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card receipt-panel">
                    <h3 style="margin-top:0; font-weight:600; border-bottom: 2px solid #eee; padding-bottom: 10px;">
                        <i class="fa fa-shopping-cart text-primary"></i> Current Sale
                    </h3>
                    <div class="receipt-items" id="receiptItems">
                        <p class="text-muted text-center" style="margin-top: 50px;">Scan or select items to add them to the transaction.</p>
                    </div>
                    <div class="receipt-totals">
                        <div style="display:flex; justify-content: space-between; font-size: 24px; font-weight: 600; color: #2c3e50; margin-bottom: 15px;">
                            <span>Total:</span>
                            <span style="color: #2ecc71;">₱<span id="cartTotal">0.00</span></span>
                        </div>
                        <button class="btn-checkout" id="btnCharge" disabled>
                            Charge ₱<span id="btnTotal">0.00</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="successModal" tabindex="-1">
      <div class="modal-dialog modal-sm">
        <div class="modal-content text-center" style="padding: 20px;">
          <div style="font-size: 50px; color: #2ecc71; margin-bottom: 10px;">
            <i class="fa fa-check-circle"></i>
          </div>
          <h3 style="margin-top:0;">Success!</h3>
          <p id="successMsg" style="font-size: 16px; color: #7f8c8d;"></p>
          <button type="button" class="btn btn-success btn-block" data-dismiss="modal" style="margin-top: 15px;">OK</button>
        </div>
      </div>
    </div>

    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function(){
        let allBooks = [];
        let posCart = [];
        let totalAmount = 0.00;

        $.get("/api/gateway.php?route=books", function(response){
            if(response.status === "success") {
                allBooks = response.data;
                renderGrid(allBooks);
            }
        });

        function renderGrid(books) {
            let html = '';
            $.each(books, function(i, book){
                if(book.stock_quantity > 0) {
                    html += `
                    <div class="col-md-4 col-sm-6">
                        <div class="book-item" data-id="${book.id}" data-title="${book.title}" data-price="${book.price}">
                            <i class="fa fa-book fa-3x text-muted" style="color:#bdc3c7;"></i>
                            <h5>${book.title}</h5>
                            <div class="price">₱${parseFloat(book.price).toFixed(2)}</div>
                            <small class="text-muted">Stock: ${book.stock_quantity}</small>
                        </div>
                    </div>`;
                }
            });
            if(html === '') html = '<div class="col-md-12 text-center text-muted">No books found.</div>';
            $('#productGrid').html(html);
        }

        $('#searchInput').on('keyup', function(){
            let term = $(this).val().toLowerCase();
            let filtered = allBooks.filter(b => b.title.toLowerCase().includes(term) || (b.author && b.author.toLowerCase().includes(term)));
            renderGrid(filtered);
        });

        $(document).on('click', '.book-item', function(){
            posCart.push({ id: $(this).data('id'), title: $(this).data('title'), price: parseFloat($(this).data('price')), qty: 1 });
            updateReceipt();
        });

        // --- NEW LOGIC: Remove individual items from the receipt ---
        $(document).on('click', '.btn-remove', function(e){
            e.stopPropagation(); // Prevents triggering other clicks
            let indexToRemove = $(this).data('index');
            posCart.splice(indexToRemove, 1); // Removes exactly that 1 item from the array
            updateReceipt();
        });
        // -----------------------------------------------------------

        function updateReceipt() {
            totalAmount = 0;
            let html = '';
            $.each(posCart, function(i, item){
                totalAmount += item.price;
                html += `
                <div class="receipt-line">
                    <span style="width:60%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${item.title}</span>
                    <span style="font-weight:600;">₱${item.price.toFixed(2)}</span>
                    <button class="btn-remove" data-index="${i}" title="Remove Item"><i class="fa fa-times"></i></button>
                </div>`;
            });
            if(posCart.length === 0) html = '<p class="text-muted text-center" style="margin-top: 50px;">No items selected.</p>';
            $('#receiptItems').html(html);
            $('#cartTotal').text(totalAmount.toFixed(2));
            $('#btnTotal').text(totalAmount.toFixed(2));
            $('#btnCharge').prop('disabled', posCart.length === 0);
            document.getElementById('receiptItems').scrollTop = document.getElementById('receiptItems').scrollHeight;
        }

        $('#btnCharge').click(function(){
            let btn = $(this);
            btn.html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            
            // --- FIX: Grab the JWT token from localStorage ---
            let token = localStorage.getItem('jwt_token');
            // -------------------------------------------------

            $.ajax({
                url: '/api/gateway.php?route=checkout',
                type: 'POST',
                contentType: 'application/json',
                // --- FIX: Attach the token to the Headers ---
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                // --------------------------------------------
                data: JSON.stringify({ customer_id: <?php echo $_SESSION['user_id']; ?>, total: totalAmount, items: posCart }),
                success: function(response){
                    if(response.status === "success") {
                        $('#successMsg').text("Transaction Successful! Order ID: " + response.order_id);
                        $('#successModal').modal('show');
                        posCart = [];
                        updateReceipt();
                        btn.html('Charge ₱<span id="btnTotal">0.00</span>').prop('disabled', true);
                        
                        // Refresh stock count silently in the background
                        $.get("/api/gateway.php?route=books", function(res){
                            if(res.status === "success") { allBooks = res.data; renderGrid(allBooks); }
                        });
                    } else {
                        alert("Error processing transaction: " + (response.message || "Unknown error"));
                        btn.html('Charge ₱<span id="btnTotal">'+totalAmount.toFixed(2)+'</span>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Backend Error Details:", xhr.responseText);
                    alert("System Error: The backend API blocked the request or crashed. Check the console.");
                    btn.html('Charge ₱<span id="btnTotal">'+totalAmount.toFixed(2)+'</span>').prop('disabled', false);
                }
            });
        });
    });
    </script>
</body>
</html>