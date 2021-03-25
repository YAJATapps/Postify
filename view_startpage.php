<!doctype html>

<html>

<head>
    <title>Postify</title>
    
    <link rel="icon" href="img/logo.png">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/startpage.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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
                <button type='button' id='signup_button' class='button_post' data-toggle='modal' data-target='#modal-signup'>Sign Up</button>
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
                        <button type='button' class='btn btn-outline-primary' data-dismiss='modal'>Cancel</button>
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
    $(window).on('load', function() {
        let error = '<?php if (isset($errorCode)) echo $errorCode; ?>';
        if (error.includes('wrong_credentials'))
            alert("Wrong credentials, Try again");
    });

    // Submit signup button click listener
    $('#submit_signup').click(function(event) {
        $('#modal-signup').modal('hide');

        let controller = 'controller.php';
        let query = 'page=StartPage&command=Signup&signup_username=' + $('#signup_username').val() + '&signup_password=' + $('#signup_password').val() + '&signup_email=' + $('#signup_email').val();
        $.post(controller, query, function(data) {
            if (data.includes('signup_exists')) {
                alert('Username already exists, Try again');
            } else if (data.includes('signup_error')) {
                alert('Error in signup, Try again');
            } else if (data.includes('signup_success')) {
                alert('Sign Up successful');
            }
        })
    });
</script>