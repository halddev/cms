<?php include("delete_post_modal.php"); ?>

<!--BULK OPTIONS-->

<?php
//Loop through checkBoxArray - array with post_id's of all checked posts:
if (isset($_POST['checkBoxArray'])) {

    if (isset($_SESSION['user_role'])) {

        if ($_SESSION['user_role'] == 'admin') {

            //Save value (id) of each checked post in variable, $postValueId:
            foreach ($_POST['checkBoxArray'] as $postValueId) {
                escape($bulk_options = $_POST['bulk_options']);

                switch ($bulk_options) {

                    case 'published':

                        $query = "UPDATE posts SET post_status = '{$bulk_options}' WHERE post_id = $postValueId";
                        $update_to_published_query = mysqli_query($connection, $query);
                        confirmQuery($update_to_published_query);

                        break;


                    case 'draft':
                        $query = "UPDATE posts SET post_status = '{$bulk_options}' WHERE post_id = $postValueId";
                        $update_to_draft_query = mysqli_query($connection, $query);
                        confirmQuery($update_to_draft_query);
                        break;


                    case 'delete':
                        $query = "DELETE FROM posts WHERE post_id = {$postValueId}";
                        $delete_query = mysqli_query($connection, $query);
                        confirmQuery($delete_query);
                        break;


                    case 'clone':
                        $query = "SELECT * FROM posts WHERE post_id = {$postValueId} ";
                        $select_post_query = mysqli_query($connection, $query);

                        while ($row = mysqli_fetch_array($select_post_query)) {
                            $post_title         = $row['post_title'];
                            $post_category_id   = $row['post_category_id'];
                            $post_date          = $row['post_date'];
                            $post_user          = $row['post_user'];
                            $post_status        = $row['post_status'];
                            $post_image         = $row['post_image'];
                            $post_tags          = $row['post_tags'];
                            $post_content       = $row['post_content'];
                        }

                        $query = "INSERT INTO posts(post_title, post_category_id, post_date, 
                post_user, post_status, post_image, post_tags, post_content) ";

                        $query .= "VALUES('{$post_title}', {$post_category_id}, now(), '{$post_user}',
                '{$post_status}', '{$post_image}', '{$post_tags}', '{$post_content}')";

                        $clone_query = mysqli_query($connection, $query);

                        if (!$clone_query) {
                            die("Query failed" . mysqli_error($connection));
                        }
                        break;

                    case 'reset':
                        $query = "UPDATE posts SET post_viewcount = 0 WHERE post_id = $postValueId";
                        $reset_query = mysqli_query($connection, $query);
                        confirmQuery($reset_query);
                        break;
                }
            }
        }
    }
}

?>

<!--INDIVIDUAL OPTIONS-->
<?php


if (isset($_POST['delete'])) {

            $the_post_id = escape($_POST['post_id']);

            $query = "DELETE FROM posts WHERE post_id =" . $the_post_id . " ";
            $delete_query = mysqli_query($connection, $query);
            redirect("posts.php"); //refresh page
}

if (isset($_GET['reset'])) {

    if (isset($_SESSION['user_role'])) {

        if ($_SESSION['user_role'] == 'admin') {

            $the_post_id = escape($_GET['reset']);

            $query = "UPDATE posts SET post_viewcount = 0 WHERE post_id =" . $_GET['reset'] . " ";
            $reset_query = mysqli_query($connection, $query);
            redirect("posts.php"); //refresh page
        }
    }
}
?>


