<?php

    include '../../../assets/layout.php'; 
    include '../../functions/authFunctions.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST["email"];
        $pass = $_POST["password"];
        login($email, $pass);
    } 

?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">

                <div class="card-header">
                    Login
                </div>

                <div class="card-body">
                    <form method="POST" action="login.php">

                        <div class="card-title">
                            <?php 
                                if(isset($_GET['errorMessage'])) {
                                echo '<div class="alert alert-danger" role="alert">'.  $_GET["errorMessage"] .' </div>'; 
                                } 
                            ?>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Email</label>
                            <div class="col-sm-6">
                                <input type="email" name="email" class="form-control <?= (isset($_GET['errorEmail']))? 'is-invalid' : ''; ?>"  required autofocus>
                                    <span class="invalid-feedback">
                                        <strong><?= $_GET['errorEmail']; ?></strong>
                                    </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Password</label>
                            <div class="col-sm-6">
                                <input name="password" type="password" class="form-control <?= (isset($_GET['errorPass']))? 'is-invalid' : ''; ?>"  required>
                                    <span class="invalid-feedback" role="alert">
                                        <strong><?= $_GET['errorPass']; ?></strong>
                                    </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
                                <input type="checkbox" > Remember Me
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <input type="submit" class="btn btn-primary" value="Login">
                            </div>
                            <div class="col-md-8 offset-md-4 mt-3">
                                <p>Don't have an account? <a href="register.php">Register here</a></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
