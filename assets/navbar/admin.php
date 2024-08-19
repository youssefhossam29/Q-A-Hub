    <?php 
        $pageName = basename($_SERVER['PHP_SELF']);
    ?>

    <ul class="nav nav-pills flex-column flex-sm-row">

        <li class="nav-item">
            <a class="nav-link <?php echo ($pageName  == 'home.php' ) ? 'active' : ''; ?>" aria-current="page" href="home.php">Home</a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo ($pageName  == 'allUsers.php' || $pageName  == 'showUser.php' || $pageName  == 'createAdmin.php' ) ? 'active' : ''; ?>" aria-current="page" href="allUsers.php">Users</a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo ($pageName  == 'allCategories.php' || $pageName  == 'createCategory.php' || $pageName  == 'updateCategory.php' || $pageName  == 'showCategoryQuestions.php')  ? 'active' : ''; ?>" aria-current="page" href="allCategories.php">Categories</a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo ($pageName  == 'allQuestions.php' || $pageName  == 'addQuestion.php' || $pageName  == 'showQuestion.php' || $pageName  == 'showQuestionAnswers.php') ? 'active' : ''; ?>" aria-current="page" href="allQuestions.php">Questions</a>
        </li>

        <li class="nav-item">
            <a href="./updateProfile.php" class="nav-link <?php echo ($pageName  == 'updateProfile.php') ? 'active' : ''; ?>">My Profile</a>
        </li>
                
        <li class="nav-item">        
            <a href="../auth/logout.php" class="nav-link">Log out</a>
        </li>
    </ul>