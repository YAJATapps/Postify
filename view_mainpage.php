<?php?>
<!doctype html>

<html>

<head>
    <title>Postify</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/mainpage.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <div id='menu_root'>
        <div id='logo_div'>
            <img src='img/logo.png'>
            <h1>Postify</h1>
        </div>

        <br><br>

        <div id='load_posts_div' class='menu_div'>
            <h3>Load posts</h3>
            <img src='img/refresh.png'>
        </div>

        <div id='modify_profile_div' class='menu_div' data-toggle='modal' data-target='#modal-profile'>
            <h3>Modify profile</h3>
            <img src='img/profile.png'>
        </div>

        <div id='user_div' class='menu_div menu_bottom'>
            <img src='img/testman.png'>
            <h3><?php echo $_SESSION['username'] ?></h3>
        </div>

        <div id='logout_div' class='menu_div menu_bottom'>
            <img src='img/logout.png'>
            <h3>Logout</h3>
        </div>
    </div>

    <div id="border">
    </div>

    <div id='content_root'>
        <div id='intro_div'>
            <h1 class='center_text'>Hi <?php echo $_SESSION['username'] ?>,</h1>
            <h2 class='center_text'>Welcome to postify website</h2>
            <h2 class='center_text'>Click on the load posts button to start</h2>
        </div>
    </div>

    <!-- Modal window to change profile -->
    <div id='modal-profile' class='modal fade'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h1 class='modal-title'>Modify profile</h2>
                </div>
                <div class='modal-body'>
                    <div class='form-group'>
                        <label class='control-label' for='modify_email'>Email:</label>
                        <input type='email' class='form-control' id='modify_email' name='modify_email'>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='modify_status'>Status:</label>
                        <input type='text' class='form-control' id='modify_status' name='modify_status'>
                    </div>
                </div>
                <div class='modal-footer'>
                    <div class='form-group'>
                        <button type='button' class='btn btn-outline-primary' data-dismiss='modal'>Cancel</button>
                        <button type='button' class='btn btn-outline-primary' id='submit_profile'>Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal window to unsubscribe or delete post -->
    <div id='modal-post-options' class='modal fade'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h1 class='modal-title'>Post options</h2>
                </div>
                <div class='modal-body'>
                    <div class='form-group'>
                        <button type='button' class='btn btn-outline-primary btn-block' data-dismiss='modal' id='post_options_button'></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invisible form -->
    <form id='logout_form' method='post' action='controller.php' style='display:none'>
        <input type='hidden' name='page' value='MainPage'>
        <input type='hidden' name='command' value='Logout'>
    </form>

</body>

</html>

