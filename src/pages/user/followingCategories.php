<?php 
    session_start();
    if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 0){
        header("LOCATION:../auth/login.php");
    }
    include '../../../assets/layout.php'; 
    include '../../functions/userFunctions.php';

    if(isset($_GET['successMessage'])) {
        echo '<div class="container mt-4 d-flex justify-content-center">
        <div class="alert alert-success col-md-6 text-center">' . $_GET['successMessage'] . '</div> </div>';
    } 

    if(isset($_GET['errorMessage'])) {
        echo '<div class="container mt-4 d-flex justify-content-center">
        <div class="alert alert-danger col-md-6 text-center">' . $_GET['errorMessage'] . '</div> </div>';
    }

    $start = 0;
    $rows_per_page = 6;
    if(isset($_GET['page-nr'])){
        $page = $_GET['page-nr'] - 1;
        $start = $page * $rows_per_page;
    }

    $categories = showFollowedCategories('all', $start, $rows_per_page);
    if(sizeof($categories) > 0){
        $number_of_rows = $categories[0]['total_categories'];
        $number_of_pages = ceil($number_of_rows / $rows_per_page);    
    }
    
?>

<div class="container mt-5" style="margin-bottom:80px">
    <h3 class="text-muted pb-1">Followed Categories</h3> <br>

    <div class="row">
        <?php         
            if (sizeof($categories) ==0 ) {
                echo '<div class="container mt-4 d-flex justify-content-center">
                <div class="alert alert-danger col-md-6 text-center"> You have not followed any categories yet</div> </div>';
                die;
            }
        ?>
        <?php foreach ($categories as $category): ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <a href="showCategoryQuestions.php?category_id=<?php echo $category['id']; ?>" > 
                            <h5 class="card-title mb-0"><?= ($category['name']); ?></h5>
                        </a>
                        <a href="unfollowCategory.php?category_id=<?php echo $category['id']; ?>" class="btn btn-danger btn-sm">Unfollow</a>
                    </div>
                    <img src="../../../public/uploads/categories/<?= $category['image']; ?>" class="mx-auto" width="150px" alt="">

                </div>
            </div>
        <?php endforeach; ?>
        </div>

    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <li class="page-item">
                <a class="page-link" href="?page-nr=<?= (isset($_GET['page-nr']) && $_GET['page-nr'] > 1) ? ($_GET['page-nr'] - 1) : 1 ; ?>">Previous</a>
            </li>

            <?php for($i = 1; $i <= $number_of_pages; $i++): ?>
                <li class="page-item <?= (isset($_GET['page-nr']) && $_GET['page-nr'] == $i) ? 'active' : '' ; ?>">
                    <a class="page-link" href="?page-nr=<?= $i; ?>"><?= $i; ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item">
                <a class="page-link" href="?page-nr=<?= isset($_GET['page-nr']) ? (($_GET['page-nr'] < $number_of_pages) ? $_GET['page-nr'] + 1 : $number_of_pages) : 1 ; ?>">Next</a>
            </li>
        </ul>
    </nav>
</div>

