<?php

    include '../../../database/db_connection.php';    


    // home page 
    function homePageContent($start , $rows_per_page){
        $query = "SELECT c.id AS category_id, c.name AS category_name,b.title AS question_title, b.image AS question_image,
                    b.slug AS question_slug, u.id AS author_id, u.name AS author_name
                    FROM categories c
                    JOIN questions b ON c.id = b.category_id
                    JOIN users u ON b.author_id = u.id
                    ORDER BY b.id DESC LIMIT 8;
                ";
        
        $query .=  "SELECT c.*, COUNT(uc.user_id) AS total_followers
                     FROM categories c
                     LEFT JOIN users_categories uc ON c.id = uc.category_id
                     GROUP BY c.id ORDER BY total_followers DESC
                     LIMIT 3;
                "; 

        $query .=  "SELECT c.*, COUNT(b.id) AS total_questions
                     FROM categories c
                     LEFT JOIN questions b ON c.id = b.category_id
                     GROUP BY c.id ORDER BY total_questions DESC
                     LIMIT 3;
                ";

        $followed_categories_ids = isset($_SESSION['followedCategoriesIds']) ? $_SESSION['followedCategoriesIds'] : [0];
        $ids_string = implode(',', $followed_categories_ids);
        $query .= "SELECT c.id AS category_id, c.name AS category_name, COUNT(*) OVER() AS total_questions,b.title AS question_title,
                    b.image AS question_image, b.slug AS question_slug, u.id AS author_id, u.name AS author_name
                    FROM categories c
                    JOIN questions b ON c.id = b.category_id
                    JOIN users u ON b.author_id = u.id
                    WHERE b.category_id IN ($ids_string)
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
            $con->close();
            $_SESSION['errorMessage'] = "Can't reload Home page";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER']:"allCategories.php";
            header("Location: $redirectUrl");
        }
        $con->close();
        return ["latest_questions" => $data[0], "most_followed_categories" => $data[1], 
                "most_question_categories" => $data[2], "followed_questions" => $data[3]
                ] ;  
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
        $con->close();

        if(!$result) {
            return [];
        }else{
            $questions = mysqli_fetch_all($data, MYSQLI_ASSOC);
            return $questions;
        }
    } 


    function createQuestion($title, $content, $image, $category_id){
        $_GET['errorTitle'] = validateQuestionTitle($title);
        $_GET['errorContent'] = validateQuestionContent($content);
        $_GET['errorCategory'] = validateCategoryId($category_id);    

        if(!empty($_GET['errorTitle']) || !empty($_GET['errorContent']) || !empty($_GET['errorCategory'])  ){
            return 0;
        }

        if(empty(trim($image['name']))){
            $new_image_name = "question.png";
        }else{
            $_GET['errorImage'] = validateFile($image);
            if(($_GET['errorImage'])){
                return 0;
            }
            $new_image_name = uploadFile($image, "../../../public/uploads/questions/");
            if(!$new_image_name){
                $_SESSION['errorMessage'] = "Can't create Question";
                header("Location:addQuestion.php");
                die;
            }
        }

        $author_id = $_SESSION['userdata']['id']; 
        $slug = bin2hex(random_bytes(8)) . time();

        $query = "INSERT INTO questions (author_id,category_id, title,content,image,slug) VALUES ('$author_id','$category_id', '$title',\"$content\",'$new_image_name', '$slug' )"; 
        $con = connection();
        mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);
        $con->close();

        if(!$result) {
            $_SESSION['errorMessage'] = "Can't create Question";
            header("Location:addQuestion.php");
        }else{
            $_SESSION['successMessage'] = "Question created successfully";
            header("Location:./showUser.php?user_id=$author_id");
        }
    }


    function updateQuestion($question_slug, $title, $content, $image, $category_id){
        $_GET['errorTitle'] = validateQuestionTitle($title);
        $_GET['errorContent'] = validateQuestionContent($content);
        $_GET['errorCategory'] = validateCategoryId($category_id);    

        if(!empty($_GET['errorTitle']) || !empty($_GET['errorContent']) || !empty($_GET['errorCategory']) ){
            return 0;
        }
       

        $query = ""; 
        if(!empty(trim($image['name']))){
            $_GET['errorImage'] = validateFile($image);
            if(($_GET['errorImage'])){
                return 0;
            }

            $new_image_name = uploadFile($image, "../../../public/uploads/questions/");
            if(!$new_image_name){
                $_SESSION['errorMessage'] = "Can't update your Question";
                header("Location:updateQuestion.php?question_slug=$question_slug");
                die;
            }
            
            $query .= "UPDATE questions SET image = '$new_image_name' WHERE slug = '$question_slug';"; 
        }
        
        $query .= "UPDATE questions SET title = '$title', content = \"$content\", category_id = '$category_id' WHERE slug = '$question_slug';"; 
        $con = connection();
        $data = mysqli_multi_query($con,$query);
        $con->close();

        if(!$data) {
            $_SESSION['errorMessage'] = "Can't update your Question";
        }else{
            $_SESSION['successMessage'] = "Question updated successfully";
        }
        header("Location:updateQuestion.php?question_slug=$question_slug");
    }


    function validateQuestionTitle($title){
        if(empty(trim($title))){
            return "You must enter title";
        }
        return null ;
    }


    function validateQuestionContent($content){
        if(empty(trim($content))){
            return "You must enter title";
        }
        return null ;
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


    function canUserModifyQuestion($question_slug){
        $author_id = $_SESSION['userdata']['id'];
        $query = "SELECT * FROM questions WHERE slug = '$question_slug' AND author_id = '$author_id' ;";
        $con = connection();
        $data = mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);
        $con->close();

        if(!$result){
            return 0;
        }else{
            return 1;
        } 
    }


    function showUserQuestions($user_id, $start, $rows_per_page){
        if ($user_id <= 0) {
            $_SESSION['errorMessage'] = "User not found";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER']:"home.php";
            header("Location: $redirectUrl");
            die;
        }

        $query = " SELECT u.id, u.name, u.photo, COUNT(DISTINCT b.id) AS total_questions, COUNT(DISTINCT uc.category_id) AS number_of_followed_categories
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
            $con->close();
            $_SESSION['errorMessage'] = "User not found";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER']:"home.php";
            header("Location: $redirectUrl");
            die;
        }

        if(empty($data[0][0]['id'])){
            $_SESSION['errorMessage'] = "User not found";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER']:"home.php";
            header("Location: $redirectUrl");
            die;
        }
        $con->close();
        return ["user" =>$data[0][0], "questions" =>$data[1]];   
    }


    function showQuestion($question_slug){
        if ( $question_slug == null) {
            $_SESSION['errorMessage'] = "Can't select data of Question";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "allQuestions.php";
            header("Location: $redirectUrl");
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
        $con->close();

        if(!$result) {
            $_SESSION['errorMessage'] = "Can't select data of Question";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "allQuestions.php";
            header("Location: $redirectUrl");
            die;
        }
        $question = mysqli_fetch_assoc($data);
        return $question;
    }


    function deleteQuestion($question_slug){
        $con = connection();
        $query = "DELETE FROM questions WHERE slug = '$question_slug' ";
        mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);
        $con->close();
        if(!$result) {
            $_SESSION['errorMessage'] = "Can't delete Question";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "showQuestion.php?question_slug=$question_slug";
            header("Location: $redirectUrl");
            die;
        }else{
            $user_id = $_SESSION['userdata']['id'];
            $_SESSION['successMessage'] = "Question deleted successfully";
            header("Location:./showUser.php?user_id=$user_id&successMessage=Question deleted successfully");
        }
    }



    // Answers 
    function createAnswer($question_id, $question_slug, $content){    
        $author_id = $_SESSION['userdata']['id']; 
        $query = "INSERT INTO answers (author_id,question_id,content) VALUES ('$author_id', '$question_id','$content')"; 
        $con = connection();
        mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);
        $con->close();

        if(!$result) {
            $_SESSION['errorMessage'] = "Can't add Answer";
        }else{
            $_SESSION['successMessage'] = "Answer Added successfully";
        }
        header("Location:showQuestionAnswers.php?question_slug=$question_slug");
    }


    function showQuestionAnswers($question_slug, $start, $rows_per_page){
        if ( $question_slug == null) {
            $_SESSION['errorMessage'] = "Can't select answers of Question";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "allQuestions.php";
            header("Location: $redirectUrl");
            die;
        }

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
            $_SESSION['errorMessage'] = "Can't select answers";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "showQuestion.php?question_slug=$question_slug";
            header("Location: $redirectUrl");
            die;
        }
        $con->close();
        if(empty($data[0])){
            $_SESSION['errorMessage'] = "Can't select answers";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "allQuestions.php";
            header("Location: $redirectUrl");
            die;          
        }
        return ["question" =>$data[0][0], "answers" =>$data[1]];
    }


    function canUserModifyAnswer($answer_id){
        $author_id = $_SESSION['userdata']['id'];
        $query = "SELECT * FROM answers WHERE id = '$answer_id' AND author_id = '$author_id' LIMIT 1;";
        $con = connection();
        $data = mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);
        $con->close();

        if(!$result){
            return 0;
        }else{
            return 1;
        }   
    }


    function updateAnswer($answer_id, $content, $question_slug){
        $query = "UPDATE answers SET content = '$content' WHERE id = '$answer_id';";
        $con = connection();
        $data = mysqli_query($con,$query);
        $con->close();

        if(!$data) {
            $_SESSION['errorMessage'] = "Can't update your Answer";
        }else{
            $_SESSION['successMessage'] = "Your answer updated successfully";
        }
        $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "showQuestionAnswers.php?question_slug=$question_slug";
        header("Location: $redirectUrl");
    }


    function deleteAnswer($answer_id, $question_slug){
        $con = connection();
        $query = "DELETE FROM answers WHERE id = '$answer_id' ";
        $data = mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);
        $con->close();

        if(!$result) {
            $_SESSION['errorMessage'] = "Can't delete your Answer";
        }else{
            $_SESSION['successMessage'] = "Your answer deleted successfully";
        }
        $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "showQuestionAnswers.php?question_slug=$question_slug";
        header("Location: $redirectUrl");
    }


    function showAnswer($answer_id, $question_slug){
        $user_id = $_SESSION['userdata']['id'];
        $query = "SELECT b.title AS question_title, b.id AS question_id, b.slug AS question_slug, a.id AS answer_id, a.content AS answer_content, a.author_id AS author_id
                  FROM questions b
                  JOIN answers a ON b.id = a.question_id
                  WHERE  a.id = $answer_id AND a.author_id = $user_id
                  LIMIT 1;
                ";

        $con = connection();
        $data = mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);
        $con->close();

        if(!$result) {
            $_SESSION['errorMessage'] = "Un Authorized";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "showQuestionAnswers.php?question_slug=$question_slug";
            header("Location: $redirectUrl");
        }

        $answer = mysqli_fetch_assoc($data);
        if($answer['question_slug'] != $question_slug ){
            $_SESSION['errorMessage'] = "Un Authorized";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "allQuestions.php";
            header("Location: $redirectUrl");
            die;
        }else{
            return $answer;
        }
    }



    // Categories functions
    function showCategories($start, $rows_per_page, $type = "Latest"){
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

        $con = connection();
        $data = mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);  
        $con->close();

        if(!$result) {
            $_SESSION['errorMessage'] = "Can't select Categories";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "home.php";
            header("Location: $redirectUrl");
            die;        
        }
        $categories = mysqli_fetch_all($data, MYSQLI_ASSOC);
        return $categories;
    } 


    function getCategoriesInfo(){
        $con = connection();
        $query = "SELECT * FROM categories";
        $con = connection();
        $data = mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);
        if(!$result) {
            return [];
        }
        $categories = mysqli_fetch_all($data, MYSQLI_ASSOC);
        $con->close();
        return $categories;
    }


    function showCategoryQuestions($category_id, $start, $rows_per_page){
        if ($category_id <= 0) {
            $_SESSION['errorMessage'] = "Invalid Category";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "allCategories.php";
            header("Location: $redirectUrl");            
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
            $_SESSION['errorMessage'] = "Can't select questions of Category";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "allCategories.php";
            header("Location: $redirectUrl");
            die;
        }
        $con->close();
        if(empty($data[0])){
            $_SESSION['errorMessage'] = "Invalid Category";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "allCategories.php";
            header("Location: $redirectUrl");
            die;          
        }
        return ["category" =>$data[0][0], "questions" =>$data[1]];
    }


    function showCategoryFollowers($category_id, $start, $rows_per_page){
        if ($category_id <= 0) {
            $_SESSION['errorMessage'] = "Invalid Category";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "allCategories.php";
            header("Location: $redirectUrl");            
            die;
        }

        $query = "SELECT c.name, c.id, c.image, COUNT(DISTINCT b.id) AS total_questions, COUNT(DISTINCT uc.user_id) AS total_followers
                  FROM categories c
                  LEFT JOIN questions b ON c.id = b.category_id
                  LEFT JOIN users_categories uc ON c.id = uc.category_id
                  WHERE c.id = $category_id
                  GROUP BY c.id;";

        $query .= "SELECT u.name AS user_name, u.id AS user_id, u.photo AS user_photo
                    FROM users u
                    JOIN users_categories uc ON u.id = uc.user_id
                    WHERE uc.category_id = $category_id
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
            $_SESSION['errorMessage'] = "Can't select questions of Category";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "allCategories.php";
            header("Location: $redirectUrl");
            die;
        }
        $con->close();
        if(empty($data[0])){
            $_SESSION['errorMessage'] = "Invalid Category";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "allCategories.php";
            header("Location: $redirectUrl");
            die;          
        }
        return ["category" =>$data[0][0], "users" =>$data[1]];
    }


    function validateCategoryId($category_id){
        if($category_id == 0){
            return 'You Must Select Category';
        }
        $query = "SELECT * FROM categories WHERE id = $category_id LIMIT 1;";
        $con = connection();
        $data = mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);
        if($result) {
            return null;
        }else{
            return 'Invalid Category';
        }
    }



    // manage categories
    function followCategory($category_id) {
        if ($category_id <= 0) {
            $_SESSION['errorMessage'] = "Can't follow category";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "allCategories.php";
            header("Location: $redirectUrl");            
            die;
        }
    
        $con = connection();
        $user_id = $_SESSION['userdata']['id'];
        $query = "INSERT INTO users_categories (user_id, category_id) VALUES ($user_id, $category_id)";
        $data = mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);
        $con->close();

        if(!$result) {
            $_SESSION['errorMessage'] = "Can't follow the category";
        }else{
            $_SESSION['followedCategoriesIds'] = getFollowedCategories();
            $_SESSION['successMessage'] = "You've successfully follow the category";
        }
        $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "allCategories.php";
        header("Location: $redirectUrl");
    } 
    
    
    function unfollowCategory($category_id) {
        if ($category_id <= 0) {
            $_SESSION['errorMessage'] = "Can't follow category";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "allCategories.php";
            header("Location: $redirectUrl");            
            die;
        }
    
        $con = connection();
        $user_id = $_SESSION['userdata']['id'];
        $query = "DELETE FROM users_categories WHERE user_id = '$user_id' AND category_id = '$category_id';";
        $data = mysqli_query($con,$query);

        if(!$data) {
            $_SESSION['errorMessage'] = "Can't unfollow the category";
        }else{
            $_SESSION['followedCategoriesIds'] = getFollowedCategories();
            $_SESSION['successMessage'] = "You've successfully unfollow the category";
        }
        $con->close();
        $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "allCategories.php";
        header("Location: $redirectUrl");
    }


    function getFollowedCategories() {
        $user_id = $_SESSION['userdata']['id'];
        $query = "SELECT category_id AS id FROM users_categories WHERE user_id = $user_id;";
        $user_id = $_SESSION['userdata']['id'];
        $con = connection();
        $data = mysqli_query($con,$query);
        if(!$data) {
            return [];
        }
    
        $categories = array(); 
        if (mysqli_num_rows($data) > 0) {
            while ($row = mysqli_fetch_assoc($data)) {
                $categories[] = $row['id'];
            }
        }        
        
        $con->close();
        return $categories;
    }
    

    function userFollowingCategories($user_id, $start, $rows_per_page){
        if ($user_id <= 0) {
            $_SESSION['errorMessage'] = "Can't select following categories of user";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "home.php";
            header("Location: $redirectUrl");            
            die;
        }

        $query = " SELECT u.id, u.name, u.photo, COUNT(DISTINCT b.id) AS total_questions, COUNT(DISTINCT uc.category_id) AS number_of_followed_categories
                    FROM users u
                    LEFT JOIN questions b ON u.id = b.author_id
                    LEFT JOIN users_categories uc ON u.id = uc.user_id
                    WHERE u.id = $user_id;
                ";
                
        $query .= "SELECT c.id, c.name, c.image
                    FROM categories c 
                    JOIN users_categories uc ON c.id = uc.category_id
                    WHERE uc.user_id = $user_id
                    ORDER BY c.id DESC
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
            $_SESSION['errorMessage'] = "Can't select following categories of user";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "showUser.php?user_id=$user_id";
            header("Location: $redirectUrl");
            die;
        }
        $con->close();
        if(empty($data[0][0]['id'])){
            $_SESSION['errorMessage'] = "User not found";
            $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "home.php";
            header("Location: $redirectUrl");
            die;          
        }
        return ["user" =>$data[0][0], "categories" =>$data[1]];   
    }
    
?>