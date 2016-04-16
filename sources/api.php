<?php
// Initialisation
include 'makerBoard.class.php';
$makerBoard = new makerBoard();

// Extraction des informations de commandes
$method = strtolower(isset($_SERVER["REQUEST_METHOD"]) ? $_SERVER["REQUEST_METHOD"] : "get"); 
$path = trim(isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : "", " /");

$response = FALSE;

// Commande ping d'un board
if( ($r = urlMatch("board/{board}/ping", $path)) !== FALSE && $method = "get" ) {
    $update = $makerBoard->getBoadLastUpdate($r["board"]);
    if($update === FALSE){
        if($r["board"] == "default"){
            $response = '0';
        }
    } else {
        $response = $update;
    }
}
// Commande board
else if( ($r = urlMatch("board/{board}", $path)) !== FALSE ) {
    if($method == "get"){
        $layout = $makerBoard->getBoadLayout($r["board"]);
        if($layout === FALSE) {
            if($r["board"] == "default"){
                $response = '{ "lastUpdate": 0, "size": { "width":1024, "height":768 }, "modules": [] }';
            }
        } else {
            $response = $layout;
        }
    }
}
/*else if($path == "board"){
    if($method == "get"){
        // Lecture du fichier de positionnement
        if(file_exists("datas/board.json")){
            $response = file_get_contents("datas/board.json");
        }else{
            $response = '{ "size": { "width":1024, "height":768 } "modules": [] }';    
        }        
    } else if($method == "post"){
        // MAJ du layout du board
        file_put_contents("datas/board.json", json_encode($_POST));
        $response = TRUE;
    }
}*/

// Traitement de la réponse
if($response === FALSE) {
    $response = '{"status":"error", "message":"Commande inconnue"}';
} else if($response === TRUE) {
    $response = '{"status":"ok"}';
} else if(is_array($response)){
    $response = json_encode($response);
}
echo($response);

// Détermination de la correspondance d'une url avec un pattern
function urlMatch($pattern, $url) {
    $cnt = preg_match_all('/\{[\w_-]+\}/i', $pattern, $matches, PREG_OFFSET_CAPTURE); 
    $regex="";
    if($cnt>0){
        $pos = 0;
        foreach ($matches[0] as $value) {
            $part = substr($pattern, $pos, $value[1] - $pos);
            if($part!==FALSE) {
                $regex .= preg_quote($part);                
            }
            $regex .= "(?P<".substr($value[0], 1, strlen($value[0])-2 ).">[\w_-]+)";
            $pos = $value[1] + strlen($value[0]);
        }
        $part = substr($pattern, $pos);
        if($part!==FALSE){
            $regex .= preg_quote($part);
        }
    }else{
        $regex = preg_quote($pattern);
    }
    if($regex === "") return FALSE;
    $regex = "#^${regex}$#i";
    
    $cnt = preg_match($regex, $url, $matches);
    if($cnt>0) {
        return $matches;
    }
    return FALSE;    
}