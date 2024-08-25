<?php 

    session_start();
    if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 1){
        header("LOCATION:../auth/login.php");
    }

    include '../../../assets/layout.php'; 
    include '../../functions/adminFunctions.php';

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
    $question = showQuestion($question_slug);
?>

<div class="container mt-0" style="margin-bottom:80px">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card question-card" style=" border-radius: 0.5rem;">

                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a href="showCategoryQuestions.php?category_id=<?= $question['category_id']; ?>" style="color:white"> 
                                <h4 class="badge bg-secondary" style="font-size: 0.9rem; font-weight: bold; padding: 0.25rem 0.5rem; border-radius: 0.25rem;text-decoration: none;"><?= $question['category_name'];?></h4> 
                            </a>                                    
                            <h4>by <a href="./showUser.php?user_id=<?=$question['author_id'];?>"><?=$question['author_name'];?></a></h4>
                            
                        </li>
                        

                        <li class="nav-item ml-auto mr-3">
                            <a href="./showQuestionAnswers.php?question_slug=<?=$question['slug'];?>" class="btn btn-secondary"><i class="fa-solid fa-comment"></i> Answers</a>
                            <a href="./deleteQuestion.php?question_slug=<?=$question['slug'];?>" class="btn btn-danger"><i class = 'fas fa-trash fa-lg'></i> Delete</a>
                        </li>
                    </ul>
                    <br>
                </div>

                <div class="card-body question-content">
                    <h3 class="question-title"><?= $question['title']; ?></h3>

                    <p class="card-text"><?= $question['content'];?></p>
                    <img class="card-img-top" src="../../../public/uploads/questions/<?= $question['image']; ?>" alt="Card image cap" style="border-radius: 0.5rem;margin:10px;height:300px">
                </div>
            </div>
        </div>
    </div>
                
</div>



