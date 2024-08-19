<?php 
    
    session_start();
    if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 1){
        header("LOCATION:../auth/login.php");
    }

    include '../../functions/adminFunctions.php';

    $question_slug = isset($_GET['question_slug']) ? ($_GET['question_slug']) : null;
    deleteQuestion($question_slug);
    
?>
