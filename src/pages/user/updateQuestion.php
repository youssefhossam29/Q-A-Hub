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

    
    $question_slug = isset($_GET['question_slug']) ? ($_GET['question_slug']) : null;
    
    if ( $question_slug == null) {
        $_SESSION['errorMessage'] = "Invalid Question";
        $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "allQuestions.php";
        header("Location: $redirectUrl");            
        die;
    }
    
    $question = showQuestion($question_slug);  
    if($question['author_id'] != $_SESSION['userdata']['id']){
        $_SESSION['errorMessage'] = "Un Authorized";
        $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "showQuestion.php?question_slug=$question_slug";
        header("Location: $redirectUrl");            
        die;
    }

    $categories = getCategoriesInfo();
    $title = isset($_POST['title'])? $_POST['title']: $question['title'];
    $image = isset($_FILES['image'])? $_FILES['image']: null;
    $content = isset($_POST['content'])? $_POST['content']: $question['content'];
    $category_id = isset($_POST['category_id'])? $_POST['category_id']: $question['category_id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        updateQuestion($question_slug,$title, $content, $image, $category_id);
    } 

?>



<div class="container mt-0" style="margin-bottom:80px">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">

                <div class="card-header">
                    <h2> Create question </h2>
                </div>

                <div class="card-body">
                    <form method="POST" action="updateQuestion.php?question_slug=<?= $question['slug'];?>" enctype="multipart/form-data">

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
                                <img class="card-img-top" src="../../../public/uploads/questions/<?= $question['image']; ?>" alt="Card image cap" style="border-radius: 0.5rem;margin:10px;height:350px">
                                <input type="file" name="image" class="form-control <?= (isset($_GET['errorImage']))? 'is-invalid' : ''; ?>"  style="border: none;box-shadow: none;" >
                                <span class="invalid-feedback">
                                    <strong><?= $_GET['errorImage']; ?></strong>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Update Question">
                            <a class="btn btn-secondary" href="showQuestion.php?question_slug=<?= $question_slug; ?>">
                                    Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>




