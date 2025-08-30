<?php
session_start();

require("../../vendor/autoload.php");

use src\Connectbd;
use src\Product;

$cnx = Connectbd::getConnection();


function redirect($url, $message = '') {
    if (!empty($message)) {
        $url .= '?message=' . urlencode($message);
    }
    header("Location: $url");
    exit();
}



function checkProduct(){
      if (
          isset($_POST['name']) && !empty($_POST['name']) &&
          isset($_POST['price']) && !empty($_POST['price']) &&
          isset($_POST['quantity']) && !empty($_POST['quantity']) &&
          isset($_FILES['image']) && !empty($_FILES['image']) &&
          isset($_POST['status']) && !empty($_POST['status'])
      ) {
           

            $products = [
                'name' => htmlspecialchars(trim($_POST['name'])),
                'price' => htmlspecialchars(trim($_POST['price'])),
                'quantity' => htmlspecialchars(trim($_POST['quantity'])),
                'image' => basename($_FILES['image']['name']),
                'status' => htmlspecialchars(trim($_POST['status'])),
            ];
            return $products;
      } else {
          return false;
      }
}


function checkAdditionalDescriptions(){
      if (
          isset($_POST['titre']) && !empty($_POST['titre']) &&
          isset($_POST['texte']) && !empty($_POST['texte']) //&&
          //isset($_POST['is_active']) && !empty($_POST['is_active'])
      ) {

        $addescripts = [
            'titre' => htmlspecialchars(trim($_POST['titre'])),
            'texte' => htmlspecialchars(trim($_POST['texte'])),
            //'is_active' => htmlspecialchars(trim($_POST['is_active']))
        ];
          return $addescripts;
      } else {
          return false;
      }
}


function checkCharacteristics(){
      if (
          isset($_POST['titre']) && !empty($_POST['titre']) &&
          isset($_FILES['image']) && !empty($_FILES['image']) &&
          isset($_POST['description']) && !empty($_POST['description']) //&&
          //isset($_POST['is_active']) && !empty($_POST['is_active'])
      ) {

        $characteristics = [
            'titre' => htmlspecialchars(trim($_POST['titre'])),
            'image' => htmlspecialchars(trim($_FILES['image']['name'])),
          ];


          return $characteristics;
      } else {
          return false;
      }
}

function checkCarousel(){
      if (
          isset($_FILES['image_url']) && !empty($_FILES['image_url']) //&&
          //isset($_POST['is_active']) && !empty($_POST['is_active'])
      ) {

        $carousel = [
            'image_url' => htmlspecialchars(trim($_FILES['image_url']['name'])),
        ];
          return $carousel;
      } else {
          return false;
      }
}

function checkFunctionalities(){
      if (
          isset($_POST['image']) && !empty($_POST['image']) &&
          isset($_POST['titre']) && !empty($_POST['titre']) &&
          isset($_POST['elements_1']) && !empty($_POST['elements_1']) &&
          isset($_POST['elements_2']) && !empty($_POST['elements_2']) &&
          isset($_POST['elements_3']) && !empty($_POST['elements_3']) &&
          isset($_POST['elements_4']) && !empty($_POST['elements_4']) &&
          isset($_POST['functionality_order']) && !empty($_POST['functionality_order']) //&&
         // isset($_POST['is_active']) && !empty($_POST['is_active'])
      ) {
          return true;
      } else {
          return false;
      }
}

function checkMentions(){
      if (
          isset($_POST['video_url']) && !empty($_POST['video_url']) &&
          isset($_POST['texte']) && !empty($_POST['texte']) &&
          isset($_POST['is_active']) && !empty($_POST['is_active'])
      ) {
          return true;
      } else {
          return false;
      }
}



function checkDescriptiveImages(){
      if (
          isset($_POST['image_url']) && !empty($_POST['image_url'])
      ) {
          return true;
      } else {
          return false;
      }
}



function checkPacks(){
      if (
          isset($_POST['pack_order']) && !empty($_POST['pack_order']) &&
          isset($_POST['titre']) && !empty($_POST['titre']) &&
          isset($_POST['description']) && !empty($_POST['description']) &&
          isset($_POST['quantity']) && !empty($_POST['quantity']) &&
          isset($_POST['pack_price']) && !empty($_POST['pack_price']) &&
          isset($_POST['image']) && !empty($_POST['pack_quantity']) &&
          isset($_POST['pack_image']) && !empty($_POST['pack_image']) &&
          isset($_POST['pack_status']) && !empty($_POST['pack_status'])
      ) {
          return true;
      } else {
          return false;
      }
}


function checkRecommandaton(){
      if (
          isset($_POST['pack_order']) && !empty($_POST['pack_order']) &&
          isset($_POST['titre']) && !empty($_POST['titre']) &&
          isset($_POST['description']) && !empty($_POST['description']) &&
          isset($_POST['quantity']) && !empty($_POST['quantity']) &&
          isset($_POST['pack_price']) && !empty($_POST['pack_price']) &&
          isset($_POST['image']) && !empty($_POST['pack_quantity']) &&
          isset($_POST['pack_image']) && !empty($_POST['pack_image']) &&
          isset($_POST['pack_status']) && !empty($_POST['pack_status'])
      ) {
          return true;
      } else {
          return false;
      }
}