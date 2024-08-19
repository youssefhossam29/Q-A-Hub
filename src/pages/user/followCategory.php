<?php 
    
    session_start();
    if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 0){
        header("LOCATION:../auth/login.php");
    }

    include '../../functions/userFunctions.php';

    $category_id = isset($_GET['category_id']) ? ($_GET['category_id']) : 0;
    followCategory($category_id)    
?>
