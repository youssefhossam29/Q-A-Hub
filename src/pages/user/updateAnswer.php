<?php 

    session_start();
    if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 0){
        header("LOCATION:../auth/login.php");
    }

    include '../../../assets/layout.php'; 
    include '../../functions/userFunctions.php';

    if(isset($_GET['successMessage'])) {
        echo '<div class="container mt-4 d-flex justify-content-center">
        <div class="alert alert-success col-md-6 text-center">' . $_GET['successMessage'] . '</div> </div>';
    } 

    if(isset($_GET['errorMessage'])) {
        echo '<div class="container mt-4 d-flex justify-content-center">
        <div class="alert alert-danger col-md-6 text-center">' . $_GET['errorMessage'] . '</div> </div>';
    } 


    $answer_id = isset($_GET['answer_id']) ? intval($_GET['answer_id']) : null; 
    $question_slug = isset($_GET['question_slug']) ? intval($_GET['question_slug']) : null; 
    if(!canUserModifyAnswer($answer_id)){
        header("Location:./showQuestionAnswers.php?question_slug=$question_slug&errorMessage=Un Authorized");
        die;
    }


    $answer = showAnswer($answer_id, $question_slug); 
    $content = isset($_POST['content']) ? $_POST['content'] : $answer['answer_content'];
    $errorContent = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty(trim($content))) {
            $errorContent = "Answer cannot be empty.";
        } else {
            updateAnswer($answer_id, $content, $answer['question_slug'] );
        }
    }
?>

<div class="container mt-10" style="margin-bottom:80px">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <a href='./showQuestion.php?question_slug=<?= $answer['question_slug']; ?>'> <h3> <?= $answer['question_title'] ?> </h3></a>
                </div>
                <div class="card-body">
                    <div class="mt-4">
                        <div class="d-flex align-items-start">
                            <div class="alert alert-secondary mt-2" style="border-radius:15px;width:650px">
                                <form action="updateAnswer.php?answer_id=<?= $answer_id;?>" method="POST">

                                    <div class="form-group">
                                        <input type="text" name="content" class="form-control <?= (!empty($errorContent)) ? 'is-invalid' : ''; ?>" value='<?= $content;?>' autofocus style="background-color:transparent;border: none;box-shadow: none;">
                                        <?php if (!empty($errorContent)): ?>
                                            <div class="invalid-feedback"><?= $errorContent; ?></div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary mt-2">Update Answer</button>
                                    </div>  
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
