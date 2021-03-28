<!doctype html>

<html>

<head>
    <title>Postify</title>

    <link rel="icon" href="img/logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet" href="css/startpage.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
</head>

<body>
    <div id='content_left'>
        <div id='intro-container'>
            <h1>Postify</h1>
            <h2>Connect with people around Postify.</h2>
        </div>
    </div>
    <div id='content_right'>
        <div id='login-container'>
            <form method='post' action='controller.php'>
                <input type='hidden' name='page' value='StartPage'>
                <input type='hidden' name='command' value='Login'>
                <input type='text' id='login_username' name='login_username' placeholder='Username'><br><br>
                <input type='password' id='login_password' name='login_password' placeholder='Password'><br><br>
                <button type='submit' id='login_button' class='button_post'>Login</button><br><br>
                <hr>
                <h5>Don't have an account yet?</h5>
                <button type='button' id='signup_button' class='button_post' data-bs-toggle='modal' data-bs-target='#modal-signup'>Sign Up</button>
            </form>
        </div>
    </div>

    <div id='content_bottom'>
        <img src='img/logo.png'>
        <h1>Postify</h1>
    </div>

    <!-- Modal window for signup -->
    <div id='modal-signup' class='modal fade'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h1 class='modal-title'>Sign Up</h2>
                </div>
                <div class='modal-body'>
                    <div class='form-group'>
                        <label class='control-label' for='signup_username'>Username:</label>
                        <input type='text' class='form-control' id='signup_username' name='signup_username'>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='signup_password'>Password:</label>
                        <input type='password' class='form-control' id='signup_password' name='signup_password'>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='signup_email'>Email:</label>
                        <input type='email' class='form-control' id='signup_email' name='signup_email'>
                    </div>
                </div>
                <div class='modal-footer'>
                    <div class='form-group'>
                        <button type='button' class='btn btn-outline-primary' id='dismiss_signup' data-bs-dismiss='modal'>Cancel</button>
                        <button type='button' class='btn btn-outline-primary' id='submit_signup'>Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<script>
    // Load event
    window.onload = function() {
        let error = '<?php if (isset($errorCode)) echo $errorCode; ?>';
        if (error.includes('wrong_credentials'))
            alert("Wrong credentials, Try again");
    };

    // Submit signup button click listener
    document.getElementById('submit_signup').onclick = function(event) {
        document.getElementById('dismiss_signup').click();

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                let data = this.responseText;
                if (data.includes('signup_exists')) {
                    alert('Username already exists, Try again');
                } else if (data.includes('signup_error')) {
                    alert('Error in signup, Try again');
                } else if (data.includes('signup_success')) {
                    alert('Sign Up successful');
                }
            }
        };  
        let controller = 'controller.php';
        let query = 'page=StartPage&command=Signup&signup_username=' + document.getElementById("signup_username").value + '&signup_password=' + document.getElementById("signup_password").value + '&signup_email=' + document.getElementById("signup_email").value;
        xhttp.open("post", controller);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(query);
    };
</script>