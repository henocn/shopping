<?php

use src\User;
use src\Connectbd;

function verifyConnection($redirection)
{
    session_start();
    if (empty($_SESSION)) {
        header('location: /shopping/management/users/login.php?redirect=' . $redirection);
        die();
    }
}

function checkAdminAccess($id)
{
    $cnx = Connectbd::getConnection();
    $manager = new User($cnx);
    $user = $manager->getUserById($id);
    if ($user) {
        if ($user['role'] !== 1) {
            header('Location: ../error.php?code=403');
            exit();
        }
    } else {
        header('Location: ../error.php?code=401');
        exit();
    }
}


function checkIsActive($id)
{
    $cnx = Connectbd::getConnection();
    $manager = new User($cnx);
    $user = $manager->getUserById($id);
    if ($user) {
        if ($user['is_active'] == 0) {
            header('Location: ../error.php?code=403');
            exit();
        }
    } else {
        header('Location: ../error.php?code=401');
        exit();
    }
}
