<?php

if (!isset($_POST['page'])) { 
    include('view_startpage.php');
    exit();
}

session_start();
require('model.php');


if ($_POST['page'] == 'StartPage') { 

    if($_POST['command'] == 'Login') {

        if (verify_user($_POST['login_username'], $_POST['login_password'])) {

            $_SESSION['username'] = $_POST['login_username'];
            include('view_mainpage.php');

        } else {

            $errorCode = 'wrong_credentials';
            include('view_startpage.php');

        }

        exit();

    } else if($_POST['command'] == 'Signup') { 

        if (verify_user_exists($_POST['signup_username'])) {
            echo 'signup_exists';
        } else {
            if (signup_user($_POST['signup_username'], $_POST['signup_password'], $_POST['signup_email'])) {
                echo 'signup_success';
            }
            else {
                echo 'signup_error';
            }
        }
        exit();

    }

}

if ($_POST['page'] == 'MainPage') { 

    if($_POST['command'] == 'ReadPosts') {

        echo json_encode(fetch_posts());
        exit();

    } else if($_POST['command'] == 'CreatePost') { 

        create_post($_SESSION['username'], $_POST['post-title'], $_POST['post-description']);
        exit();
    
    } else if($_POST['command'] == 'Logout') {

        session_unset();
        session_destroy();
        include('view_startpage.php');
        exit();

    } else if($_POST['command'] == 'FetchEmailStatus') {

        echo json_encode(fetch_email_status($_SESSION['username']));
        exit();

    } else if ($_POST['command'] == 'UpdateProfile') {
        
        update_profile($_SESSION['username'], $_POST['profile-email'], $_POST['profile-status']);
        exit();

    } else if($_POST['command'] == 'CreateComment') { 

        create_comment($_SESSION['username'], $_POST['post-comment'], $_POST['post-id']);
        exit();
    
    } else if($_POST['command'] == 'ReadComments') {

        echo json_encode(fetch_comments($_POST['post-id']));
        exit();

    } else if($_POST['command'] == 'LikePost') {
    
        echo like_post($_SESSION['username'], $_POST['post-id']);
        exit();
    
    } else if($_POST['command'] == 'FetchLikeStatus') {
    
        echo verify_post_liked($_SESSION['username'], $_POST['post-id']);
        exit();
    
    } else if($_POST['command'] == 'UnsubscribeUser') {
    
        echo unsubscribe_user($_SESSION['username'], $_POST['post-user']);
        exit();
    
    } else if($_POST['command'] == 'DeletePost') {
    
        delete_post($_POST['post-id']);
        exit();
    
    } else if($_POST['command'] == 'ReadUnsubscribedUsers') {

        echo json_encode(fetch_unsubscribed_users($_SESSION['username']));
        exit();

    }
 
}

?>