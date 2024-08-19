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
    $questions_per_page = 3;
    if(isset($_GET['page-nr'])){
        $page_number = $_GET['page-nr'] - 1;
        $start = $page_number * $questions_per_page;
    }

    $data = homePageContent($start, $questions_per_page);
    $latest_questions = $data['latest_questions'];
    $most_followed_categories = $data['most_followed_categories'];
    $most_question_categories = $data['most_question_categories'];
    $followed_questions = $data['followed_questions'];
    $Followed_questions_size = sizeof($followed_questions);
    if($Followed_questions_size != 0){
        $Followed_questions_size = $followed_questions[0]['total_questions'];
        $number_of_pages = ceil($Followed_questions_size / $questions_per_page);
    }

?>

<style>
    .carousel-control-prev, .carousel-control-next {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        top: 50%;
        transform: translateY(-50%);
    }
    .carousel-item {
        flex-wrap: wrap; 
        justify-content: center;
    }
</style>

<div class="container mt-0" style="margin-bottom:80px">
    <div class="row justify-content-center">

        <?php         
            if (sizeof($most_followed_categories) == 0 && sizeof($most_question_categories) == 0) {
                echo '<div class="container mt-4 d-flex justify-content-center">
                <div class="alert alert-danger col-md-6 text-center"> There is no Categories yet</div> </div>';
                die;
            }
        ?>

        <!-- Popular  categories-->
        <div class="col-md-10" style="margin-bottom:40px">

            <!-- Most Followed categories-->
            <div class="tab-pane" id="categories" role="tabpanel">
                <br><h5 class="text-muted pb-1">Popular Categories</h5>
                <div class="row mt-4">
                    <?php foreach($most_followed_categories as $category): ?>
                        <div class="col-md-6 col-lg-4 grid-margin stretch-card">
                            <div class="card bg-light card-rounded pb-2 text-center" style="border-radius: 20px;">
                                <div class="card-body pb-0 d-flex flex-column align-items-center">
                                    <img src="../../../public/uploads/categories/category.png" width="150px" class="rounded-circle mb-3">
                                    <a href="showCategoryQuestions.php?category_id=<?= $category['id']; ?>" style="text-decoration: none">
                                        <h5 class="card-title card-title-dash mb-4"><?= $category['name'];  ?></h5>
                                    </a>
                                </div>
                            </div><br>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Most Question categories-->
            <div class="tab-pane" id="categories" role="tabpanel">
                <div class="row mt-4">
                    <?php foreach($most_question_categories as $category): ?>
                        <div class="col-md-6 col-lg-4 grid-margin stretch-card">
                            <div class="card bg-light card-rounded pb-2 text-center" style="border-radius: 20px;">
                                <div class="card-body pb-0 d-flex flex-column align-items-center">
                                    <img src="../../../public/uploads/categories/category.png" width="150px" class="rounded-circle mb-3">
                                    <a href="showCategoryQuestions.php?category_id=<?= $category['id']; ?>" style="text-decoration: none">
                                        <h5 class="card-title card-title-dash mb-4"><?= $category['name'];  ?></h5>
                                    </a>
                                </div>
                            </div><br>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                <a href="allCategories.php" class="btn btn-primary" style="border-radius:15px;padding:20px">Explore More Categories</a>
            </div>
        </div>


        <!-- latest questions-->
        <?php 
            $number_of_latest_questions = sizeof($latest_questions);
            if ($number_of_latest_questions == 0) {
                die;
            }
        ?>
        <div class="col-md-10" style="margin-bottom:40px">
            <br><h5 class="text-muted">Latest Questions</h5><br>
            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner" >
                    <?php 
                        $remainingQuestions = $number_of_latest_questions;
                        $questionsPerSlide = 4; 
                        $numOfSlides = ceil($number_of_latest_questions / $questionsPerSlide);

                        for($i = 0; $i < $numOfSlides; $i++):
                    ?>

                    <div class="carousel-item <?= ($i == 0 ? 'active' : '') ?>">
                        <div class="row" <?php echo ($remainingQuestions < 4 || $questionsPerSlide < 4) ? 'style="justify-content: center;"' : '' ?>>
                            <?php 
                                for($j = $i * $questionsPerSlide; $j < ($i + 1) * $questionsPerSlide && $j < $number_of_latest_questions; $j++): 
                            ?>
                                <div class="col-lg-2 question-item">
                                    <img src="../../../public/uploads/questions/question.png" class="img-fluid mb-2" style="max-width: 150px;">
                                    <a href="showQuestion.php?question_slug=<?= $latest_questions[$j]['question_slug']; ?>" style="text-decoration: none">
                                        <p class="card-title card-title-dash mb-4"><?= $latest_questions[$j]['question_title']; ?></p>
                                    </a>
                                </div>
                            <?php endfor; $remainingQuestions -= $questionsPerSlide; ?>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
                
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>


        <!-- Followed Categories Questions -->
        <?php         
            if ($Followed_questions_size == 0) {
                die;
            }
        ?>     
        <div class="col-md-10" style="margin-bottom:40px">
            <br><h5 class="text-muted">Followed Categories Questions</h5><br>
            <div class="row">
                <?php foreach($followed_questions as $question): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card question-card" style=" border-radius: 0.5rem;">
                            <div class="card-body question-content">
                                <a href="showCategoryQuestions.php?category_id=<?= $question['category_id']; ?>" style="color:white"> 
                                    <h4 class="badge bg-secondary" style="font-size: 0.9rem; font-weight: bold; padding: 0.25rem 0.5rem; border-radius: 0.25rem;text-decoration: none;"><?= $question['category_name'];?></h4> 
                                </a>                                    
                                
                                <p class="question-author">by <a href="./showUser.php?user_id=<?=$question['author_id'];?>"><?=$question['author_name'];?></a></p>
                                <hr>

                                <h3 class="question-title">
                                    <a href='./showQuestion.php?question_slug=<?= $question['question_slug'] ?>'><?= $question['question_title']; ?></a>
                                </h3>
                            </div>
                        
                            <img src="../../../public/uploads/questions/<?= $question['question_image']; ?>" class="question-img img-fluid" alt="question Image" style="border-radius: 0.5rem;margin:10px;height:250px">

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

    </div>
</div>
  