
    <?php 
        $pageName = basename($_SERVER['PHP_SELF']);
    ?>

    <ul class="nav nav-pills flex-column flex-sm-row">
        <li class="nav-item">
            <a class="nav-link <?php echo ( basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : ''; ?>" aria-current="page" href="login.php">Login</a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo ( basename($_SERVER['PHP_SELF']) == 'register.php') ? 'active' : ''; ?>" aria-current="page" href="register.php">Register</a>
        </li>            
    </ul>
    
