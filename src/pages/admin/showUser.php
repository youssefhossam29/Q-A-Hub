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
    $data = showUserQuestions($user_id, $start, $rows_per_page);
    $user = $data['user'];
    $questions = $data['questions'];
    $number_of_questions = $user['total_questions'];

    if($number_of_questions > 0){
        $number_of_pages = ceil($number_of_questions / $rows_per_page);
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
        <div class=" col- btn btn-light"> <i class="fa-solid fa-envelope"></i>  <?= $user['email'];?> </div>
        <div class=" col- btn btn-light"> <i class="fa-solid fa-user"></i> <?= ($user['admin'] == 1)?"Admin":"User";?> </div>
        <div class=" col- btn btn-light"> <i class="fa-solid fa-venus-mars"></i>  <?= ($user['gender'] == 1)? "Male":"Female"; ?> </div>      
        <?php if($user['admin'] == 1) die; ?>
        <a href="#" class=" col- btn btn-light"> Number of Questions:  <?= $user['total_questions'];?> </a>
        <a href="followingCategories.php?user_id=<?= $user_id; ?>" class=" col- btn btn-light"> Number of following Categories:  <?= $user['number_of_followed_categories'];?> </a>
</div>
                                  

<div class="container" style="margin-bottom: 80px;">
    <?php         
        if ($number_of_questions == 0) {
            echo '<div class="container mt-4 d-flex justify-content-center">
            <div class="alert alert-danger col-md-6 text-center"> There is no Questions</div> </div>';
            die;
        }
    ?>

    <div class="row">
        <?php foreach($questions as $question): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card question-card" style=" border-radius: 0.5rem;">
                    <div class="card-body question-content">
                        <a href="showCategoryQuestions.php?category_id=<?= $question['category_id']; ?>" style="color:white"> 
                            <h4 class="badge bg-secondary" style="font-size: 0.9rem; font-weight: bold; padding: 0.25rem 0.5rem; border-radius: 0.25rem;text-decoration: none;"><?= $question['category_name'];?></h4> 
                        </a>                                    
                        
                        <p class="question-author">by <a href="./showUser.php?user_id=<?=$user['id'];?>"><?=$user['name'];?></a></p> 
                        <hr>
 
                        <a href='./showQuestion.php?question_slug=<?= $question['slug'] ?>'> <h3 class="question-title"><?= $question['title']; ?></h3></a>

                    </div>
                
                    <img src="../../../public/uploads/questions/<?= $question['image']; ?>" class="question-img img-fluid" alt="question Image" style="border-radius: 0.5rem;margin:10px; height:250px">

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



