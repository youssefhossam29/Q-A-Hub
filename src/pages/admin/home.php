<?php 
    session_start();
    if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['admin'] != 1){
        header("LOCATION:../auth/login.php");
    }

    include '../../../assets/layout.php'; 
    include '../../functions/adminFunctions.php';

    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'overview';
    switch ($active_tab) {
        case 'overview':
            $active_tab = 'overview';
            $stats = generalStat();
            break;
        case 'categories':
            $active_tab = 'categories';
            $categories = categoriesStat(); 
            break;
        case 'questions':
            $active_tab = 'questions';
            $questions = showQuestions(0,3);
            break;
        case 'users':
            $active_tab = 'users';
            $users = usersStat(); 
            break;
        default:
            die('Error');
            break;
    }


    if(isset($_GET['successMessage'])) {
        echo '<div class="container mt-4 d-flex justify-content-center">
        <div class="alert alert-success col-md-6 text-center">' . $_GET['successMessage'] . '</div> </div>';
    } 

    if(isset($_GET['errorMessage'])) {
        echo '<div class="container mt-4 d-flex justify-content-center">
        <div class="alert alert-danger col-md-6 text-center">' . $_GET['errorMessage'] . '</div> </div>';
    } 
?>

<style>
    .center-content {
        display: flex; 
        flex-direction: column;  
        align-items: center; 
        justify-content: center; 
        text-align: center; 
    }
</style>

