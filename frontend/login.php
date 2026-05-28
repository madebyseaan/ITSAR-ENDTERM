<?php
session_start();
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>BookHive | Secure Login</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        /* 1. Bulletproof Centering */
        html, body { 
            height: 100%; 
            width: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif; 
            background-color: #f4f7f6; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }
        
        .auth-wrapper {
            width: 100%;
            max-width: 420px; /* Limits the width so it doesn't stretch */
            padding: 15px; /* Keeps it off the edges on mobile */
        }

        .auth-card { 
            background: white; 
            padding: 40px 30px; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.08); 
            text-align: center;
        }

        .brand-logo { 
            margin-top: 0;
            margin-bottom: 30px; 
            color: #2c3e50; 
            font-weight: 600;
        }
        
        /* 2. Strict Form & Input Alignment */
        form { 
            text-align: left; 
            width: 100%;
        }
        
        .form-group {
            margin-bottom: 20px;
            width: 100%;
        }
        
        label {
            display: block; 
            font-weight: 500; 
            color: #2c3e50;
            margin-bottom: 8px;
        }
        
        .form-control {
            display: block;
            width: 100% !important; /* Forces inputs to span the exact width */
            box-sizing: border-box; 
            border-radius: 8px;
            padding: 12px 15px;
            height: auto;
            box-shadow: none;
            border: 1px solid #dfe6e9;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
        }
        
        .form-control:focus { border-color: #3498db; box-shadow: 0 0 0 2px rgba(52,152,219,0.2); }
        
        .btn-modern {
            display: block;
            width: 100%;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            background-color: #3498db;
            border: none;
            color: white;
            transition: all 0.3s ease;
            margin-top: 25px;
        }
        
        .btn-modern:hover { background-color: #2980b9; box-shadow: 0 4px 10px rgba(52,152,219,0.3); color: white;}
        
        /* 3. Clean Tabs */
        .nav-tabs { border-bottom: 2px solid #ecf0f1; margin-bottom: 25px;}
        .nav-tabs > li { margin-bottom: -2px; }
        .nav-tabs > li > a { border: none; color: #7f8c8d; font-weight: 500; }
        .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover { border: none; border-bottom: 2px solid #3498db; color: #2c3e50; background: transparent;}
    </style>
</head>
<body>

    <div class="auth-wrapper">
        <div class="auth-card">
            <h2 class="brand-logo"><i class="fa fa-book text-primary"></i> BookHive</h2>
            
            <ul class="nav nav-tabs nav-justified" role="tablist">
                <li role="presentation" class="active"><a href="#login" role="tab" data-toggle="tab">Sign In</a></li>
                <li role="presentation"><a href="#register" role="tab" data-toggle="tab">Sign Up</a></li>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="login">
                    <p class="text-muted text-center" style="font-size: 13px; margin-bottom:20px;">Access your portal securely.</p>
                    <div class="alert alert-danger" id="errorMsg" style="display:none; border-radius: 8px; padding: 10px; text-align: left;"></div>
                    
                    <form id="loginForm">
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control" id="loginEmail" placeholder="name@example.com" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" id="password" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-modern" id="btnSignIn">Sign In <i class="fa fa-arrow-right"></i></button>
                    </form>
                </div>

                <div role="tabpanel" class="tab-pane" id="register">
                    <p class="text-muted text-center" style="font-size: 13px; margin-bottom:20px;">Join BookHive today.</p>
                    
                    <div class="alert alert-danger" id="regError" style="display:none; border-radius: 8px; padding: 10px; text-align: left;"></div>
                    <div class="alert alert-success" id="regSuccess" style="display:none; border-radius: 8px; padding: 10px; text-align: left;"></div>
                    
                    <form id="registerForm">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" class="form-control" id="regName" placeholder="e.g. John Doe" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control" id="regEmail" placeholder="name@example.com" required>
                        </div>
                        <div class="form-group">
                            <label>Create Password</label>
                            <input type="password" class="form-control" id="regPass" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-modern" style="background-color: #2ecc71;" id="btnRegister">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
$(document).ready(function(){
    
    // 1. Login Logic
    $('#loginForm').submit(function(e){
        e.preventDefault(); 
        let btn = $('#btnSignIn');
        btn.html('<i class="fa fa-spinner fa-spin"></i> Authenticating...');
        $('#errorMsg').hide();

        let user = $('#loginEmail').val();
        let pass = $('#password').val();

        // Use the API Gateway route
        $.post("/api/gateway.php?route=login", { username: user, password: pass }, function(response){
            if(response.status === "success") {
                btn.html('<i class="fa fa-check"></i> Success');
                btn.css('background-color', '#2ecc71');
                
                // ---- ADDED THIS BLOCK TO SAVE THE JWT TOKEN ----
                if(response.token) {
                    localStorage.setItem('jwt_token', response.token);
                }
                // ------------------------------------------------
                
                // Synchronize the session locally with the frontend container
                $.post("set_session.php", { 
                    user_id: response.user_id,
                    role_name: response.role,
                    token: response.token
                }, function() {
                    setTimeout(function(){
                        let role = response.role;
                        // RBAC Direct Routing
                        if(role === "Admin") {
                            window.location.href = "admin.php";
                        } else if (role === "Cashier") {
                            window.location.href = "staff_pos.php";
                        } else if (role === "Fulfillment") {
                            window.location.href = "staff_orders.php";
                        } else if (role === "Stock_Clerk") {
                            window.location.href = "staff_inventory.php";
                        } else if (role === "Supervisor") {
                            window.location.href = "staff.php";
                        } else {
                            window.location.href = "shop.php";
                        }
                    }, 500);
                });
            } else {
                $('#errorMsg').text(response.message || "Invalid email or password.").fadeIn();
                btn.html('Sign In <i class="fa fa-arrow-right"></i>');
                btn.css('background-color', '#3498db');
            }
        }, "json").fail(function() {
            $('#errorMsg').text("Error connecting to API Gateway.").fadeIn();
            btn.html('Sign In <i class="fa fa-arrow-right"></i>');
        });
    });

    // 2. Registration Logic
    $('#registerForm').submit(function(e){
        e.preventDefault(); 
        let btn = $('#btnRegister');
        btn.html('<i class="fa fa-spinner fa-spin"></i> Creating...');
        $('#regError, #regSuccess').hide();

        let name = $('#regName').val();
        let email = $('#regEmail').val();
        let pass = $('#regPass').val();

        // Route through Gateway
        $.post("/api/gateway.php?route=register", { fullname: name, email: email, password: pass }, function(response){
            if(response.status === "success") {
                $('#regSuccess').text(response.message + " You can now sign in.").fadeIn();
                $('#registerForm')[0].reset(); 
                btn.html('Create Account');
            } else {
                $('#regError').text(response.message).fadeIn();
                btn.html('Create Account');
            }
        }, "json").fail(function() {
            $('#regError').text("Error connecting to API Gateway.").fadeIn();
            btn.html('Create Account');
        });
    });
});
</script>
</body>
</html>l>