<?php

    include '../../../database/db_connection.php';   
    
    // home page functions
    function generalStat(){
        $query = "SELECT
                    (SELECT COUNT(*) FROM users) AS total_users,
                    (SELECT COUNT(*) FROM questions) AS total_questions,
                    (SELECT COUNT(*) FROM categories) AS total_categories;
                ";  
                
        $con = connection();
        $data = mysqli_query($con,$query);
        if(!$data) {
            header("Location:./home.php?errorMessage=Can't select data");
            die;
        }
        $stats = mysqli_fetch_all($data, MYSQLI_ASSOC);
        $con->close();
        return $stats[0];
    }

 
    function categoriesStat(){
        $query =  "SELECT COUNT(*) AS total_categories FROM categories;"; 
        $query .= "SELECT * FROM categories ORDER BY id DESC LIMIT 3;";
        $query .= "SELECT c.*, COUNT(b.id) AS question_count
                   FROM categories c
                   LEFT JOIN questions b ON c.id = b.category_id
                   GROUP BY c.id ORDER BY question_count DESC LIMIT 3;";
        $query .= "SELECT c.*, COUNT(uc.user_id) AS follower_count
                    FROM categories c
                    LEFT JOIN users_categories uc ON c.id = uc.category_id
                    GROUP BY c.id ORDER BY follower_count DESC LIMIT 3;";

        $con = connection();
        $data = []; 
        if (mysqli_multi_query($con, $query)){
            do{
                if ($result = mysqli_store_result($con)){
                    $rows =  mysqli_fetch_all($result, MYSQLI_ASSOC);
                    $data[] = $rows;
                    mysqli_free_result($result);
                }
            }while (mysqli_next_result($con));
        }else{
            header("Location:./home.php?errorMessage=Can't select data of Categories");
            die;        
        }
        
        $con->close();
        return ['0' => $data[0][0], 'latest' => $data[1], 'most_question' =>$data[2], 'most_followed' =>$data[3]];
    }


    function usersStat(){
        $query =  "SELECT COUNT(*) AS total_users FROM users WHERE admin = 0;"; 
        $query .= "SELECT * FROM users WHERE admin = 0 ORDER BY id DESC LIMIT 3 ;";
        $query .= "SELECT u.id, u.name, u.photo, COUNT(b.id) AS question_count 
                   FROM users u LEFT JOIN questions b ON u.id = b.author_id
                   WHERE admin = 0
                   GROUP BY u.id ORDER BY question_count DESC LIMIT 3;";

        $con = connection();
        $data = []; 
        if (mysqli_multi_query($con, $query)){
            do{
                if ($result = mysqli_store_result($con)){
                    $rows =  mysqli_fetch_all($result, MYSQLI_ASSOC);
                    $data[] = $rows;
                    mysqli_free_result($result);
                }
            }while (mysqli_next_result($con));
        }else{
            header("Location:./home.php?errorMessage=Can't select data of Users");
            die;        
        }
        
        $con->close();
        return ['0' => $data[0][0], 'latest' => $data[1], 'most_question' =>$data[2]];
    }



    // users functions
    function showUsers($start, $rows_per_page, $type ){
        $user_id = $_SESSION['userdata']['id']; 
        switch ($type){
            case 'all':
                $query = "SELECT *, COUNT(*) OVER() AS total_users FROM users WHERE id != '$user_id' ORDER BY id DESC LIMIT $start, $rows_per_page ";   
            break; 

            case 'admins':
                $query = "SELECT *, COUNT(*) OVER() AS total_users FROM users WHERE admin = 1 AND id != '$user_id' ORDER BY id DESC LIMIT $start, $rows_per_page";   
            break;

            case 'users':
                $query = "SELECT *, COUNT(*) OVER() AS total_users FROM users WHERE admin = 0 AND id != '$user_id' ORDER BY id DESC LIMIT $start, $rows_per_page";   
            break;

            default:
                die("Error");
        }
        $con = connection();
        $data = mysqli_query($con,$query);
        if(!$data) {
            header("Location:./home.php?errorMessage=Can't select data of users");
            die;
        }
        $users = mysqli_fetch_all($data, MYSQLI_ASSOC);
        $con->close();
        return $users;
    } 


    function deleteUser($user_id){
        if ( $user_id == 0) {
            header("Location:./allUsers.php?errorMessage=Can't delete user");
        }

        $con = connection();
        $query = "DELETE FROM users WHERE id = $user_id ";
        mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);
        $con->close();

        if(!$result) {
            header("Location:./showUser.php?user_id=$user_id&errorMessage=Can't delete user");
        }else{
            header("Location:./allUsers.php?successMessage=User deleted successfully");

        }
    }
    


    // question functions
    function showQuestions($start, $rows_per_page){
        $query = "SELECT questions.*, users.name AS author_name, categories.name AS category_name, COUNT(*) OVER() AS total_questions
                FROM questions 
                JOIN users ON questions.author_id = users.id
                JOIN categories ON questions.category_id = categories.id
                ORDER BY questions.id DESC
                LIMIT $start, $rows_per_page;
                ";

        $con = connection();
        $data = mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);
        if(!$result) {
            header("Location:./home.php?errorMessage=Could not select data of Questions");
            die;
        }
        $questions = mysqli_fetch_all($data, MYSQLI_ASSOC);
        $con->close();
        return $questions;
    } 


    function showUserQuestions($user_id, $start, $rows_per_page){
        if ($user_id <= 0) {
            header("Location:./home.php?errorMessage=Can't select data of user");
            die;
        }

        $query = " SELECT u.id, u.name, u.photo, u.admin, u.email, u.gender, COUNT(DISTINCT b.id) AS total_questions, COUNT(DISTINCT uc.category_id) AS number_of_followed_categories
                    FROM users u
                    LEFT JOIN questions b ON u.id = b.author_id
                    LEFT JOIN users_categories uc ON u.id = uc.user_id
                    WHERE u.id = $user_id;
                ";
                
        $query .= "SELECT questions.*, categories.name AS category_name
                    FROM questions 
                    JOIN categories ON questions.category_id = categories.id
                    WHERE questions.author_id = $user_id
                    ORDER BY questions.id DESC
                    LIMIT $start, $rows_per_page;
                ";

        $con = connection();
        $data = []; 
        if (mysqli_multi_query($con, $query)){
            do{
                if ($result = mysqli_store_result($con)){
                    $rows =  mysqli_fetch_all($result, MYSQLI_ASSOC);
                    $data[] = $rows;
                    mysqli_free_result($result);
                }
            }while (mysqli_next_result($con));
        }else{
            header("Location:./home.php?errorMessage=Can't select Questions of User");
            die;
        }
        $con->close();
        return ["user" =>$data[0][0], "questions" =>$data[1]];   
    }


    function showQuestion($question_slug){
        if ( $question_slug == null) {
            header("Location:./allQuestions.php?errorMessage=invalid Question");
            die;
        }

        $query = "SELECT questions.*, users.name AS author_name, categories.name AS category_name, categories.id AS category_id
                FROM questions 
                JOIN users ON questions.author_id = users.id
                JOIN categories ON questions.category_id = categories.id
                WHERE questions.slug = '$question_slug';
                ";

        $con = connection();
        $data = mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);
        if(!$result) {
            header("Location:./allQuestions.php?errorMessage=Could not select data of Question");
            die;
        }
        $question = mysqli_fetch_assoc($data);
        $con->close();
        return $question;
    }


    function deleteQuestion($question_slug){
        if ( $question_slug == null) {
            header("Location:./allQuestions.php?errorMessage=invalid Question");
            die;
        }
        $con = connection();
        $query = "DELETE FROM questions WHERE slug = '$question_slug' ";
        mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);
        $con->close();
        if(!$result) {
            header("Location:./showQuestion.php?question_slug=$question_slug&errorMessage=Could not delete Question");
        }else{
            header("Location:./allQuestion.php?successMessage=Question deleted successfully");
        }
    }



    // answers 
    function showQuestionAnswers($question_slug, $start, $rows_per_page){
        $query = "SELECT b.id AS question_id, b.title AS question_title, b.slug AS question_slug, COUNT(c.id) AS total_answers
                    FROM questions b
                    LEFT JOIN answers c ON b.id = c.question_id
                    WHERE b.slug = '$question_slug'
                    GROUP BY b.id;
                ";

        $query .= "SELECT c.id AS answer_id, c.content AS answer_content, u.id AS answer_author_id, u.name AS answer_author_name, u.photo AS answer_author_photo
                  FROM answers c
                  JOIN questions b ON c.question_id = b.id
                  JOIN users u ON c.author_id = u.id  
                  WHERE b.slug = '$question_slug'
                  LIMIT $start, $rows_per_page;
                ";
 
        $con = connection();
        $data = []; 
        if (mysqli_multi_query($con, $query)){
            do{
                if ($result = mysqli_store_result($con)){
                    $rows =  mysqli_fetch_all($result, MYSQLI_ASSOC);
                    $data[] = $rows;
                    mysqli_free_result($result);
                }
            }while (mysqli_next_result($con));
        }else{
            header("Location:./showQuestion.php?question_slug=$question_slug&errorMessage=Can't select answers");
            die;
        }
        $con->close();
        return ["question" =>$data[0][0], "answers" =>$data[1]]; 
    }


    function deleteAnswer($answer_id, $question_slug){
        if ( $answer_id == 0 || $question_slug == null) {
            header("Location:showQuestionAnswers.php?question_slug=$question_slug&errorMessage=Can't delete Answer");
        }
        $con = connection();
        $query = "DELETE FROM answers WHERE id = '$answer_id' ";
        $data = mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);
        $con->close();

        if(!$result) {
            header("Location:showQuestionAnswers.php?question_slug=$question_slug&errorMessage=Can't delete Answer");
        }else{
            header("Location:showQuestionAnswers.php?question_slug=$question_slug&successMessage=Answer deleted successfully");
        }
    }


    // category functions
    function showCategories($start, $rows_per_page, $type = "Latest"){
        $con = connection();

        $query = "SELECT c.*, COUNT(*) OVER() AS total_categories, COUNT(DISTINCT uc.user_id) AS total_followers, COUNT(DISTINCT b.id) AS total_questions
                  FROM categories c 
                  LEFT JOIN users_categories uc ON c.id = uc.category_id
                  LEFT JOIN questions b ON c.id = b.category_id
                  GROUP BY c.id
                  ";

        switch($type){
            case 'Latest':
                $query .=  "ORDER BY c.id DESC LIMIT $start, $rows_per_page;";
            break; 

            case 'Most Questions':
                $query .=  "ORDER BY total_questions DESC LIMIT $start, $rows_per_page;";
            break;

            case 'Most Followed':
                $query .=  "ORDER BY total_followers DESC LIMIT $start, $rows_per_page;";
            break;

            default:
                die("Error");
        }

        $data = mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);  
       
        if(!$result) {
            header("Location:./home.php?errorMessage=invalid Category");
            die;        
        }
        $categories = mysqli_fetch_all($data, MYSQLI_ASSOC);
        $con->close();
        return $categories;
    } 


    function showCategory($Category_id){
        if ( $Category_id == null) {
            header("Location:./allCategories.php?errorMessage=invalid Category");
            die;
        }
        $con = connection();
        $query = "SELECT * FROM categories WHERE id = $Category_id;";
        $data = mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);
        if(!$result) {
            header("Location:./allCategories.php?errorMessage=Can't select Questions of Category");
            die;
        }
        $Category = mysqli_fetch_assoc($data);
        $con->close();
        return $Category;
    }


    function showCategoryQuestions($category_id, $start, $rows_per_page){
        if ( $category_id == 0) {
            header("Location:./allCategories.php?errorMessage=Can't select questions of Category");
            die;
        }

        $query = "SELECT c.name, c.id, c.image, COUNT(DISTINCT b.id) AS total_questions, COUNT(DISTINCT uc.user_id) AS total_followers
                  FROM categories c
                  LEFT JOIN questions b ON c.id = b.category_id
                  LEFT JOIN users_categories uc ON c.id = uc.category_id
                  WHERE c.id = $category_id
                  GROUP BY c.id;";

        $query .= "SELECT  b.category_id AS question_category_id, COUNT(*) OVER() AS total_questions, b.title AS question_title,
                    b.image AS question_image, b.slug AS question_slug, u.id AS author_id, u.name AS author_name, u.photo AS author_photo
                    FROM categories c
                    JOIN questions b ON c.id = b.category_id
                    JOIN users u ON b.author_id = u.id
                    WHERE b.category_id = $category_id
                    LIMIT $start, $rows_per_page;";

        $con = connection();
        $data = []; 
        if (mysqli_multi_query($con, $query)){
            do{
                if ($result = mysqli_store_result($con)){
                    $rows =  mysqli_fetch_all($result, MYSQLI_ASSOC);
                    $data[] = $rows;
                    mysqli_free_result($result);
                }
            }while (mysqli_next_result($con));
        }else{
            header("Location:./allCategories.php?errorMessage=Can't select Questions of Category");
            die;
        }
        $con->close();
        return ["category" =>$data[0][0], "questions" =>$data[1]];
    }


    function validateCategoryName($category_name){
        if(empty(trim($category_name))){
            return "you must enter name of category";
        }elseif(!preg_match("/^[A-Za-z][A-Za-z0-9\s-]{2,49}$/",  trim($category_name))){
            return "Category name must start with a letter, be 3-50 characters long, and may include numbers, spaces, and hyphens";
        }
        return  null ;
    }


    function validateFile($image){
        $file_name = $image['name'];
        if(empty(trim($file_name))){
            $error = "You must upload an Image";
            return $error;
        }

        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'png', 'jpeg', 'gif'];
        if (!in_array($file_type, $allowed_types)) {
            $error = "Sorry, only JPG, JPEG, PNG, & GIF files are allowed.";
            return $error;
        }

        if ($image["size"] >  3145728 ) {
            $error = "Your file is too large, Maximum size is 3MB";
            return $error;
        }
        return null;
    }


    function uploadFile($file, $path){
        $file_name = $file['name'];
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $file_tmp_name = $file['tmp_name'];
        $new_file_name = bin2hex(random_bytes(16)).time(). ".$file_type";
        $flag = move_uploaded_file($file_tmp_name, $path.$new_file_name);
        if($flag){
            return $new_file_name;
        }else{
            return 0;
        }
    }


    function createCategory($name, $image){

        $_GET['errorName'] = validateCategoryName($name);
        $_GET['errorImage'] = validateFile($image);
        if(!empty($_GET['errorName']) || !empty($_GET['errorImage']) ){
            return 0;
        }

        $new_image_name = uploadFile($image, "../../../public/uploads/categories/");
        if(!$new_image_name){
            header("Location:./createCategory.php?errorMessage=Could not upload the image");
            die;
        }

        $query = "INSERT INTO categories (name, image) VALUES ('$name','$new_image_name' )"; 
        $con = connection();
        mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);
        $con->close();

        if(!$result) {
            header("Location:./allCategories.php?errorMessage=Could not create new Category");
        }else{
            header("Location:./allCategories.php?successMessage=Category created successfully");
        }
    }

    
    function updateCategory($category_id, $category_name, $image){
        $_GET['errorName'] = validateCategoryName($category_name);
        if(!empty($_GET['errorName']) ){
            return 0;
        }

        $query = ""; 
        if(!empty(trim($image['name']))){
            $_GET['errorImage'] = validateFile($image);
            if(!empty($_GET['errorImage'])){
                return 0;
            }

            $new_image_name = uploadFile($image, "../../../public/uploads/categories/");
            if(!$new_image_name){
                header("Location:./showCategory.php?category_id=$category_id&errorMessage=Could not upload the image");
                die;
            }
            $query .= "UPDATE categories SET image = '$new_image_name' WHERE id = $category_id;"; 
        }
        
        $query .= "UPDATE categories SET name = '$category_name' WHERE id = '$category_id';"; 
        $con = connection();
        $data = mysqli_multi_query($con,$query);
        if(!$data) {
            header("Location:./showCategoryQuestions.php?category_id=$category_id&errorMessage=Could not update Category");
        }else{
            header("Location:./showCategoryQuestions.php?category_id=$category_id&successMessage=Category updated successfully");
        }
    }


    function deleteCategory($category_id){
        if ( $category_id == null) {
            header("Location:./allCategories.php?errorMessage=invalid Category");
            die;
        }
        $con = connection();
        $query = "DELETE FROM categories WHERE id = '$category_id' ";
        mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);
        $con->close();
        if(!$result) {
            header("Location:./showCategoryQuestions.php?category_id=$category_id&errorMessage=Could not delete Category");
        }else{
            header("Location:./allCategories.php?successMessage=Category deleted successfully");
        }
    }

?>