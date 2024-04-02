<?php
session_start();
error_reporting(0);
require('../database/connection.php');
require('../models/Auth.php');
$database = new Database();
$db = $database->connect();
$authenticate = new Auth($db);

function login()
{
    global $authenticate;
    try {

        $email = str_replace("'", "\'", $_POST['email']);
        $password = str_replace("'", "\'", $_POST['password']);

        $response = $authenticate->login($email);
        $num = $response->rowCount();

        if ($num > 0) {
            $user = $response->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $user['password'])) {
                $userId               = $user['id'];
                $name                 = $user['name'];
                $email                = $user['email'];

                $_SESSION['userid']   = $userId;
                $_SESSION['username'] = $name;
                $_SESSION['email']    = $email;

                if(!empty($_POST['sync_data'])) {
                    ?>
                    <script>
                        window.location.href = "../sync/home.php"
                    </script>
                    <?php
                } else {
                    ?>
                    <script>
                        window.location.href = "../views/dashboard.php"
                    </script>
                    <?php
                }
                
            } else {
                $_SESSION['message'] = '<div class="alert alert-danger">Incorrect username or password</div>';
                ?>
                <script>
                    window.history.back();
                </script>
                <?php
            }
        } else {
            $_SESSION['message'] = '<div class="alert alert-danger">Incorrect username or password</div>';
            ?>
            <script>
                window.history.back();
            </script>
            <?php
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

function create()
{
    global $authenticate;
    $options = [
        'cost' => 12,
    ];

    $id = $_POST['id'] ?? '';

    if($_POST['password'] != $_POST['confirm_password']) {
        $_SESSION['message']='<div class="alert alert-danger">Passwords do not match!</div>'; ?>
        <script>
            window.history.back();
        </script>
        <?php
        return;
    }

    $data=array(
        'name'      => str_replace("'","\'",$_POST['name']),
        'password'      => password_hash($_POST['password'], PASSWORD_BCRYPT, $options),
        'email'         => str_replace("'","\'",$_POST['email']),
        'created_at'     => date('Y-m-d H:m:s'),
        'updated_at'     => date('Y-m-d H:m:s')
    );

    try {

        if($id) {
            $user = $authenticate->update($data, $id);
            $message = 'User update successfully';
        } else {
            $user = $authenticate->add($data);
            $message = 'User created successfully';
        }

        if($user) {
                $_SESSION['message'] = $message;
                ?>
                <script>
                    window.location.href="../views/users.php"</script>
                <?php
        } else{
            $_SESSION['message']='Problem in user creation!'; ?>
            <script>
                window.history.back();
            </script>
            <?php
        }
    } catch (Exception $e) {
        $_SESSION['message'] = $e->getMessage(); 
        ?>
        <script>
            window.history.back();
        </script>
        <?php
    }
}

function Logout()
{
    session_destroy();
    ?>
    <script>
        window.location.href="../index.php"</script>
    <?php
}

$f = $_GET['f'];
$f();
