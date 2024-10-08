<?php 

    session_start();
    if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 0){
        header("LOCATION:../auth/login.php");
    }

    include '../../../assets/layout.php'; 
    include '../../functions/userFunctions.php';


    if(isset($_SESSION['successMessage'])){
        echo '<div class="container mt-4 d-flex justify-content-center">
        <div class="alert alert-success col-md-6 text-center">' . $_SESSION['successMessage'] . '</div></div>';
        unset($_SESSION['successMessage']);
    }
    
    if(isset($_SESSION['errorMessage'])){
        echo '<div class="container mt-4 d-flex justify-content-center">
        <div class="alert alert-danger col-md-6 text-center">' . $_SESSION['errorMessage'] . '</div></div>';
        unset($_SESSION['errorMessage']);
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
        $followed_categories_ids = isset($_SESSION['followedCategoriesIds'])? $_SESSION['followedCategoriesIds']:[];
    }  
            
?>



<div class="jumbotron container">
        <img class="img-lg rounded-circle" width="130px" height="130px" src="../../../public/uploads/users/<?= $user['photo']; ?>" alt="user_image">
        <h1> <?= $user['name']; ?></h1>
        <hr class="mb-2">   
        <a href="showUser.php?user_id=<?= $user_id; ?>" class="col-lg-4 btn btn-light"> Number of Questions:  <?= $user['total_questions'];?> </a>
        <a href="#" class="col-lg-4 btn btn-light"> Number of following Categories:  <?= $user['number_of_followed_categories'];?> </a>
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
                        <?php if (in_array($category['id'], $followed_categories_ids)): ?>
                            <a href="unfollowCategory.php?category_id=<?php echo $category['id']; ?>" class="btn btn-danger"><i class="fa-solid fa-minus"></i> Unfollow</a>
                        <?php else: ?>
                            <a href="followCategory.php?category_id=<?php echo $category['id']; ?>" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Follow</a>
                        <?php endif; ?>
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



