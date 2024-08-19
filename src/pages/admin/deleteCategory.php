<?php 

    session_start();
    if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 1){
        header("LOCATION:../auth/login.php");
    }

    include '../../functions/adminFunctions.php';

    $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
    deleteCategory($category_id);

?>