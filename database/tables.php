<?php

    include 'db_connection.php'; 
    $con = connection();

    $query = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            photo VARCHAR(255),
            gender BOOLEAN DEFAULT 1,
            admin BOOLEAN DEFAULT 0
        );";


    $query .= "CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            image VARCHAR(255) NOT NULL
        );"; 
        
        
    $query .= "CREATE TABLE IF NOT EXISTS questions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            author_id INT,
            category_id INT,
            title VARCHAR(255) NOT NULL,
            content LONGTEXT,
            image VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
        );"; 


    $query .= "CREATE TABLE IF NOT EXISTS answers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            author_id INT,
            question_id INT,
            content VARCHAR(255) NOT NULL,
            FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
        );"; 


    $query .= "CREATE TABLE IF NOT EXISTS users_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            category_id INT NOT NULL,
            UNIQUE KEY unique_user_category (user_id, category_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
        );";

    
    mysqli_multi_query($con,$query);

?>