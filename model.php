<?php

// The database connection object
$conc = mysqli_connect('localhost', 'root', '', 'mysql');

// Fetch the posts
function fetch_posts()
{
    global $conc;

    $sql = "SELECT * FROM ProjectPosts ORDER BY Time DESC";
    $result = mysqli_query($conc, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result))
        $data[] = [$row['Id'], $row['Username'], $row['Title'], $row['Description'], $row['Time']];

    return $data;
}

// Create a post
function create_post($user, $title, $description)
{
    global $conc;

    $date_time = date('Y/m/d H:i:s');
    $sql = "INSERT INTO ProjectPosts VALUES (NULL, '$user', '$title', '$description', '$date_time')";
    return mysqli_query($conc, $sql);
}

// Verify username and password
function verify_user($username, $password)
{
    global $conc;

    $sql = "SELECT * FROM ProjectUsers WHERE Username = '$username' AND Password = '$password'";
    $result = mysqli_query($conc, $sql);
    return (mysqli_num_rows($result) > 0);
}

// Verify if user exists
function verify_user_exists($username)
{
    global $conc;

    $sql = "SELECT * FROM ProjectUsers WHERE Username = '$username'";
    $result = mysqli_query($conc, $sql);
    return (mysqli_num_rows($result) > 0);
}

// Sign Up a new user
function signup_user($username, $password, $email)
{
    global $conc;

    $date = date("Ymd");
    $sql = "INSERT INTO ProjectUsers VALUES (NULL, '$username', '$password', '$date', '$email', 'Available')";
    return mysqli_query($conc, $sql);
}

// Fetch email and status
function fetch_email_status($username)
{
    global $conc;

    $sql = "SELECT * FROM ProjectUsers WHERE Username = '$username'";
    $result = mysqli_query($conc, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row['Email'];
        $data[] = $row['Status'];
    }

    return $data;
}

// Update the profile with new email and status
function update_profile($username, $email, $status)
{
    global $conc;

    $sql = "UPDATE ProjectUsers SET Email = '$email', Status = '$status' WHERE Username = '$username'";
    mysqli_query($conc, $sql);
}

// Create a comment
function create_comment($user, $comment, $postid)
{
    global $conc;

    $sql = "INSERT INTO ProjectComments VALUES (NULL, '$user', '$comment', '$postid')";
    return mysqli_query($conc, $sql);
}

// Fetch the comments for a post
function fetch_comments($postid)
{
    global $conc;

    $sql = "SELECT * FROM ProjectComments WHERE PostId = '$postid'";
    $result = mysqli_query($conc, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result))
        $data[] = [$row['Id'], $row['Username'], $row['Comment'], $row['PostId']];

    return $data;
}

// Like a post
function like_post($user, $postid)
{
    // Send error if the post is already liked
    if (verify_post_liked($user, $postid)) {
        return 'post_liked_error';
    } else {
        global $conc;

        $sql = "INSERT INTO ProjectLikes VALUES (NULL, '$user', '$postid')";
        return mysqli_query($conc, $sql);
    }
}

// Verify if the post is liked
function verify_post_liked($user, $postid)
{
    global $conc;

    $sql = "SELECT * FROM ProjectLikes WHERE Username = '$user' AND PostId = '$postid'";
    $result = mysqli_query($conc, $sql);
    return (mysqli_num_rows($result) > 0);
}

// Unsubscribe a user
function unsubscribe_user($user, $block)
{
    if (verify_unsubscribe($user, $block)) {
        return 'already_unsubscribed';
    } else {
        global $conc;

        $sql = "INSERT INTO ProjectBlockList VALUES (NULL, '$user', '$block')";
        return mysqli_query($conc, $sql);
    }
}

// Verify if a user is unsubscribed by some other user
function verify_unsubscribe($user, $block)
{
    global $conc;

    $sql = "SELECT * FROM ProjectBlockList WHERE Username = '$user' AND Blockname = '$block'";
    $result = mysqli_query($conc, $sql);
    return (mysqli_num_rows($result) > 0);
}

// Delete a post
function delete_post($id)
{
    global $conc;

    $sql = "DELETE FROM ProjectPosts WHERE Id='$id'";
    mysqli_query($conc, $sql);
}

// Fetch the users unsubscribed by a user
function fetch_unsubscribed_users($user)
{
    global $conc;

    $sql = "SELECT * FROM ProjectBlockList WHERE Username = '$user'";
    $result = mysqli_query($conc, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row['Blockname'];
    }

    return $data;
}

?>