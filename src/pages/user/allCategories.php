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
    $rows_per_page = 6;
    if(isset($_GET['page-nr'])){
        $page = $_GET['page-nr'] - 1;
        $start = $page * $rows_per_page;
    }

    $type = isset($_GET['type']) ? $_GET['type'] : "Latest";
    $categories = showCategories($start, $rows_per_page, $type);
    if(sizeof($categories)){
        $number_of_rows = $categories[0]['total_categories'];
        $number_of_pages = ceil($number_of_rows / $rows_per_page);
        $followed_categories_ids = isset($_SESSION['followedCategoriesIds'])? $_SESSION['followedCategoriesIds']:[];
    }
    
?>



<div class="container mt-5" style="margin-bottom:80px">    
    
<div class="card-header">

        <ul class="nav nav-tabs card-header-tabs" style="margin:1px">
            <li class="nav-item">
                <a class="btn btn-secondary dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false" > <h4>Categories Details</h4></a>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink" style="text-align: center">
                    <li><a href="./allCategories.php?type=Latest" >Latest</a></li>
                    <li><a href="./allCategories.php?type=Most Questions" >Most Questions</a></li>
                    <li><a href="./allCategories.php?type=Most Followed" >Most Followed</a></li>
                </ul>
            </li>
        </ul>
    
</div>
    
    <br>

    <div class="row">
        <?php         
            if (sizeof($categories) == 0 ) {
                echo '<div class="container mt-4 d-flex justify-content-center">
                <div class="alert alert-danger col-md-6 text-center"> There is no Categories yet</div> </div>';
                die;
            }
        ?>
        <?php foreach ($categories as $category): ?>
            <div class="col-md-4 mb-3">
                <div class="card" style="height: 230px;">
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
                    <img src="../../../public/uploads/categories/<?= $category['image']; ?>" class="mx-auto" width="150px" height="150px" alt="" style="margin:10px">

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