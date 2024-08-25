<?php 
    
    session_start();
    if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 0){
        header("LOCATION:../auth/login.php");
    }

    include '../../functions/userFunctions.php';

    $question_slug = isset($_GET['question_slug']) ? ($_GET['question_slug']) : null;
    if ($question_slug == null) {
        $_SESSION['errorMessage'] = "Invalid Question";
        $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "allQuestions.php";
        header("Location: $redirectUrl");            
        die;
    }elseif(!canUserModifyQuestion($question_slug)){
        $_SESSION['errorMessage'] = "Un Authorized";
        $redirectUrl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "allQuestions.php";
        header("Location: $redirectUrl");            
        die;
    }else{
        deleteQuestion($question_slug);
    }
    
?>
