<?php
session_start();
include_once '../inc/header.inc.php';
$email = $_SESSION['email'];
$userData = getUserData($email);
$userPrivilege = $userData['privilege'];

$validate['success'] = array('success' => false, 'message' => "");
if ($userPrivilege != 'guest') {
    if ($_POST) {
        $userName = $_POST['userName'];
        $name = $_POST['nameUser'];
        $lastname = $_POST['lastnameUser'];
        $password = $_COOKIE['passwordUserTemp'];
        $emailOld = $_COOKIE['loginUserTemp'];
        $emailNew = $_POST['email'];
        $image = $_POST['userPicture'];
        $row = getUserData($emailOld);
        $id = $row['IDuser'];
        $oldUSerName = $row['userName'];
        $reservedWords = reservedWords();
        if ($userName == $oldUSerName) {
            $userName = $row['userName'];
        }
        if (empty($image)) {
            $image = $row['userPicture'];
        }
        if (checkUserName($userName) && $userName != $oldUSerName) {
            header("HTTP/1.1 400 Bad Request");
            $validate['success'] = false;
            $validate['message'] = 'ERROR. That user name already exists';
        } elseif (in_array(strtolower($userName), $reservedWords)) {
            header("HTTP/1.1 400 Bad Request");
            $validate['success'] = false;
            $validate['message'] = 'ERROR. You cannot use system reserved words';
        } elseif ($emailOld == $emailNew) {
            if (update_user($userName, $emailOld, $password)) {
                header("HTTP/1.1 200 OK");
                updateSaveImage($emailNew, $image);
                updateAboutUser($id, "", $name, $lastname);
                $validate['success'] = true;
                $validate['message'] = 'The user saved correctly';
            } else {
                header("HTTP/1.1 400 Bad Request");
                $validate['success'] = false;
                $validate['message'] = 'ERROR. The user did not save correctly';
            }
        } elseif (checkEmail($emailNew)) {
            header("HTTP/1.1 400 Bad Request");
            $validate['success'] = false;
            $validate['message'] = 'ERROR. The email is already in use';
        } else {
            if (update_user($userName, $emailOld, $password)) {
                if ($row['privilege'] == 'admin') {
                    unset($_SESSION['email']);
                    $_SESSION['email'] = $emailNew;
                    cookiesUser($emailNew, $password);
                    cookiesAdmin($emailNew, $password);
                }
                updateAboutUser($id, $infoUser, $name, $lastname);
                update_email($emailNew, $emailOld);
                createDirectory($emailNew, $id);
                updateSaveImage($emailNew, $image);
                insertURL($emailNew, $id);
                destroyCookiesUserTemporal();
                deleteDirectory($emailOld, $id);
                $validate['success'] = true;
                $validate['message'] = 'The user has been updated';
                header("HTTP/1.1 200 OK");
            } else {
                header("HTTP/1.1 400 Bad Request");
                $validate['success'] = false;
                $validate['message'] = 'ERROR. The user did not save correctly';
            }
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        $validate['success'] = false;
        $validate['message'] = 'ERROR. The user was not saved in the database';
    }
} else {
    header("HTTP/1.1 401 Unauthorized");
    $validate['success'] = false;
    $validate['message'] = 'ERROR. You must be logged in to modify a user';
}

echo json_encode($validate);