<?php

    session_start();
    if(isset($_SESSION['userdata'])){
        if($_SESSION['userdata']['admin'] == 0){
            header("LOCATION:../user/home.php");
            die;
        }
        else{
            header("LOCATION:../admin/home.php");
        }
    }
    
    include '../../../assets/layout.php'; 
    include '../../functions/authFunctions.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $pass = $_POST["password"];
        $passConfirm = $_POST["password_confirm"];
        $gender = isset($_POST["gender"])? $_POST["gender"]:0 ;
        register($name, $email, $pass, $passConfirm, $gender);
    }
    
?>


<div class="container mt-0" style="margin-bottom:80px">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">

                <div class="card-header">
                    <h2> Register </h2>
                </div>

                <div class="card-body">
                    <form method="POST" action="register.php">
                        <div class="card-title">
                            <?php 
                                if(isset($_SESSION['errorMessage'])){
                                    echo '<div class="container mt-4 d-flex justify-content-center">
                                    <div class="alert alert-danger col-md-6 text-center">' . $_SESSION['errorMessage'] . '</div></div>';
                                    unset($_SESSION['errorMessage']);
                                }
                            ?>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Name</label>
                            <div class="col-sm-6">
                                <input type="text" name="name" class="form-control <?= (isset($_GET['errorName']))? 'is-invalid' : ''; ?>" value = '<?= isset($_POST['name'])? $_POST['name']: "";?>' autofocus>
                                    <span class="invalid-feedback">
                                        <strong><?= $_GET['errorName']; ?></strong>
                                    </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Email</label>
                            <div class="col-sm-6">
                                <input type="email" name="email" class="form-control <?= (isset($_GET['errorEmail']))? 'is-invalid' : ''; ?>" value = '<?= isset($_POST['email'])? $_POST['email']: "";?>' >
                                    <span class="invalid-feedback">
                                        <strong><?= $_GET['errorEmail']; ?></strong>
                                    </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Password</label>
                            <div class="col-sm-6">
                                <input name="password" type="password" class="form-control <?= (isset($_GET['errorPass']))? 'is-invalid' : ''; ?>"  >
                                    <span class="invalid-feedback" role="alert">
                                        <strong><?= $_GET['errorPass']; ?></strong>
                                    </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Confirm Password</label>
                            <div class="col-sm-6">
                                <input name="password_confirm" type="password" class="form-control <?= (isset($_GET['errorPassConfirm']))? 'is-invalid' : ''; ?>"  >
                                    <span class="invalid-feedback" role="alert">
                                        <strong><?= $_GET['errorPassConfirm']; ?></strong>
                                    </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label class="col-form-label col-sm-4 pt-0">Gender</label>
                                <div class="col-sm-7">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" value="m" 
                                            <?= (isset($_POST['gender']) && $_POST['gender'] == 'm')? 'checked':"" ;?> 
                                        >
                                        <label class="form-check-label">Male</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" value="f"
                                            <?= (isset($_POST['gender']) && $_POST['gender'] == 'f')? 'checked':"" ;?>
                                        >
                                        <label class="form-check-label">Female</label>
                                    </div>
                                    
                                    <span class="invalid-feedback" style="display: block;">
                                        <strong><?= isset($_GET['errorGender'])? "You must choose your gender":""; ?></strong>
                                    </span>
                                </div>
                            </div>
                        </div>                    

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <input type="submit" class="btn btn-primary" value="Register">
                            </div>
                            <div class="col-md-8 offset-md-4 mt-3">
                                <p>Already have an account? <a href="login.php">Login here</a></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>




