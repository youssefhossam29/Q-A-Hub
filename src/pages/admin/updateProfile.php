<?php 
    session_start();
    if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 1){
        header("LOCATION:../auth/login.php");
    }

    include '../../functions/adminFunctions.php';
    include '../../functions/authFunctions.php';
    include '../../../assets/layout.php'; 

    $user = $_SESSION['userdata']; 
    $user_id = $_SESSION['userdata']['id']; 
    $name = isset($_POST['name'])? $_POST['name']: $user['name'];
    $email = isset($_POST['email'])? $_POST['email']: $user['email'];
    $gender = isset($_POST['gender'])? $_POST['gender']: ($user['gender'] == 1 ? 'm':'f');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $pass = isset($_POST["password"])? $_POST["password"]:null ;
        $passConfirm = isset($_POST["password_confirm"])? $_POST["password_confirm"]:null ;
        $image = isset($_FILES['image'])? $_FILES['image']: null;
        updateUser($name, $email, $image, $pass, $passConfirm, $gender);
    }


?>


<?php
    if(isset($_SESSION['successMessage'])){
        echo '<div class="container mt-4 d-flex justify-content-center">
        <div class="alert alert-success col-md-6 text-center">' . $_SESSION['successMessage'] . '</div></div>';
        unset($_SESSION['successMessage']);
    }
    
    if(isset($_SESSION['errorMessage'])){
        echo '<div class="container mt-4 d-flex justify-content-center">
        <div class="alert alert-danger col-md-6 text-center">' . $_SESSION['errorMessage'] . '</div></div>';
        unset($_SESSION['errorMessage']);
    }
?>

<div class="container" style="margin-bottom: 80px;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">

                <div class="card-header">
                    <h2>Update Your Profile </h2>
                </div>

                <div class="card-body">
                    
                    <form method="post" action='updateProfile.php?user_id=<?= $user['id'];?>' enctype="multipart/form-data">  

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Name</label>
                            <div class="col-sm-7">
                                <input type="text" name="name" class="form-control <?= (isset($_GET['errorName']))? 'is-invalid' : ''; ?>" value = "<?= $name?>"  autofocus>
                                    <span class="invalid-feedback">
                                        <strong><?= $_GET['errorName']; ?></strong>
                                    </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Email</label>
                            <div class="col-sm-7">
                                <input type="email" name="email" class="form-control <?= (isset($_GET['errorEmail']))? 'is-invalid' : ''; ?>" value = "<?= $email?>" >
                                    <span class="invalid-feedback">
                                        <strong><?= $_GET['errorEmail']; ?></strong>
                                    </span>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Profile Picture</label>
                            <div class="col-sm-7">
                                <img class="img-fluid" src="../../../public/uploads/users/<?= $user['photo']; ?>" alt="Profile Picture" style="height:300px">
                                <input type="file" name="image" class="form-control <?= (isset($_GET['errorImage']))? 'is-invalid' : ''; ?>" value = "<?= $image;?>"  style="border: none;box-shadow: none;" >
                                    <span class="invalid-feedback">
                                        <strong><?= $_GET['errorImage']; ?></strong>
                                    </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Password</label>
                            <div class="col-sm-7">
                                <input name="password" type="password" class="form-control <?= (isset($_GET['errorPass']))? 'is-invalid' : ''; ?>"  >
                                    <span class="invalid-feedback" role="alert">
                                        <strong><?= $_GET['errorPass']; ?></strong>
                                    </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Confirm Password</label>
                            <div class="col-sm-7">
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
                                        <input class="form-check-input" type="radio" name="gender" value="m" <?= ($gender == 'm')? 'checked':"" ;?> >
                                        <label class="form-check-label">Male</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" value="f" <?= ($gender == 'f')? 'checked':"" ;?> >
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
                                <input type="submit" class="btn btn-primary" value="Submit">
                                <a class="btn btn-secondary" href="home.php">
                                    Back
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div