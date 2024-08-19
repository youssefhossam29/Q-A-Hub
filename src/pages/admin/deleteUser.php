<?php 
    
    session_start();
    if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 1){
        header("LOCATION:../auth/login.php");
    }

    include '../../functions/adminFunctions.php';
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    deleteUser($user_id);
    
?>
