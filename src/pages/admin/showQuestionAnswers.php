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
    $rows_per_page = 2;
    if(isset($_GET['page-nr'])){
        $page = $_GET['page-nr'] - 1;
        $start = $page * $rows_per_page;
    }

    $question_slug = isset($_GET['question_slug']) ? ($_GET['question_slug']) : null;
    $data = showQuestionAnswers($question_slug, $start, $rows_per_page);
    $question = $data['question'];
    $answers = $data['answers'];

    $number_of_rows = $question['total_answers'];
    $number_of_pages = ceil($number_of_rows / $rows_per_page);
?>

<div class="container mt-10" style="margin-bottom:80px">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <a href='./showQuestion.php?question_slug=<?= $question['question_slug']; ?>'> <h3> <?= $question['question_title']; ?> </h3></a>
                </div>
                <div class="card-body">
                    <?php         
                        if ($number_of_rows <= 0) {
                            echo '<div class="alert alert-danger" role="alert"> There is no Answers yet! </div>';
                            die;
                        }
                    ?>
                    <?php foreach($answers as $answer): ?>
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <img class="rounded-circle" width="60px" src="../../../public/uploads/users/<?= $answer['answer_author_photo'] ?>" alt="user_image">
                            </div>
                            <div class="mt-2 p-1">
                                <div class="alert alert-secondary" style="border-radius:15px;max-width:600px;margin-bottom:-5px">
                                    <h6 class="mb-0"><a href="./showUser.php?user_id=<?=$answer['answer_author_id'];?>"><?=$answer['answer_author_name'];?></a></h6> 
                                    <?= $answer['answer_content']; ?>
                                </div>
                                <div class="mt-2" >
                                    <small style="margin-left:15px;" ><a href="deleteAnswer.php?answer_id=<?= $answer['answer_id'];?>&question_slug=<?= $question['question_slug'];?>"> <i class="fa-solid fa-trash" style="color:#dc3545"></i> </a></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item">
                                <a class="page-link" href="?question_slug=<?=$question['question_slug'];?>&page-nr=<?= (isset($_GET['page-nr']) && $_GET['page-nr'] > 1) ? ($_GET['page-nr'] - 1) : 1 ; ?>">Previous</a>
                            </li>

                            <?php for($i = 1; $i <= $number_of_pages; $i++): ?>
                                <li class="page-item <?= (isset($_GET['page-nr']) && $_GET['page-nr'] == $i) ? 'active' : '' ; ?>">
                                    <a class="page-link" href="?question_slug=<?=$question['question_slug'];?>&page-nr=<?= $i; ?>"><?= $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item">
                                <a class="page-link" href="?question_slug=<?=$question['question_slug'];?>&page-nr=<?= isset($_GET['page-nr']) ? (($_GET['page-nr'] < $number_of_pages) ? $_GET['page-nr'] + 1 : $number_of_pages) : 1 ; ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div> 




                