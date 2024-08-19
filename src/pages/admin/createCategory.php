<?php 
        
    
    session_start();
    if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 1){
        header("LOCATION:../auth/login.php");
    }
    
    include '../../../assets/layout.php'; 
    include '../../functions/adminFunctions.php';

    if(isset($_GET['errorMessage'])) {
        echo '<div class="container mt-4 d-flex justify-content-center">
        <div class="alert alert-danger col-md-6 text-center">' . $_GET['errorMessage'] . '</div> </div>';
    } 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["name"];
        $image = isset($_FILES['image'])? $_FILES['image']: null;
        createCategory($name, $image);
    }
    
?>

<div class="container"  style="margin-bottom:80px">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">

                <div class="card-header">
                    <h2> Create new Category </h2>
                </div>

                <div class="card-body">
                    <div class="card-title">
                        <p>Please fill this form and submit to add new Category</p>
                    </div>
                    <form method="POST" action="createCategory.php" enctype="multipart/form-data">
                        <div class="card-title">
                           
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Name</label>
                            <div class="col-sm-7">
                                <input type="text" name="name" class="form-control <?= (isset($_GET['errorName']))? 'is-invalid' : ''; ?>" value = '<?= isset($_POST['name'])? $_POST['name']: "";?>'  autofocus>
                                    <span class="invalid-feedback">
                                        <strong><?= $_GET['errorName']; ?></strong>
                                    </span>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Category Image</label>
                            <div class="col-sm-7">
                                <input type="file" name="image" class="form-control <?= (isset($_GET['errorImage']))? 'is-invalid' : ''; ?>"    style="border: none;box-shadow: none;" >
                                    <span class="invalid-feedback">
                                        <strong><?= $_GET['errorImage']; ?></strong>
                                    </span>
                            </div>
                        </div>


                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <input type="submit" class="btn btn-primary" value="Submit">
                                <a class="btn btn-secondary" href="allCategories.php">
                                    Back
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
