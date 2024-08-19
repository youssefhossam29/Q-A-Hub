<?php

    if(! isset($_SESSION['userdata']))
        include '../../../database/db_connection.php';   
   

    // login and register functions
    function validate($input , $inputName, $regx){
        if(empty(trim($input))){
            $error = "You must enter $inputName";
        }elseif(!preg_match($regx,  $input)){
            $error = "The $inputName you enterd doesn't match the required format";
        }
        return isset($error) ? $error : null ;
    }


    function checkEmail($email){
        $_GET['errorEmail'] = validate($email, 'email', "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/");
        if($_GET['errorEmail']){
            return $_GET['errorEmail'];
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


    function validateImage($image){

        // check type file
        $fileName = $image['name'];
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'png', 'jpeg', 'gif'];
        if (!in_array($fileType, $allowedTypes)) {
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
        $fileName = $file['name'];
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $fileTmpName = $file['tmp_name'];
        $newFileName = bin2hex(random_bytes(16)).time(). ".$fileType";
        $flag = move_uploaded_file($fileTmpName, $path.$newFileName);
        if($flag){
            return $newFileName;
        }else{
            return 0;
        }
    }
    

    function validatePasswordConfirm($pass, $passConfirm){
        if($pass != $passConfirm){
            $errorPassConfirm = "two password must be the same";
        }
        return isset($errorPassConfirm) ? $errorPassConfirm : null ;
    }
 


    function register($name, $email, $pass, $passConfirm, $gender){
        
        $_GET['errorName'] = validate($name, 'name', "/^[A-Za-z][A-Za-z 0-9]{1,49}$/");
        $_GET['errorEmail'] = checkEmail($email);
        $_GET['errorPass'] = validate($pass, 'password', "/^[A-Za-z\d@$!%*#?&]{8,}$/");
        $_GET['errorGender'] = validate($gender, 'gender', "/^(m|f)$/");
        $_GET['errorPassConfirm'] = validatePasswordConfirm($pass, $passConfirm);

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




    function createAdmin($name, $email, $pass, $passConfirm, $gender){
        
        $_GET['errorName'] = validate($name, 'name', "/^[A-Za-z][A-Za-z 0-9]{1,49}$/");
        $_GET['errorEmail'] = checkEmail($email);
        $_GET['errorPass'] = validate($pass, 'password', "/^[A-Za-z\d@$!%*#?&]{8,}$/");
        $_GET['errorGender'] = validate($gender, 'gender', "/^(m|f)$/");
        $_GET['errorPassConfirm'] = validatePasswordConfirm($pass, $passConfirm);

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

    

    function updateUser($name, $email, $image, $pass, $passConfirm, $gender){  
        
        $user_id = $_SESSION['userdata']['id'];
        $query = "";
        if(!empty(trim($pass))){
            $_GET['errorPass'] = validate($pass, 'password', "/^[A-Za-z\d@$!%*#?&]{8,}$/");
            $_GET['errorPassConfirm'] = validatePasswordConfirm($pass, $passConfirm);
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

            $newImageName = uploadImage($image, "../../../public/uploads/users/");
            if(!$newImageName){
                header("Location:./updateProfile.php?errorMessage=Could not upload the image");
                die;
            }
            $query .= "UPDATE users SET photo = '$newImageName' WHERE id = '$user_id';"; 
        }

        $_GET['errorName'] = validate($name, 'name', "/^[A-Za-z][A-Za-z 0-9]{1,49}$/");
        $_GET['errorGender'] = validate($gender, 'gender', "/^(m|f)$/");
        $_GET['errorEmail'] = checkEmail($email);

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
            $_SESSION['userdata']['photo'] = isset($newImageName)? $newImageName: $_SESSION['userdata']['photo'];
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