<div class="container">
    <!-- <div class="text-center mt-4">
        <h1 class="mt-2">question</h1>
        <p class="lead">Let's Start question</p>
    </div>  -->

    <div class="d-sm-flex align-items-center justify-content-between border-bottom">
        <ul class="nav nav-tabs mt-4" id="myTab" role="tablist" style="border-bottom: 0px solid #ddd;">
            <li class="nav-item1">
                <a class="nav-link <?= $active_tab == 'overview' ? 'active' : ''; ?>" href="?tab=overview" role="tab">
                    Overview 
                </a>
            </li>
            <li class="nav-item1">
                <a class="nav-link <?= $active_tab == 'categories' ? 'active' : ''; ?>" href="?tab=categories" role="tab">
                    Categories
                </a>
            </li>
            <li class="nav-item1">
                <a class="nav-link <?= $active_tab == 'questions' ? 'active' : ''; ?>" href="?tab=questions" role="tab">
                    Questions
                </a>
            </li>
            <li class="nav-item1">
                <a class="nav-link <?= $active_tab == 'users' ? 'active' : ''; ?>" href="?tab=users" role="tab">
                    Users
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content tab-content-basic mt-4">
        <div class="tab-pane fade <?php echo $active_tab == 'overview' ? 'show active' : ''; ?>" id="overview" role="tabpanel">
            <div class="row">
                <div class="col-sm-12">
                    <div class="statistics-details d-flex align-items-center justify-content-between">

                        <div class="center-content">
                            <a href="allCategories.php" class="text-secondary" style="text-decoration: none;">Total Categories</a><br>
                            <h3 class="rate-percentage"><?= $stats['total_categories'] ?></h3>
                            <p class="text-danger"><i class="fa-solid fa-chart-pie"></i></p>
                        </div>

                        <div class="center-content">
                            <a href="allQuestions.php" class="text-secondary" style="text-decoration: none;">Total Questions</a><br>
                            <h3 class="rate-percentage"><?= $stats['total_questions'] ?></h3>
                            <p class="text-danger"><i class="fa-solid fa-newspaper"></i></p>
                        </div>

                        <div class="center-content">
                            <a href="allUsers.php" class="text-secondary" style="text-decoration: none;">Total Users</a><br>
                            <h3 class="rate-percentage"><?= $stats['total_users'] ?></h3>
                            <p class="text-danger"><i class="fa-solid fa-user"></i></p>
                        </div>

                    </div>
                </div>
            </div>
            <hr>
        </div>

        <!-- Categories Tab -->
        <div class="tab-pane fade <?php echo $active_tab == 'categories' ? 'show active' : ''; ?>" id="categories" role="tabpanel" style="margin-bottom:120px">
            <h4 class="text-muted pb-1">Categories</h4>
            
            <div class="row">
                <div class="col-lg-3 m-auto text-center col-sm-12" >
                    <div class="statistics-details d-flex align-items-center justify-content-between">
                        <div class="card card-body bg-light" style="border-radius: 15px;">
                            <a href="allCategories.php" class="text-secondary" style="text-decoration: none;">Total Categories: </a>
                            <h3 class="rate-percentage"><?= $categories[0]['total_categories'] ?></h3>
                        </div>
                    </div>
                 </div>
            </div>

            <br>
            <h5 class="text-muted pb-1">Latest Categories</h5>

            <div class="row mt-4">
                <?php foreach($categories['latest'] as $category): ?>
                    <div class="col-md-6 col-lg-4 grid-margin stretch-card">
                        <div class="card bg card-rounded pb-2" style="border-radius: 15px;">
                            <div class="card-body pb-0">
                                <a href="showCategoryQuestions.php?category_id=<?= $category['id']; ?>" style="text-decoration: none">
                                    <h5 class="card-title card-title-dash mb-4"><?= $category['name'];  ?></h5>
                                </a>

                                <img src="../../../public/uploads/categories/<?= $category['image']; ?>" class="mx-auto"alt="" style="max-height:150px;margin:10px;border-radius:10px">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>


            <br><br><br>
            <h5 class="text-muted pb-1">Popular Categories</h5>

            <div class="row mt-4">
                <?php foreach($categories['most_question'] as $category): ?>
                    <div class="col-md-6 col-lg-4 grid-margin stretch-card">
                        <div class="card bg card-rounded pb-2" style="border-radius: 15px;">
                            <div class="card-body pb-0">
                                <a href="showCategoryQuestions.php?category_id=<?= $category['id']; ?>" style="text-decoration: none">
                                    <h5 class="card-title card-title-dash mb-4"><?= $category['name'];  ?></h5>
                                </a>

                                <img src="../../../public/uploads/categories/<?= $category['image']; ?>" class="mx-auto"alt="" style="max-height:150px;margin:10px;border-radius:10px">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <br><br><br>
            <h5 class="text-muted pb-1">Most Following Categories</h5>

            <div class="row mt-4">
                <?php foreach($categories['most_followed'] as $category): ?>
                    <div class="col-md-6 col-lg-4 grid-margin stretch-card">
                        <div class="card bg card-rounded pb-2" style="border-radius: 15px;">
                            <div class="card-body pb-0">
                                <a href="showCategoryQuestions.php?category_id=<?= $category['id']; ?>" style="text-decoration: none">
                                    <h5 class="card-title card-title-dash mb-4"><?= $category['name'];  ?></h5>
                                </a>

                                <img src="../../../public/uploads/categories/<?= $category['image']; ?>" class="mx-auto"alt="" style="max-height:150px;margin:10px;border-radius:10px">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Questions Tab -->
        <div class="tab-pane fade <?php echo $active_tab == 'questions' ? 'show active' : ''; ?>" id="questions" role="tabpanel" style="margin-bottom:120px">
            <h4 class="text-muted pb-1">Questions</h4>
            
            <div class="row">
                <div class="col-lg-3 m-auto text-center col-sm-12" >
                    <div class="statistics-details d-flex align-items-center justify-content-between">
                        <div class="card card-body bg-light" style="border-radius: 15px;">
                            <a href="allQuestions.php" class="text-secondary" style="text-decoration: none;">Total Questions: </a>
                            <h3 class="rate-percentage"><?= $questions[0]['total_questions']; ?></h3>
                        </div>
                    </div>
                 </div>
            </div>

            <br>
            <h5 class="text-muted pb-1">Latest Questions</h5>

            <div class="row mt-4">
                <?php foreach($questions as $question): ?>
                    <div class="col-md-6 col-lg-4 grid-margin stretch-card">
                        <div class="card card-rounded pb-2" style="border-radius: 15px;">
                            <div class="card-body pb-0">
                                <a href="showCategoryQuestions.php?category_id=<?= $question['category_id']; ?>" style="color:white"> 
                                    <h4 class="badge bg-secondary" style="font-size: 0.9rem; font-weight: bold; padding: 0.25rem 0.5rem; border-radius: 0.25rem;text-decoration: none;"><?= $question['category_name'];?></h4> 
                                </a>                                    
                                <p class="question-author">by <a href="./showUser.php?user_id=<?=$question['author_id'];?>"><?=$question['author_name'];?></a></p>
                                <hr>
                                                                
                                <a href='./showQuestion.php?question_slug=<?= $question['slug'] ?>'><h3 class="card-title card-title-dash mb-4"><?= $question['title']; ?></h3></a>
                            </div>
                            <img src="../../../public/uploads/questions/<?= $question['image']; ?>" class="question-img img-fluid" alt="question Image" style="border-radius: 0.5rem;margin:10px;height:250px">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>        
        </div>

        <!-- Users Tab -->
        <div class="tab-pane fade <?php echo $active_tab == 'users' ? 'show active' : ''; ?>" id="users" role="tabpanel" style="margin-bottom:120px">
            <h4 class="text-muted pb-1">Users</h4>
            
            <div class="row">
                <div class="col-lg-3 m-auto text-center col-sm-12" >
                    <div class="statistics-details d-flex align-items-center justify-content-between">
                        <div class="card card-body bg-light" style="border-radius: 15px;">
                            <a href="allUsers.php" class="text-secondary" style="text-decoration: none;">Total Users: </a>
                            <h3 class="rate-percentage"><?= $users[0]['total_users']; ?></h3>
                        </div>
                    </div>
                 </div>
            </div>

            <br>
            <h5 class="text-muted pb-1">Latest Users</h5>

            <div class="row mt-4">
                <?php foreach($users['latest'] as $user): ?>
                    <div class="col-md-6 col-lg-4 grid-margin stretch-card">
                        <div class="card card-rounded pb-2" style="border-radius: 15px;">
                            <div class="card-body pb-0">
                                <a href="showUser.php?user_id=<?= $user['id'];?>" style="text-decoration: none">
                                    <h5 class="card-title card-title-dash mb-4"> <?= $user['name'];?></h5>
                                </a>  
                                <img class="img-lg fluid" width="130px" height="130px" src="../../../public/uploads/users/<?= $user['photo']; ?>" alt="user_image">
                                                                                                  
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>   
            
            
            <br><br><br>
            <h5 class="text-muted pb-1">Popular Users</h5>

            <div class="row mt-4">
                <?php foreach($users['most_question'] as $user): ?>
                    <div class="col-md-6 col-lg-4 grid-margin stretch-card">
                        <div class="card card-rounded pb-2" style="border-radius: 15px;">
                            <div class="card-body pb-0">
                                <a href="showUser.php?user_id=<?= $user['id'];?>" style="text-decoration: none">
                                    <h5 class="card-title card-title-dash mb-4"> <?= $user['name'];?></h5>
                                </a>  
                                <img class="img-lg fluid" width="130px" height="130px" src="../../../public/uploads/users/<?= $user['photo']; ?>" alt="user_image">
                                                                                                  
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
