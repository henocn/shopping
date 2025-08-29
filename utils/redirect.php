<?php 
session_start(); 
if (isset($_SESSION['username']) && isset($_SESSION['role']) && isset($_SESSION['pays'])) {
    $username = $_SESSION['username'];
    $role = $_SESSION['role'];
    $country = $_SESSION['country'];
}


if ($role !== 'assistante') {
    header('location:../managements/index.php?message=success');
    exit();
}else{
      header('location:../assistantes/index.php?status=success');
      exit();
}