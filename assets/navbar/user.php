<?php 
    $pageName = basename($_SERVER['PHP_SELF']);
?>

<ul class="nav nav-pills flex-column flex-sm-row">

    <li class="nav-item">
        <a class="nav-link <?php echo ($pageName  == 'home.php' ) ? 'active' : ''; ?>" aria-current="page" href="home.php">Home</a>
    </li>


    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle <?php echo ($pageName  == 'followingCategories.php'  || $pageName  == 'allCategories.php' || $pageName  == 'showCategoryQuestions.php') ? 'active' : ''; ?>" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Categories</a>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink" style="text-align: center">
                <li><a href="allCategories.php" >All Categories</a></li>
                <li><a href="followingCategories.php" >Following Categories</a></li>
            </ul>
    </li>


    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle <?php echo ($pageName  == 'updateQuestion.php'  || $pageName  == 'addQuestion.php' || $pageName  == 'showUser.php' || $pageName  == 'allQuestions.php' || $pageName  == 'showQuestionAnswers.php' || $pageName  == 'showQuestion.php' || $pageName  == 'updateAnswer.php' || $pageName  == 'allQuestions.php') ? 'active' : ''; ?>" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Questions</a>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink" style="text-align: center">
                <li><a href="allQuestions.php" >All Questions</a></li>
                <li><a href="addQuestion.php" >Ask Question</a></li>
                <li><a href="showUser.php?user_id=<?= $_SESSION['userdata']['id'] ?>" >My Questions</a></li>
            </ul>
    </li>

    

    <li class="nav-item dropdown">
        <a href="./updateProfile.php" class="nav-link <?php echo ($pageName  == 'updateProfile.php') ? 'active' : ''; ?>">My Profile</a>
    </li>

    <li class="nav-item">
        <a href="../auth/logout.php" class="nav-link">Log out</a>
    </li>
</ul>

