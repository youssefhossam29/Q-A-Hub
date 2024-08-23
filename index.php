<?php 

    session_start();
    if(isset($_SESSION['userdata'])){
        if($_SESSION['userdata']['admin'] == 0){
            header("LOCATION:src/pages/user/home.php");
            die;
        }
        else{
            header("LOCATION:src/pages/admin/home.php");
        }
    }
?>


<!DOCTYPE html>
<head>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Q&A Hub</title>  
    <link rel="icon" type="image/png" href="../../../assets/img/logo1.png">

</head>

<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="container text-center">
        <div class="image mb-0">
            <img class="img" style="d-block ;width:300px" src="./assets/img/logo1.png" alt="Logo">
        </div>

        <div class="col-12 pb-5">
            <a class="btn btn-outline-secondary btn-icon-text py-4" href="src/pages/auth/login.php">
                <h6 class="d-inline-block text-left">Ask, learn, and get answers <i class="fa fa-arrow-circle-right"></i></h6>
            </a>
        </div>
    </div>
</body>



</html>





