<?php 

    session_start();
    if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 0){
        header("LOCATION:../auth/login.php");
    }
    include '../../../assets/layout.php'; 
    include '../../functions/userFunctions.php';

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

    $title = isset($_POST['title'])? $_POST['title']: "";
    $content = isset($_POST['content'])? $_POST['content']: "";
    $image = isset($_FILES['image'])? $_FILES['image']: "";
    $category_id = isset($_POST['category_id'])? $_POST['category_id']: 0;
    $categories = getCategoriesInfo();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        createQuestion($title, $content, $image, $category_id);
    }

?>



<div class="container mt-0" style="margin-bottom:80px">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">

                <div class="card-header">
                    <h2> Ask Question </h2>
                </div>

                <div class="card-body">
                    <form method="POST" action="addQuestion.php" enctype="multipart/form-data">

                        <div class="form-group">
                            <label>Title</label>
                            <div>
                                <input type="text" name="title" class="form-control <?= (isset($_GET['errorTitle']))? 'is-invalid' : ''; ?>" value = '<?= $title;?>' autofocus>
                                <span class="invalid-feedback">
                                    <strong><?= $_GET['errorTitle']; ?></strong>
                                </span>
                            </div>
                        </div>


                        <div>
                            <label>Content</label>
                            <div class="form-group"> 
                                <textarea name="content" rows="3" class="form-control <?= (isset($_GET['errorContent']))? 'is-invalid' : ''; ?>"><?= "$content" ?></textarea>
                                <span class="invalid-feedback">
                                    <strong><?= $_GET['errorContent']; ?></strong>
                                </span>
                            </div>
                        </div> 
                        

                        <div class="form-group">
                            <label>Category</label>
                            <div class="form-group"> 
                                <select name="category_id" class="form-control <?= (isset($_GET['errorCategory']))? 'is-invalid' : ''; ?>">
                                    <option value="" disabled selected>Select a category</option>
                                    <?php 
                                        foreach($categories as $category):
                                    ?>
                                    <option value="<?= $category['id'];?>"  
                                        <?php if ($category_id == $category['id']) echo 'selected'; ?>
                                    ><?= $category['name'];?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="invalid-feedback">
                                    <strong><?= $_GET['errorCategory']; ?></strong>
                                </span>
                            </div>
                        </div>

                        
                        <div class="form-group">
                            <label>Image</label>
                            <div>
                                <input type="file" name="image" class="form-control <?= (isset($_GET['errorImage']))? 'is-invalid' : ''; ?>" value = '<?= "$image";?>'  style="border: none;box-shadow: none;">
                                <span class="invalid-feedback">
                                    <strong><?= $_GET['errorImage']; ?></strong>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Create">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>