<script>
    let totalPosts = 0;
    let currentFirst = 0;

    // Load event
    $(window).on('load', function() {
        window.dispatchEvent(new Event('resize'));

        let topPos = ($(window).height() - $('#intro_div').height()) / 2 + "px";
        let leftPos = ($(window).width() * 0.2) + ((($(window).width() * 0.8) - $('#intro_div').width()) / 2) + "px";
        $("#intro_div").css({
            top: topPos,
            left: leftPos,
            position: 'absolute'
        });
    });

    // Resize event
    $(window).resize(function() {
        let logoutHeight = ($('#logout_div').height());
        $("#logout_div").css({
            bottom: logoutHeight + "px"
        });

        let userHeight = ($('#user_div').height());
        $("#user_div").css({
            bottom: userHeight + logoutHeight + 20 + "px"
        });
    });

    // Load posts click listener
    $('#load_posts_div').click(function(event) {
        currentFirst = 0;

        let controller = 'controller.php';
        let query = 'page=MainPage&command=ReadUnsubscribedUsers';
        $.post(controller, query, function(data) {
            let blockedArr = JSON.parse(data);
            loadPosts(blockedArr);
        });
    });

    // Profile submit modal window button
    $('#submit_profile').click(function(event) {
        $('#modal-profile').modal('hide');
        let email = $('#modify_email').val();
        let status = $('#modify_status').val()
        let controller = 'controller.php';
        let query = 'page=MainPage&command=UpdateProfile&profile-email=' + email + '&profile-status=' + status;
        $.post(controller, query, function(data) {
            alert('Profile successfully updated!');
        });
    });


    // Logout click listener
    $('#logout_div').click(function(event) {
        $('#logout_form').submit();
    });


    // Set initial values in modify profile
    $(document).ready(function() {
        let controller = 'controller.php';
        let query = 'page=MainPage&command=FetchEmailStatus';
        $.post(controller, query, function(data) {
            let initArr = JSON.parse(data);
            $('#modify_email').val(initArr[0]);
            $('#modify_status').val(initArr[1]);
        });
    });

    function loadPosts(blockedList) {
        let controller = "controller.php";
        let query = "page=MainPage&command=ReadPosts";
        $.post(controller, query, function(data) {
            let postsArr = JSON.parse(data);
            totalPosts = getTotalPosts(postsArr, blockedList);

            // New post box
            let posts = "<div class='post_container'><h1>New post</h1>" +
                "<input type='text' id='postTitle' name='postTitle' placeholder='Write a title'><br><br>" +
                "<input type='text' id='postDescription' name='postDescription' placeholder='Description here'><br><br>" +
                "<button type='button' id='create_post_button' class='button_post'>Create a post</button>" +
                "</div>";


            let currentPagePosts = 0;

            // Fetch posts from database
            for (let i = currentFirst; i < postsArr.length; i++) {
                let postInfo = postsArr[i];

                if (!blockedList.includes(postInfo[1])) {
                    let postId = postInfo[0];
                    posts += "<div class='post_container' id='user_post' post-id=" + postId + ">" +
                        "<h1 class='post_title'>" + postInfo[2] + "</h1>" +
                        "<h1 class='post_user'>-by " + postInfo[1] + "</h1><br>" +
                        "<h3 class='post_description'>" + postInfo[3] + "</h2>" +
                        "<div class='post_button' id='like_post' post-id=" + postId + ">" +
                        "<img src='img/like_outline.png'>" +
                        "<h4>Like</h4>" +
                        "</div>" +
                        "<div class='post_button' id='write_comment' post-id=" + postId + ">" +
                        "<img src='img/comment.png'>" +
                        "<h4>Write comment</h4>" +
                        "</div>" +
                        "<div class='post_button' id='load_comments' post-id=" + postId + ">" +
                        "<img src='img/refresh.png'>" +
                        "<h4>Read comments</h4>" +
                        "</div>" +
                        "<div id='comments_box' style='display:none'>" +
                        "</div>" +
                        "</div>";

                    currentPagePosts++;
                }

                if (currentPagePosts == 20) {
                    break;
                }
            }

            posts += "<h3 style='text-align: center'>End of this page... Press next page to load next page posts</h3>";
            posts += "<h2 style='color: blue; text-align: center'; id='next_posts'>Next page</h2>";

            $('#content_root').html(posts);

            // Next page button
            $("#next_posts").click(function() {
                let currentPagePosts = totalPosts - currentFirst;
                if (Math.floor(currentPagePosts / 20) > 0 && currentPagePosts % 20 > 0) {
                    currentFirst += 20;
                    loadPosts(blockedList);
                } else {
                    alert('You have reached last page. No more posts to show.');
                }
            });

            // Set the icon if post is liked or not
            $("#like_post[post-id]").each(function() {
                let post_id = $(this).attr('post-id');
                let controller = 'controller.php';
                let query = 'page=MainPage&command=FetchLikeStatus&post-id=' + post_id;
                let like_icon = $(this).children('img');
                $.post(controller, query, function(data) {
                    if (data.includes('1'))
                        like_icon.attr('src', 'img/like.png');
                });
            });

            // Like a post
            $("#like_post[post-id]").click(function() {
                let post_id = $(this).attr('post-id');
                let controller = 'controller.php';
                let query = 'page=MainPage&command=LikePost&post-id=' + post_id;
                let like_icon = $(this).children('img');
                $.post(controller, query, function(data) {
                    if (data.includes('post_liked_error'))
                        alert('Post was already liked');
                    else {
                        like_icon.attr('src', 'img/like.png');
                        alert('Post liked');
                    }
                });
            });

            // Write a comment
            $("#write_comment[post-id]").click(function() {
                let comment = prompt("Write the comment");
                if (comment != null) {
                    let post_id = $(this).attr('post-id');
                    let controller = 'controller.php';
                    let query = 'page=MainPage&command=CreateComment&post-comment=' + comment + '&post-id=' + post_id;
                    $.post(controller, query, function(data) {
                        alert('Comment posted');
                    });
                }
            });

            // Load comments
            $("#load_comments[post-id]").click(function() {
                let controller = "controller.php";
                let post_id = $(this).attr('post-id');
                let query = 'page=MainPage&command=ReadComments&post-id=' + post_id;
                let comments_box = $(this).parent().children('#comments_box');
                $.post(controller, query, function(data) {
                    let commentsArr = JSON.parse(data);
                    let comments = '<h3>Comments</h3>';

                    // Fetch comments from database
                    for (let i = 0; i < commentsArr.length; i++) {
                        comments += "<h3><b>" + commentsArr[i][1] + "</b></h3>";
                        comments += "<h5>" + commentsArr[i][2] + "</h5>";
                    }

                    if (commentsArr.length <= 0) {
                        comments += '<h5>No comments</h5>';
                    }

                    comments_box.show();
                    comments_box.html(comments);
                });
            });

            // Double click listener for post options
            $("#user_post[post-id]").dblclick(function() {
                $('#modal-post-options').modal('show');

                let post_id = $(this).attr('post-id');

                for (let i = 0; i < postsArr.length; i++) {
                    let arrId = postsArr[i][0];
                    if (arrId == post_id) {
                        let poster_username = postsArr[i][1];
                        let current_user = '<?php echo $_SESSION['username'] ?>';

                        if (poster_username == current_user) {
                            // Option to delete own post
                            $('#post_options_button').html("Delete post");
                            $('#post_options_button').off('click').on('click', function() {
                                let controller = "controller.php";
                                let query = 'page=MainPage&command=DeletePost&post-id=' + post_id;
                                $.post(controller, query, function(data) {
                                    alert('Post deleted successfully')
                                });
                            });
                        } else {
                            // Option to unsubscribe someone's posts
                            $('#post_options_button').html("Unsubscribe");
                            $('#post_options_button').off('click').on('click', function() {
                                let controller = "controller.php";
                                let query = 'page=MainPage&command=UnsubscribeUser&post-user=' + poster_username;
                                $.post(controller, query, function(data) {
                                    if (data.includes('already_unsubscribed'))
                                        alert(poster_username + ' was already unsubscribed')
                                    else
                                        alert(poster_username + ' has been successfully unsubscribed')
                                });
                            });
                        }
                    }
                }
            });

            // Create a new post
            $("#create_post_button").click(function(event) {
                let title = $('#postTitle').val();
                let description = $('#postDescription').val()
                let controller = 'controller.php';
                let query = 'page=MainPage&command=CreatePost&post-title=' + title + '&post-description=' + description;
                $.post(controller, query, function(data) {
                    alert('Successfully posted, The posts will reload');
                    $('#load_posts_div').click();
                });
            });
        });
    }

    // Returns the total number of posts. Total posts in db - Total posts by blocked users
    function getTotalPosts(posts, blocked) {
        let count = 0;
        for (let i = 0; i < posts.length; i++) {
            if (!blocked.includes(posts[i][1]))
                count++;
        }
        return count;
    }
</script>