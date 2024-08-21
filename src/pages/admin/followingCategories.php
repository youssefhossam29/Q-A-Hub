<?php 

    session_start();
    if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 1){
        header("LOCATION:../auth/login.php");
    }

    include '../../../assets/layout.php'; 
    include '../../functions/adminFunctions.php';


    if(isset($_GET['successMessage'])) {
        echo '<div class="container mt-4 d-flex justify-content-center">
        <div class="alert alert-success col-md-6 text-center">' . $_GET['successMessage'] . '</div> </div>';
    } 

    if(isset($_GET['errorMessage'])) {
        echo '<div class="container mt-4 d-flex justify-content-center">
        <div class="alert alert-danger col-md-6 text-center">' . $_GET['errorMessage'] . '</div> </div>';
    } 
    

    $start = 0;
    $rows_per_page = 3;
    if(isset($_GET['page-nr'])){
        $page = $_GET['page-nr'] - 1;
        $start = $page * $rows_per_page;
    }

    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    $data = userFollowingCategories($user_id, $start, $rows_per_page);
    $user = $data['user'];
    $categories = $data['categories'];
    $number_of_categories = $user['number_of_followed_categories'];

    if($number_of_categories > 0){
        $number_of_pages = ceil($number_of_categories / $rows_per_page);
    }  
            
?>



<div class="jumbotron container">
        <img class="img-lg rounded-circle" width="130px" height="130px" src="../../../public/uploads/users/<?= $user['photo']; ?>" alt="user_image">
        <h2> 
            <?= $user['name']; ?>
            <?php if($user['admin'] == 0): ?>
                <a class="btn btn-danger" style ='text-decoration: none;' href='./deleteUser.php?user_id=<?= $user['id'] ?>'><i class = 'fas fa-trash'></i> Delete User </a>
            <?php endif;?>
        </h2>
        <hr class="mb-2"> 
        <div class="col- btn btn-light"> <i class="fa-solid fa-envelope"></i>  <?= $user['email'];?> </div>
        <div class="col- btn btn-light"> <i class="fa-solid fa-user"></i> <?= ($user['admin'] == 1)?"Admin":"User";?> </div>
        <div class="col- btn btn-light"> <i class="fa-solid fa-venus-mars"></i>  <?= ($user['gender'] == 1)? "Male":"Female"; ?> </div>      
        <?php if($user['admin'] == 1) die; ?>  
        <a href="showUser.php?user_id=<?= $user_id; ?>" class=" col- btn btn-light"> Number of Questions:  <?= $user['total_questions'];?> </a>
        <a href="#" class=" col- btn btn-light"> Number of following Categories:  <?= $user['number_of_followed_categories'];?> </a>
</div>

<div class="container" style="margin-bottom: 80px;">

    <?php         
        if ($number_of_categories == 0) {
            echo '<div class="container mt-4 d-flex justify-content-center">
            <div class="alert alert-danger col-md-6 text-center"> User has not followed any categories yet</div> </div>';
            die;
        }
    ?>

    <div class="row">
        <?php foreach ($categories as $category): ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <a href="showCategoryQuestions.php?category_id=<?php echo $category['id']; ?>" > 
                            <h5 class="card-title mb-0"><?= ($category['name']); ?></h5>
                        </a>
                    </div>
                    <img src="../../../public/uploads/categories/<?= $category['image']; ?>" class="mx-auto" width="150px" alt="">
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <li class="page-item">
                <a class="page-link" href="?user_id=<?=$user_id;?>&page-nr=<?= (isset($_GET['page-nr']) && $_GET['page-nr'] > 1) ? ($_GET['page-nr'] - 1) : 1 ; ?>">Previous</a>
            </li>

            <?php for($i = 1; $i <= $number_of_pages; $i++): ?>
                <li class="page-item <?= (isset($_GET['page-nr']) && $_GET['page-nr'] == $i) ? 'active' : '' ; ?>">
                    <a class="page-link" href="?user_id=<?=$user_id;?>&page-nr=<?= $i; ?>"><?= $i; ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item">
                <a class="page-link" href="?user_id=<?=$user_id;?>&page-nr=<?= isset($_GET['page-nr']) ? (($_GET['page-nr'] < $number_of_pages) ? $_GET['page-nr'] + 1 : $number_of_pages) : 1 ; ?>">Next</a>
            </li>
        </ul>
    </nav>
</div> 



