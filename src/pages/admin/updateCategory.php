<?php 
    session_start();
    if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 1){
        header("LOCATION:../auth/login.php");
    }

    include '../../functions/adminFunctions.php';
    include '../../functions/authFunctions.php';
    include '../../../assets/layout.php'; 

    $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
    $category = showCategory($category_id); 
    $name = isset($_POST['name'])? $_POST['name']: $category['name'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $image = isset($_FILES['image'])? $_FILES['image']: null;
        updateCategory($category_id, $name, $image);
    }
?>



<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">

                <div class="card-header">
                    <h2> Create new Category </h2>
                </div>

                <div class="card-body">
                    <form method="post" action='updateCategory.php?category_id=<?= $category['id'];?>' enctype="multipart/form-data">  

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
                            <label class="col-sm-4 col-form-label">Category Image</label>
                            <div class="col-sm-7">
                                <img class="card-img-top" src="../../../public/uploads/categories/<?= $category['image']; ?>" alt="Profile Picture" height="150px">
                                <input type="file" name="image" class="form-control <?= (isset($_GET['errorImage']))? 'is-invalid' : ''; ?>" value = '<?= "$image";?>'  style="border: none;box-shadow: none;" >
                                    <span class="invalid-feedback">
                                        <strong><?= $_GET['errorImage']; ?></strong>
                                    </span>
                            </div>
                        </div>
                    

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <input type="submit" class="btn btn-primary" value="Submit">
                                <a class="btn btn-secondary" href="showCategoryQuestions.php?category_id=<?= $category['id']; ?>">
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