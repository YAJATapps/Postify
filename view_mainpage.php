<?php?>
<!doctype html>

<html>

<head>
    <title>Postify</title>

    <link rel="icon" href="img/logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet" href="css/mainpage.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
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

        <div id='modify_profile_div' class='menu_div' data-bs-toggle='modal' data-bs-target='#modal-profile'>
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
                        <button type='button' class='btn btn-outline-primary' id='cancel_profile' data-bs-dismiss='modal'>Cancel</button>
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
                        <button type='button' class='btn btn-outline-primary btn-block' data-bs-dismiss='modal' id='post_options_button'></button>
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
    window.onload = function() {
        window.dispatchEvent(new Event('resize'));

        let browserWidth = window.innerWidth;
        let browserHeight = window.innerHeight;
        let topPos = (browserHeight - document.getElementById('intro_div').offsetHeight) / 2 + "px";
        let leftPos = (browserWidth * 0.2) + ((browserWidth * 0.8 - document.getElementById('intro_div').offsetWidth) / 2) + "px";
        document.getElementById('intro_div').style.position = 'absolute';
        document.getElementById('intro_div').style.top = topPos;
        document.getElementById('intro_div').style.left = leftPos;
    };

    // Resize event
    window.onresize = function() {
        // Bottom margin on left menu
        let bottomOffset = 30;

        // Bottom margin on logout button
        let logoutHeight = (document.getElementById('logout_div').offsetHeight) + bottomOffset;
        document.getElementById('logout_div').style.bottom = logoutHeight + "px";

        // Bottom margin on user button
        let userHeight = document.getElementById('user_div').offsetHeight;
        document.getElementById('user_div').style.bottom = userHeight + logoutHeight + 20 + "px";
    };

    // Load posts click listener
    document.getElementById('load_posts_div').onclick = function(event) {
        currentFirst = 0;

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                let blockedArr = JSON.parse(this.responseText);
                loadPosts(blockedArr);
            }
        };

        let controller = 'controller.php';
        let query = 'page=MainPage&command=ReadUnsubscribedUsers';
        xhttp.open("post", controller);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(query);
    };

    // Profile submit modal window button
    document.getElementById('submit_profile').onclick = function(event) {
        document.getElementById('cancel_profile').click();

        let email = document.getElementById('modify_email').value;
        let status = document.getElementById('modify_status').value;

        var xhttp = new XMLHttpRequest();
        let controller = 'controller.php';
        let query = 'page=MainPage&command=UpdateProfile&profile-email=' + email + '&profile-status=' + status;
        xhttp.open("post", controller);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(query);
    };


    // Logout click listener
    document.getElementById('logout_div').onclick = function(event) {
        document.getElementById('logout_form').submit();
    };


    // Set initial values in modify profile
    document.addEventListener("DOMContentLoaded", function() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                let initArr = JSON.parse(this.responseText);
                document.getElementById('modify_email').value = initArr[0];
                document.getElementById('modify_status').value = initArr[1];
            }
        };

        let controller = 'controller.php';
        let query = 'page=MainPage&command=FetchEmailStatus';
        xhttp.open("post", controller);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(query);
    });

    function loadPosts(blockedList) {
        let controller = "controller.php";
        let query = "page=MainPage&command=ReadPosts";

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                let postsArr = JSON.parse(this.responseText);
                totalPosts = getTotalPosts(postsArr, blockedList);

                // New post box
                let posts = "<div class='post_container'><h1>New post</h1>" +
                    "<input type='text' id='postTitle' name='postTitle' placeholder='Write a title'><br><br>" +
                    "<textarea id='postDescription' name='postDescription' placeholder='Description here' rows='4' style='resize: none'></textarea><br><br>" +
                    "<button type='button' id='create_post_button' class='button_post'>Create a post</button>" +
                    "</div>";


                let currentPagePosts = 0;

                // Fetch posts from database
                for (let i = currentFirst; i < postsArr.length; i++) {
                    let postInfo = postsArr[i];

                    // Post box
                    if (!blockedList.includes(postInfo[1])) {
                        let postId = postInfo[0];
                        posts += "<div class='post_container' id='user_post' post-id=" + postId + ">" +
                            "<h1 class='post_title'>" + postInfo[2] + "</h1>" +
                            "<h1 class='post_user'>-by " + postInfo[1] + "</h1><br>" +
                            "<p class='post_description' style='white-space: pre-wrap; font-size: 1.5em'>" + postInfo[3] + "</p>" +
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

                // Next page button
                posts += "<h3 style='text-align: center'>End of this page... Press next page to load next page posts</h3>";
                posts += "<h2 style='color: #4285F4; text-align: center' id='next_posts'>Next page</h2><br>";

                // Replace intro div with posts
                var postDiv = document.createElement('div');
                postDiv.id = 'content_root';
                postDiv.innerHTML = posts;
                document.getElementById('content_root').replaceWith(postDiv);

                // Next page button
                document.getElementById('next_posts').onclick = function() {
                    let currentPagePosts = totalPosts - currentFirst;
                    if (Math.floor(currentPagePosts / 20) > 0 && currentPagePosts % 20 > 0) {
                        currentFirst += 20;
                        loadPosts(blockedList);
                    } else {
                        alert('You have reached last page. No more posts to show.');
                    }
                };

                // Set the icon if post is liked or not
                let likedPosts = document.querySelectorAll('#like_post');
                likedPosts.forEach(function(button) {
                    let post_id = button.getAttribute('post-id');
                    let controller = 'controller.php';
                    let query = 'page=MainPage&command=FetchLikeStatus&post-id=' + post_id;
                    let like_icon = button.querySelector('img');

                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            if (this.responseText.includes('1'))
                                like_icon.setAttribute('src', 'img/like.png');
                        }
                    };
                    xhttp.open("post", controller);
                    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhttp.send(query);
                });

                // Like a post
                let likeButtons = document.querySelectorAll('#like_post');
                likeButtons.forEach(function(button) {
                    button.onclick = function() {
                        let post_id = button.getAttribute('post-id');
                        let controller = 'controller.php';
                        let query = 'page=MainPage&command=LikePost&post-id=' + post_id;
                        let like_icon = button.querySelector('img');

                        var xhttp = new XMLHttpRequest();
                        xhttp.onreadystatechange = function() {
                            if (this.readyState == 4 && this.status == 200) {
                                if (this.responseText.includes('post_liked_error'))
                                    alert('Post was already liked');
                                else {
                                    like_icon.setAttribute('src', 'img/like.png');
                                }
                            }
                        };
                        xhttp.open("post", controller);
                        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        xhttp.send(query);
                    };
                });

                // Write a comment
                let commentButtons = document.querySelectorAll('#write_comment');
                commentButtons.forEach(function(button) {
                    button.onclick = function() {
                        let comment = prompt("Write the comment");
                        if (comment != null) {
                            let post_id = button.getAttribute('post-id');
                            let controller = 'controller.php';
                            let query = 'page=MainPage&command=CreateComment&post-comment=' + comment + '&post-id=' + post_id;

                            var xhttp = new XMLHttpRequest();
                            xhttp.open("post", controller);
                            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                            xhttp.send(query);
                        }
                    };
                });

                // Load comments
                let loadButtons = document.querySelectorAll('#load_comments');
                loadButtons.forEach(function(button) {
                    button.onclick = function() {
                        let controller = "controller.php";
                        let post_id = button.getAttribute('post-id');
                        let query = 'page=MainPage&command=ReadComments&post-id=' + post_id;
                        let comments_box = button.parentNode.querySelector('#comments_box');

                        var xhttp = new XMLHttpRequest();
                        xhttp.onreadystatechange = function() {
                            if (this.readyState == 4 && this.status == 200) {
                                let commentsArr = JSON.parse(this.responseText);
                                let comments = '<h3>Comments</h3>';

                                // Fetch comments from database
                                for (let i = 0; i < commentsArr.length; i++) {
                                    comments += "<h3><b>" + commentsArr[i][1] + "</b></h3>";
                                    comments += "<h5>" + commentsArr[i][2] + "</h5>";
                                }

                                if (commentsArr.length <= 0) {
                                    comments += '<h5>No comments</h5>';
                                }

                                // Make comments box visible
                                comments_box.style.display = 'block';

                                // Add comments into comments box
                                var commentsDiv = document.createElement('div');
                                commentsDiv.id = 'comments_box';
                                commentsDiv.innerHTML = comments;
                                comments_box.replaceWith(commentsDiv);
                            }
                        };
                        xhttp.open("post", controller);
                        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        xhttp.send(query);
                    };
                });

                // Double click listener for post options
                let postOptions = document.querySelectorAll('#user_post');
                postOptions.forEach(function(postOption) {
                    postOption.ondblclick = function() {
                        var modalPost = new bootstrap.Modal(document.getElementById('modal-post-options'));
                        modalPost.show();

                        let post_id = postOption.getAttribute('post-id');

                        for (let i = 0; i < postsArr.length; i++) {
                            let arrId = postsArr[i][0];
                            if (arrId == post_id) {
                                let poster_username = postsArr[i][1];
                                let current_user = '<?php echo $_SESSION['username'] ?>';
                                let postButton = document.getElementById('post_options_button');

                                if (poster_username == current_user) {
                                    // Option to delete own post
                                    postButton.innerHTML = "Delete post";
                                    postButton.removeEventListener('click', deletePost);
                                    postButton.addEventListener('click', function() {
                                        deletePost(post_id);
                                    });
                                } else {
                                    // Option to unsubscribe someone's posts
                                    postButton.innerHTML = "Unsubscribe";
                                    postButton.removeEventListener('click', unsubscribeUser);
                                    postButton.addEventListener('click', function() {
                                        unsubscribeUser(poster_username);
                                    });
                                }
                            }
                        }
                    };
                });

                // Create a new post
                document.getElementById('create_post_button').onclick = function(event) {
                    let title = document.getElementById("postTitle").value;;
                    let description = document.getElementById("postDescription").value;

                    if (!title) {
                        alert('Post title cannot be empty');
                    } else if (!description) {
                        alert('Post description cannot be empty');
                    } else {
                        var xhttp = new XMLHttpRequest();
                        xhttp.onreadystatechange = function() {
                            if (this.readyState == 4 && this.status == 200) {
                                // Successfully posted, The posts will reload
                                document.getElementById('load_posts_div').click();
                            }
                        };

                        let controller = 'controller.php';
                        let query = 'page=MainPage&command=CreatePost&post-title=' + title + '&post-description=' + description;
                        xhttp.open("post", controller);
                        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        xhttp.send(query);
                    }

                };
            }
        };
        xhttp.open("post", controller);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(query);
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

    // Delete the post
    function deletePost(id) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('load_posts_div').click();
            }
        };

        let controller = "controller.php";
        let query = 'page=MainPage&command=DeletePost&post-id=' + id;
        xhttp.open("post", controller);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(query);
    }

    // Unsubscribe the user
    function unsubscribeUser(name) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if (this.responseText.includes('already_unsubscribed'))
                    alert(name + ' was already unsubscribed');
                else {
                    alert(name + ' has been successfully unsubscribed');
                    document.getElementById('load_posts_div').click();
                }
            }
        };

        let controller = "controller.php";
        let query = 'page=MainPage&command=UnsubscribeUser&post-user=' + name;
        xhttp.open("post", controller);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(query);
    }
</script>