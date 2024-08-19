<?php 

    session_start();
    if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 0){
        header("LOCATION:../auth/login.php");
    }

    include '../../functions/userFunctions.php';
    
    $answer_id = isset($_GET['answer_id']) ? $_GET['answer_id'] : null; 
    $question_slug = isset($_GET['question_slug']) ? $_GET['question_slug'] : null; 
    if(canUserModifyAnswer($answer_id)){
        deleteAnswer($answer_id, $question_slug);
    }else{
        header("Location:./showQuestionAnswers.php?question_slug=$question_slug&errorMessage=Un Authorized");
    }

?>