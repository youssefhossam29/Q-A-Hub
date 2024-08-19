<!DOCTYPE html>
<html>
<head>
    <title>Q&A</title>
    <link rel="icon" type="image/png" href="../../../assets/img/logo1.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .text-center img {max-width: 50px;}
        .nav-item1{border-right: 1px solid #ddd;}
        .nav-link {color: #333; padding: 10px 15px; border: none; background-color: transparent;}
        .question-item {margin-left:62px;padding: 15px; border-radius: 15px; text-align: center;}
        .question-item img {width: 80px; height: 80px; border-radius: 10px;}
    </style>
</head>
<body>  
    <nav class="navbar navbar-expand-lg ">
        <div class="img-lg rounded-circle">
            <a class="navbar-brand" href="#">
                <img height="100px" src="../../../assets/img/logo1.png" alt="logo">
            </a>
        </div>
        
        <button class="navbar-toggler navbar-light bg-light" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <?php 
                    if(isset($_SESSION['userdata']) ){
                        if($_SESSION['userdata']['admin'] ==0){
                            include 'navbar/user.php';
                        }else{
                            include 'navbar/admin.php';
                        }
                    }else{ 
                        include 'navbar/auth.php';
                    }
                ?>
        </div>
    </nav>


    <footer class="footer fixed-bottom bg-light text-black text-center py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <span>&copy; 2024 Q&A. All rights reserved.</span>
            <div class="social-icons">
                <a href="#" aria-label="Twitter" style="text-decoration:none"><i class="fa-brands fa-x-twitter fa-lg"></i>&nbsp;&nbsp;&nbsp;&nbsp;</a>
                <a href="#" aria-label="Facebook" style="text-decoration:none"><i class="fab fa-facebook fa-lg"></i>&nbsp;&nbsp;&nbsp;&nbsp;</a>
                <a href="#" aria-label="Instagram" style="text-decoration:none"><i class="fa-brands fa-instagram fa-lg"></i>&nbsp;&nbsp;</a>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
