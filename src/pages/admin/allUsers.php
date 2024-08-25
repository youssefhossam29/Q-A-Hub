<html>

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

        $_GET['type'] = isset($_GET['type']) ? $_GET['type']: "all";
        $users = showUsers($start, $rows_per_page, $_GET['type']);

        $number_of_users = sizeof($users);
        if($number_of_users > 0){
            $number_of_users = $users[0]['total_users'];
            $number_of_pages = ceil($number_of_users / $rows_per_page);
        }   
    ?>
    <div class="container-fluid" style="margin-bottom:80px">
        
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" style="margin:1px">
                <li class="nav-item">
                    <a class="btn btn-secondary dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false" > <h5> Users Details </h5></a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink" style="text-align: center">
                        <li><a href="./allUsers.php?type=all" >Show All</a></li>
                        <li><a href="./allUsers.php?type=users" >Show Users</a></li>
                        <li><a href="./allUsers.php?type=admins" >Show Admins</a></li>
                    </ul>
                </li>

                <li class="nav-item ml-auto mr-3">
                    <a href="./createAdmin.php" class="btn btn-success">Create new admin</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <?php         
                if (sizeof($users) == 0) {
                    echo '<div class="container mt-4 d-flex justify-content-center">
                    <div class="alert alert-danger col-md-6 text-center"> There is no Users yet</div> </div>';
                    die;
                }
            ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Photo</th>
                        <th scope="col">Email</th>
                        <th scope="col">Gender</th>
                        <th scope="col">Type</th>
                        <th scope="col"></th>

                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    foreach($users as $user ): 
                    ?>  
                        <tr>
                            <td scope="row"> <?= $i++; ?> </td>
                            <td scope="row"> <?= $user['name']; ?> </td>
                            <td scope="row">
                                <img src="../../../public/uploads/users/<?= $user['photo']; ?>" alt="" class="img-tumbnail" width="60">
                            </td>
                            <td scope="row"> <?= $user['email']; ?> </td>
                            <td scope="row"> <?= $user['gender']? "M" : "F"; ?> </td>
                            <td scope="row"> <?= $user['admin']? "Admin" : "User" ?> </td>
                            <td scope="row">
                                <a  href='./showUser.php?user_id=<?= $user['id'] ?>' class="btn btn-secondary"><i class = 'fas fa-eye'></i> Show Profile</a>  
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
                    <a class="page-link" href="?type=<?= isset($_GET['type']) ? $_GET['type']: "all"; ?>&page-nr=<?= (isset($_GET['page-nr']) && $_GET['page-nr'] > 1) ? ($_GET['page-nr'] - 1) : 1 ; ?>">Previous</a>
                </li>

                <?php for($i = 1; $i <= $number_of_pages; $i++): ?>
                    <li class="page-item <?= (isset($_GET['page-nr']) && $_GET['page-nr'] == $i) ? 'active' : '' ; ?>">
                        <a class="page-link" href="?type=<?= isset($_GET['type']) ? $_GET['type']: "all"; ?>&page-nr=<?= $i; ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item">
                    <a class="page-link" href="?type=<?= isset($_GET['type']) ? $_GET['type']: "all"; ?>&page-nr=<?= isset($_GET['page-nr']) ? (($_GET['page-nr'] < $number_of_pages) ? $_GET['page-nr'] + 1 : $number_of_pages) : 1 ; ?>">Next</a>
                </li>
            </ul>
        </nav>
        </div>
    </div>


    

</html>
