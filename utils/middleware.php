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

function checkAdminAccess($role)
{
    if ($role !== 1) {
        header('HTTP/1.0 403 Forbidden');
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
            header('HTTP/1.0 401 Unauthorized');
            exit();
        }
    }
}
