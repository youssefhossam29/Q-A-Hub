<?php

    if(! isset($_SESSION['userdata']))
        include '../../../database/db_connection.php';   
   

    // login and register functions
    function validtaeName($name){
        if(empty(trim($name))){
            return "You must enter your Name";
        }elseif(!preg_match("/^[A-Za-z][A-Za-z 0-9]{2,49}$/",  trim($name))){
            return "Name must start with a letter and be 3-50 characters long, containing only letters, numbers";
        }
        return null ;
    }


    function validateEmail($email){
        if(empty(trim($email))){
            return "You must enter your Email";
        }elseif(!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/",  $email)){
            return "Please enter a valid email address in the format example@example.com";
        }

        if(isset($_SESSION['userdata'])){
            $user_id = $_SESSION['userdata']['id'];
            $query = "SELECT * FROM users WHERE email = '$email' AND id != $user_id"; 
        }else{
            $query = "SELECT * FROM users WHERE email = '$email'"; 
        }
         
        $con = connection();
        $data = mysqli_query($con,$query);
        $result = mysqli_fetch_assoc($data);
        if($result)
            return "The entered email is already exists";
        else
            return null;
    }


    function validatePassword($pass){
        if(empty($pass)){
            return "You must enter your Password";
        }elseif(!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d\s@$!%*#?&]{8,}$/",  $pass)){
            return "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one digit";
        }
        return null;
    }


    function validatePasswordConfirm($pass, $pass_confirm){
        if($pass != $pass_confirm){
            return "Two password must be the same";
        }
        return null;
    }


    function validateGender($gender){
        if(!preg_match("/^(m|f)$/", $gender) || empty(trim($gender)) ){
            return "You must select your Gender";
        }
        return null ;
    }


    function validateImage($image){
        // check type file
        $file_name = $image['name'];
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'png', 'jpeg', 'gif'];
        if (!in_array($file_type, $allowed_types)) {
            $error = "Sorry, only JPG, JPEG, PNG, & GIF files are allowed.";
            return $error;
        }

        // check size of file (in bytes)
        if ($image["size"] >  3145728 ) {
            $error = "Your file is too large, Maximum size is 3MB";
            return $error;
        }
        return null;
    }


    function uploadImage($file, $path){
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
    

    function register($name, $email, $pass, $pass_confirm, $gender){
        $_GET['errorName'] = validtaeName($name);
        $_GET['errorEmail'] = validateEmail($email);
        $_GET['errorPass'] = validatePassword($pass);
        $_GET['errorPassConfirm'] = validatePasswordConfirm($pass, $pass_confirm);
        $_GET['errorGender'] = validateGender($gender);

        if(!empty($_GET['errorName']) || !empty($_GET['errorEmail']) || !empty($_GET['errorPass']) || !empty($_GET['errorPassConfirm']) || !empty($_GET['errorGender']) ){
            return 0;
        }

        $gender = ($gender == "m") ? 1:0;
        $pass = md5($pass);
        $query = "INSERT INTO users (name,email,password,photo,admin,gender) VALUES ('$name', '$email','$pass','user.jpg', 0 ,'$gender' )"; 
        $con = connection();
        mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);

        if($result){
            $inserted_id =  mysqli_insert_id($con);
            $select_query = "SELECT id, name, email, gender, photo, admin FROM users WHERE id = $inserted_id";
            $data = mysqli_query($con,$select_query);
            $result = mysqli_fetch_assoc($data);
            session_start();
            $_SESSION['userdata'] = $result;
            if($result['admin'] == 1){
                header("Location:../admin/home.php");
            }else{
                header("Location:../user/home.php");
            }

        }else{
            header("Location:register.php?errorMessage=Could not create account");
        } 
    }


    
    function login($email, $pass){
        if(empty(trim($email))){
            $_GET['errorEmail'] = "you must enter your email";
        }
        if(empty(trim($pass))){
            $_GET['errorPass'] = "you must enter your password";
        }

        if(!empty($_GET['errorEmail']) || !empty($_GET['errorPass']) ){
            return 0;
        }

        $pass = md5($pass);
        $query = "SELECT id, name, email, gender, photo, admin FROM users WHERE email = '$email' AND password = '$pass' ";  
        $con = connection();
        $data = mysqli_query($con,$query);
        $result = mysqli_fetch_assoc($data);

        if(!empty($result)){
            session_start();
            $_SESSION['userdata'] = $result;
            if($result['admin'] == 1){
                header("Location:../admin/home.php");
            }else{
                $user_id = $result['id'];
                $query = "SELECT category_id FROM users_categories WHERE user_id = $user_id;"; 
                $data = mysqli_query($con,$query);
                if ($data->num_rows > 0) {
                    while($row = $data->fetch_assoc()) {
                        $followedCategoriesIds[] = $row['category_id'];
                    }
                    $_SESSION['followedCategoriesIds'] =  $followedCategoriesIds;
                }
                header("Location:../user/home.php");
            }        
        }else{
            header("Location:login.php?errorMessage= These credentials do not match our records.");
        } 
    }


    function createAdmin($name, $email, $pass, $pass_confirm, $gender){    
        $_GET['errorName'] = validtaeName($name);
        $_GET['errorEmail'] = validateEmail($email);
        $_GET['errorPass'] = validatePassword($pass);
        $_GET['errorPassConfirm'] = validatePasswordConfirm($pass, $pass_confirm);
        $_GET['errorGender'] = validateGender($gender);

        if(!empty($_GET['errorName']) || !empty($_GET['errorEmail']) || !empty($_GET['errorPass']) || !empty($_GET['errorPassConfirm']) || !empty($_GET['errorGender']) ){
            return 0;
        }

        $gender = ($gender == "m") ? 1:0;
        $pass = md5($pass);
        $query = "INSERT INTO users (name,email,password,photo,admin,gender) VALUES ('$name', '$email','$pass','admin.png' , 1 ,'$gender' )"; 
        $con = connection();
        mysqli_query($con,$query);
        $result = mysqli_affected_rows($con);

        if(!$result) {
            header("Location:./allUsers.php?errorMessage=Could not create new admin");

        }else{
            header("Location:./allUsers.php?successMessage=Admin created successfully");
        }
    }

    
    function updateUser($name, $email, $image, $pass, $pass_confirm, $gender){  
        $user_id = $_SESSION['userdata']['id'];
        $query = "";
        if(!empty(trim($pass))){
            $_GET['errorPass'] = validatePassword($pass);
            $_GET['errorPassConfirm'] = validatePasswordConfirm($pass, $pass_confirm);
            if(!empty($_GET['errorPassConfirm']) || !empty($_GET['errorPass']) ){
                return 0;
            }
            $pass = md5($pass);
            $query .= "UPDATE users SET password = '$pass' WHERE id = $user_id;"; 
        }

        if(!empty(trim($image['name']))){
            $_GET['errorImage'] = validateImage($image);
            if(!empty($_GET['errorImage'])){
                return 0;
            }

            $new_image_name = uploadImage($image, "../../../public/uploads/users/");
            if(!$new_image_name){
                header("Location:./updateProfile.php?errorMessage=Could not upload the image");
                die;
            }
            $query .= "UPDATE users SET photo = '$new_image_name' WHERE id = '$user_id';"; 
        }

        $_GET['errorName'] = validtaeName($name);
        $_GET['errorGender'] = validateGender($gender);
        $_GET['errorEmail'] = validateEmail($email);

        if(!empty($_GET['errorName']) || !empty($_GET['errorEmail']) || !empty($_GET['errorGender']) ){
            return 0;
        }
        
        $gender = ($gender == "m") ? 1:0;
        $query .= "UPDATE users SET name = '$name', email = '$email', gender = '$gender' WHERE id = $user_id "; 
        $con = connection();
        $data = mysqli_multi_query($con,$query);

        if($data) {
            $_SESSION['userdata']['name'] = $name;
            $_SESSION['userdata']['email'] = $email;
            $_SESSION['userdata']['photo'] = isset($new_image_name)? $new_image_name: $_SESSION['userdata']['photo'];
            $_SESSION['userdata']['gender'] = $gender;
            $_SESSION['userdata']['admin'] = $_SESSION['userdata']['admin'];            
            header("Location:./updateProfile.php?successMessage=Profile updated successfully");
        }else{
            header("Location:./updateProfile.php?errorMessage=Could not update Profile");
        }
    }


    function logout(){
        session_destroy(); 
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-42000, '/');
        }
        header("LOCATION:../auth/login.php");
    }

?>