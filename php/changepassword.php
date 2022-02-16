<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.html";</script>';
}
else{
    $userId = $_SESSION['userID'];
}

if(isset($_POST['oldPassword'], $_POST['newPassword'], $_POST['confirmPassword'])){
    $password = $_POST['oldPassword'];
    $newpassword = $_POST['newPassword'];

    $stmt = $db->prepare("SELECT * from user where id = ? LIMIT 1");
    $stmt->bind_param('s', $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if($row = $result->fetch_assoc()){
        $password = hash('sha512', $password . $row['password_salt']);
        
        if($password == $row['user_password']){
            $newpassword = hash('sha512', $newpassword . $row['password_salt']);

            if ($update_stmt = $db->prepare("UPDATE user SET user_password=? WHERE id=?")) {
                $update_stmt->bind_param('ss', $newpassword, $userId);
                
                // Execute the prepared query.
                if (! $update_stmt->execute()) {
                    echo '<script type="text/javascript">alert("Somethings wrong");';
                    echo 'location.href = "../changepassword.php";</script>';  
                }
                else{
                    echo '<script type="text/javascript">alert("Updated successfully");';
                    echo 'location.href = "../changepassword.php";</script>';   
                }
            }
        }
        else{
            echo '<script type="text/javascript">alert("Old password is not matched");';
            echo 'window.location.href = "../changepassword.php";</script>';
        }
    }
    else {
        echo '<script type="text/javascript">alert("Login unsuccessful, password or email is not matched");';
        echo 'window.location.href = "../login.html";</script>';
    }
}
else{
    echo '<script type="text/javascript">alert("Missing Attributes");';
    echo 'window.location.href = "../changepassword.php";</script>';
}
?>
