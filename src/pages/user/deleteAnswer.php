<?php 

    session_start();
    if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 0){
        header("LOCATION:../auth/login.php");
    }

    include '../../functions/userFunctions.php';
    
    $question_slug = isset($_GET['question_slug']) ? $_GET['question_slug'] : null;
    $answer_id = isset($_GET['answer_id']) ? intval($_GET['answer_id']) : 0;
    if ($question_slug == null) {
        $_SESSION['errorMessage'] = "Invalid Answer";
        $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "allQuestions.php";
        header("Location: $redirectUrl");            
        die;
    }elseif($answer_id <= 0){
        $_SESSION['errorMessage'] = "Invalid Answer";
        $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "showQuestionAnswers.php?question_slug=$question_slug";
        header("Location: $redirectUrl");            
        die;
    }elseif(!canUserModifyAnswer($answer_id)){
        $_SESSION['errorMessage'] = "Un Authorized";
        $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "showQuestionAnswers.php?question_slug=$question_slug";
        header("Location: $redirectUrl");            
        die;
    }else{
        deleteAnswer($answer_id, $question_slug);
    }

?>