<form action="" method="post">
    <table class="table table-bordered table-hover">
        <div id="bulkOptionContainer" class="col-xs-4">

            <select class="form-control" name="bulk_options" id="">
                <option value="">Select Options</option>
                <option value="published">Publish</option>
                <option value="draft">Draft</option>
                <option value="delete">Delete</option>
                <option value="clone">Clone</option>
                <option value="reset">Reset Views</option>
            </select>

        </div>

        <div class="col-xs-4">

            <input type="submit" name="submit" class="btn btn-success" value="Apply">
            <a href="posts.php?source=add_post" class="btn btn-primary">Add New</a>

        </div>

        <thead>
            <tr>
                <th><input id="selectAllBoxes" type="checkbox"></th>
                <th>Id</th>
                <th>User</th>
                <th>Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Image</th>
                <th>Tags</th>
                <th>Comments</th>
                <th>Views</th>
                <th>Date</th>
                <th>Link</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>

        <tbody>

            <?php

            //$query = "SELECT * FROM posts ORDER BY post_id DESC";

            $user = currentUser();

            //joining 'posts' and 'categories' tables
            $query  = "SELECT posts.post_id, posts.post_author, posts.post_user, posts.post_title, posts.post_category_id, ";
            $query .= "posts.post_status, posts.post_image, posts.post_tags, posts.post_comment_count, posts.post_date, ";
            $query .= "posts.post_viewcount, categories.cat_id, categories.cat_title "; 
            $query .= "FROM posts ";
            $query .= "LEFT JOIN categories ON posts.post_category_id = categories.cat_id ";
            $query .= "WHERE posts.post_user = '$user' ";  
            $query .= "ORDER BY posts.post_id DESC ";
                      


            $select_posts = mysqli_query($connection, $query);
            confirmQuery($select_posts);
            while ($row = mysqli_fetch_assoc($select_posts)) {

                $post_id            = $row['post_id'];
                $post_author        = $row['post_author'];
                $post_user          = $row['post_user'];
                $post_title         = $row['post_title'];
                $post_category_id   = $row['post_category_id'];
                $post_status        = $row['post_status'];
                $post_image         = $row['post_image'];
                $post_tags          = $row['post_tags'];
                $post_comment_count = $row['post_comment_count'];
                $post_date          = $row['post_date'];
                $post_viewcount     = $row['post_viewcount'];
                $category_title     = $row['cat_title'];
                $category_id        = $row['cat_id'];

                echo "<tr>";
            ?>

                <td><input class="checkBoxes" type="checkbox" name="checkBoxArray[]" value="<?php echo $post_id; ?>"></td>

                <?php

                echo "<td>{$post_id}</td>";

                if (!empty($post_author)) {

                    echo "<td><a href='../author_posts.php?author={$post_author}&p_id={$post_id}'>$post_author</a></td>";
                } elseif (!empty($post_user)) {

                    echo "<td><a href='../author_posts.php?author={$post_user}&p_id={$post_id}'>$post_user</a></td>";
                }

                echo "<td>{$post_title}</td>";
                echo "<td>{$category_title}</td>";
                echo "<td>{$post_status}</td>";
                echo "<td><img width='100' src='../images/" . imagePlaceholder($post_image) . "' alt='image'></td>";
                echo "<td>{$post_tags}</td>";

                $query = "SELECT * FROM comments WHERE comment_post_id = $post_id ";
                $send_comment_query = mysqli_query($connection, $query);

                while ($row = mysqli_fetch_array($send_comment_query)) {
                    $comment_id = $row['comment_id'];
                }

                $count_comments = mysqli_num_rows($send_comment_query);

                echo "<td><a href='post_comments.php?id={$post_id}'>{$count_comments}</a></td>";

                echo "<td>
                {$post_viewcount}
                <br>
                <a onClick=\"javascript: return confirm('Do you really want to reset views for this post?'); \"
                href='posts.php?reset={$post_id}'>Reset</a>
                </td>";
                echo "<td>{$post_date}</td>";
                echo "<td><a class='btn btn-primary' href='../post.php?p_id={$post_id}'>View Post</a></td>";
                
                echo "<td><a class='btn btn-info' href='posts.php?source=edit_post&p_id={$post_id}'>Edit</a></td>";

                // echo "<td><a onClick=\"javascript: return confirm('Do you really want to delete this post?'); \"
                //      href='posts.php?delete={$post_id}'>Delete</a></td>";

                ?>

                <!--delete via $_POST-->
                <form metod="post" action="">
                    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                    <?php echo "<td><input class='btn btn-danger' type='submit' name='delete' value='Delete'></td>"; ?>
                </form>

            <?php
                //Link with modal warning
                //echo "<td><a rel='$post_id' href='javascript:void(0)' class='delete_link'>Delete</a></td>";

                echo "</tr>";
            }
            ?>

        </tbody>
    </table>
</form>

<script>
    $(document).ready(function() {

        $(".delete_link").on('click', function() {

            var id = $(this).attr("rel");

            var delete_url = "posts.php?delete=" + id + " ";

            $(".modal_delete_link").attr("href", delete_url);

            $("#delPostModal").modal('show');


        });


    });
</script>