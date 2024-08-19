<?php 
    
    session_start();
    if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 0){
        header("LOCATION:../auth/login.php");
    }

    include '../../functions/userFunctions.php';

    $question_slug = isset($_GET['question_slug']) ? ($_GET['question_slug']) : null;
    if(canUserModifyQuestion($question_slug)){
        deleteQuestion($question_slug);
    }else{
        header("Location:./allQuestions.php?errorMessage=Un Authorized");
    }
    
?>
