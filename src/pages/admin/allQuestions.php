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

        $questions = showQuestions($start, $rows_per_page) ;
        $number_of_questions = sizeof($questions);
        if($number_of_questions > 0){
            $number_of_questions = $questions[0]['total_questions'];
            $number_of_pages = ceil($number_of_questions / $rows_per_page);
        }
    ?>

    <div class="container-fluid" style="margin-bottom:80px">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <h2 class="card-title">Questions Details</h2>
                </li>
            </ul> 
        </div>
        <div class="card-body">
            <?php         
                if (sizeof($questions) == 0) {
                    echo '<div class="container mt-4 d-flex justify-content-center">
                    <div class="alert alert-danger col-md-6 text-center"> There is no Questions yet</div> </div>';
                    die;
                }
            ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Author</th>
                        <th scope="col">Title</th>
                        <th scope="col">Image</th>
                        <th scope="col">Category</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    foreach($questions as $question): 
                    ?>  
                    <tr>
                        <td scope="row"> <?= $i++; ?> </td>
                        <td scope="row"> 
                            <a style ='text-decoration: none;' href='./showUser.php?user_id=<?= $question['author_id'] ?>'><?= $question['author_name']; ?> </a>
                        </td>
                        <td scope="row"> <?= $question['title']; ?> </td>
                        <td scope="row"> <img src="../../../public/uploads/questions/<?= $question['image'] ?>" alt="" class="img-tumbnail" width="60">  </td>
                        <td scope="row"> 
                            <a style ='text-decoration: none;' href='./showCategoryQuestions.php?category_id=<?= $question['category_id'] ?>'><?= $question['category_name']; ?> </a>
                        </td>
                        <td scope="row">
                            <a href="./showQuestion.php?question_slug=<?=$question['slug'];?>" class="btn btn-secondary"><i class = 'fas fa-eye'></i> Show Details</a>                            
                        </td>
                    </tr>
                    <?php 
                        endforeach;    
                    ?>
                </tbody>
            </table>
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
    </div>