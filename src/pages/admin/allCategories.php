    <?php 

        session_start();
        if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 1){
            header("LOCATION:../auth/login.php");
        }
        include '../../../assets/layout.php'; 
        include '../../functions/adminFunctions.php';

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
        $rows_per_page = 4;
        if(isset($_GET['page-nr'])){
            $page = $_GET['page-nr'] - 1;
            $start = $page * $rows_per_page;
        }

        $type = isset($_GET['type']) ? $_GET['type'] : "Latest";
        $categories = showCategories($start, $rows_per_page, $type);
        
        $number_of_categories = sizeof($categories);
        if($number_of_categories > 0){
            $number_of_categories = $categories[0]['total_categories'];
            $number_of_pages = ceil($number_of_categories / $rows_per_page);
        }
    ?>


<div class="container-fluid" style="margin-bottom:80px">
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

            <li class="nav-item ml-auto mr-3">
                <a href="./createCategory.php" class="btn btn-success">Create new category</a>
            </li>
        </ul>
    </div>

    <?php         
        if (sizeof($categories) ==0 ) {
            echo '<div class="container mt-4 d-flex justify-content-center">
            <div class="alert alert-danger col-md-6 text-center"> There is no Categories</div> </div>';
            die;
        }
    ?>

    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Image</th>
                    <th scope="col"># of questions</th>
                    <th scope="col"># of followers</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $i = 1;
                    foreach($categories as $category): 
                ?>  
                <tr>
                    <td scope="row"> <?= $i++; ?> </td>
                    <td scope="row"> <?= $category['name'] ?> </td>
                    <td scope="row"> <img src="../../../public/uploads/categories/<?= $category['image'] ?>" alt="" class="img-tumbnail" width="60">  </td>
                    <td scope="row"> <?= $category['total_questions'] ?> </td>
                    <td scope="row"> <?= $category['total_followers'] ?> </td>
                    <td scope="row">
                        <a href="./showCategoryQuestions.php?category_id=<?=$category['id'];?>" class="btn btn-secondary"><i class = 'fas fa-eye'></i> Show Details</a>  
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item">
                    <a class="page-link" href="?type=<?= isset($_GET['type']) ? $_GET['type']: "Latest"; ?>&page-nr=<?= (isset($_GET['page-nr']) && $_GET['page-nr'] > 1) ? ($_GET['page-nr'] - 1) : 1 ; ?>">Previous</a>
                </li>

                <?php for($i = 1; $i <= $number_of_pages; $i++): ?>
                    <li class="page-item <?= (isset($_GET['page-nr']) && $_GET['page-nr'] == $i) ? 'active' : '' ; ?>">
                        <a class="page-link" href="?type=<?= isset($_GET['type']) ? $_GET['type']: "Latest"; ?>&page-nr=<?= $i; ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item">
                    <a class="page-link" href="?type=<?= isset($_GET['type']) ? $_GET['type']: "Latest"; ?>&page-nr=<?= isset($_GET['page-nr']) ? (($_GET['page-nr'] < $number_of_pages) ? $_GET['page-nr'] + 1 : $number_of_pages) : 1; ?>">Next</a>
                </li>
            </ul>
        </nav>

    </div>
</div>



