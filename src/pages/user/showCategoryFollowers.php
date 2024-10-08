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
 

    $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

    $start = 0;
    $rows_per_page = 3;
    if(isset($_GET['page-nr'])){
        $page = $_GET['page-nr'] - 1;
        $start = $page * $rows_per_page;
    }

    $data = showCategoryFollowers($category_id, $start, $rows_per_page); 
    $category = $data['category'];
    $users = $data['users'];
    $number_of_questions = $category['total_questions'];
    $number_of_followers = $category['total_followers'];
    $followed_categories_ids = isset($_SESSION['followedCategoriesIds']) ? $_SESSION['followedCategoriesIds'] : [];
    $number_of_pages = ceil($number_of_followers / $rows_per_page);
?>

   
<div class="jumbotron container">
        <img class="img-lg rounded-circle" width="130px" height="130px" src="../../../public/uploads/categories/<?= $category['image']; ?>" alt="category_image">
        <h2> 
            <?= $category['name']; ?>
            <?php if (in_array($category['id'], $followed_categories_ids)): ?>
                <a href="unfollowCategory.php?category_id=<?php echo $category_id; ?>" class="btn btn-danger"><i class="fa-solid fa-minus"></i> Unfollow</a>
            <?php else: ?>
                <a href="followCategory.php?category_id=<?php echo $category_id; ?>" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Follow</a>
            <?php endif; ?>
        </h2>
        <hr class="mb-2">
        <a href="showCategoryQuestions.php?category_id=<?= $category_id; ?>" class=" col-lg-4 btn btn-light"> Number of Questions:  <?= $number_of_questions;?> </a>
        <a href="#" class=" col-lg-4 btn btn-light"> Number of Followers:  <?= $number_of_followers;?> </a>
</div>

<div class="container" style="margin-bottom: 80px;">

    <?php         
        if ($number_of_followers == 0) {
            echo '<div class="container mt-4 d-flex justify-content-center">
            <div class="alert alert-danger col-md-6 text-center"> There is no Followers</div> </div>';
            die;
        }
    ?>

    <div class="row">
        <?php foreach($users as $user): ?>
            <div class="col-md-6 col-lg-4 grid-margin stretch-card">
                <div class="card card-rounded pb-2" style="border-radius: 15px;">
                    <div class="card-body pb-0">
                        <a href="showUser.php?user_id=<?= $user['user_id'];?>" style="text-decoration: none">
                            <h5 class="card-title card-title-dash mb-4"> <?= $user['user_name'];?></h5>
                        </a>  
                        <img class="img-lg fluid" width="130px" height="130px" src="../../../public/uploads/users/<?= $user['user_photo']; ?>" alt="user_image">
                                                                                                  
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item">
                    <a class="page-link" href="?category_id=<?= $category['id'];?>&page-nr=<?= (isset($_GET['page-nr']) && $_GET['page-nr'] > 1) ? ($_GET['page-nr'] - 1) : 1 ; ?>">Previous</a>
                </li>

                <?php for($i = 1; $i <= $number_of_pages; $i++): ?>
                    <li class="page-item <?= (isset($_GET['page-nr']) && $_GET['page-nr'] == $i) ? 'active' : '' ; ?>">
                        <a class="page-link" href="?category_id=<?= $category['id'];;?>&page-nr=<?= $i; ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item">
                    <a class="page-link" href="?category_id=<?= $category['id'];;?>&page-nr=<?= isset($_GET['page-nr']) ? (($_GET['page-nr'] < $number_of_pages) ? $_GET['page-nr'] + 1 : $number_of_pages) : 1; ?>">Next</a>
                </li>
            </ul>
    </nav>
</div> 



