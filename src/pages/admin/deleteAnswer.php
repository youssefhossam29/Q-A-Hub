<?php 

    session_start();
    if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 1){
        header("LOCATION:../auth/login.php");
    }

    include '../../functions/adminFunctions.php';

    $answer_id = isset($_GET['answer_id']) ? $_GET['answer_id'] : null; 
    $question_slug = isset($_GET['question_slug']) ? $_GET['question_slug'] : null; 
    deleteAnswer($answer_id, $question_slug);

?>