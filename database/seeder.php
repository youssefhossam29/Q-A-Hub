<?php


    include 'db_connection.php'; 
    $con = connection();


    // create users
    $password = md5("12345678");
    $query = "INSERT INTO users (name,email,password,photo,admin,gender) VALUES ('admin','admin@gmail.com','$password','admin.png', 1, 1);"; 
    for($i=1 ; $i<8; $i++){
        $name = "user$i";
        $email = "user$i@gmail.com";
        $gender = rand(0,1);
        $query .= "INSERT INTO users (name,email,password,photo,admin,gender) VALUES ('$name','$email','$password','user.jpg',0,'$gender');"; 
    }

    if (mysqli_multi_query($con, $query)) {
        do {
            if ($result = mysqli_store_result($con)) {
                mysqli_free_result($result);
            }
        }while (mysqli_next_result($con));
    }else{
        echo "Error: " . mysqli_error($con);
    }

    

    // select ids of users
    $query = "SELECT id FROM users WHERE admin = 0;";
    $result = mysqli_query($con,$query);
    if($result){
        $users_ids = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users_ids[] = $row['id'];
        }
    }else{
        echo "Error: " . mysqli_error($con);
    }


    // create categories
    $query = "";
    for($i=1 ; $i<=10; $i++){
        $name = "Category $i";
        $query .= "INSERT INTO categories (name, image) VALUES ('$name', 'category.png');"; 
    }
    if (mysqli_multi_query($con, $query)) {
        do {
            if ($result = mysqli_store_result($con)) {
                mysqli_free_result($result);
            }
        }while (mysqli_next_result($con));
    }else{
        echo "Error: " . mysqli_error($con);
    }


    // select ids of categories
    $query = "SELECT id FROM categories;";
    $result = mysqli_query($con,$query);
    if($result){
        $categories_ids = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categories_ids[] = $row['id'];
        }
    }else{
        echo "Error: " . mysqli_error($con);
    }



    // create Questions
    $query = "";
    for($i=1 ; $i<=15; $i++){
        $author_id = $users_ids[rand(0, sizeof($users_ids) - 1)];
        $category_id = $categories_ids[rand(0, sizeof($categories_ids) - 1)];
        $title = "Lorem Ipsum $i";
        $content = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.";
        $slug = bin2hex(random_bytes(8)) . time();
        $query .= "INSERT INTO questions (author_id, category_id ,title, content, image, slug) VALUES ($author_id, '$category_id', '$title',\"$content\", 'question.png', '$slug');"; 
    }
    if (mysqli_multi_query($con, $query)) {
        do {
            if ($result = mysqli_store_result($con)) {
                mysqli_free_result($result);
            }
        }while (mysqli_next_result($con));
    }else{
        echo "Error: " . mysqli_error($con);
    }


    // select ids of questions
    $query = "SELECT id FROM questions;";
    $result = mysqli_query($con,$query);
    if($result){
        $questions_ids = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $questions_ids[] = $row['id'];
        }
    }else{
        echo "Error: " . mysqli_error($con);
    }


    // create answers
    $query = "";
    for($i=1 ; $i<=20; $i++){
        $author_id = $users_ids[rand(0, sizeof($users_ids) - 1)];
        $question_id = $questions_ids[rand(0, sizeof($questions_ids) - 1)];
        $content = "Answer $i";
        $query .= "INSERT INTO answers (author_id, question_id, content) VALUES ($author_id, $question_id,'$content');"; 
    }
    if (mysqli_multi_query($con, $query)) {
        do {
            if ($result = mysqli_store_result($con)) {
                mysqli_free_result($result);
            }
        }while (mysqli_next_result($con));
    }else{
        echo "Error: " . mysqli_error($con);
    }



    // create user_categories
    $query = "";
    for($i=1 ; $i<=sizeof($users_ids) - 1; $i++){
        $user_id = $users_ids[$i];
        $category_id = $categories_ids[rand(0, sizeof($categories_ids) - 1)];
        $query .= "INSERT INTO users_categories (user_id, category_id) VALUES ($user_id, $category_id);"; 
    }
    if (mysqli_multi_query($con, $query)) {
        do {
            if ($result = mysqli_store_result($con)) {
                mysqli_free_result($result);
            }
        }while (mysqli_next_result($con));
    }else{
        echo "Error: " . mysqli_error($con);
    }

    $con->close();
?>