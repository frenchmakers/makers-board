<?php
// Extraction des informations de commandes
$method = strtolower(isset($_SERVER["REQUEST_METHOD"]) ? $_SERVER["REQUEST_METHOD"] : "get"); 
$path = trim(isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : "", " /");

$response = FALSE;

// Commande display
if($path == "display"){
    if($method == "get"){
        // Lecture du fichier de positionnement
        if(file_exists("datas/display.json")){
            $response = file_get_contents("datas/display.json");
        }else{
            $response = '{ "modules": [] }';    
        }        
    } else if($method == "post"){
        // MAJ du layout du display
        file_put_contents("datas/display.json", json_encode($_POST));
        $response = TRUE;
    }
}

// Traitement de la réponse
if($response === FALSE) {
    $response = '{"status":"error", "message":"Commande inconnue"}';
} else if($response === TRUE) {
    $response = '{"status":"ok"}';
} else if(is_array($response)){
    $response = json_encode($response);
}
echo($response);
