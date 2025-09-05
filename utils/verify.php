<?php

function verify($redirection){
    session_start();
    if (empty($_SESSION)) {
        header('location: /shopping/management/users/login.php?redirect='. $redirection);
    }
}
