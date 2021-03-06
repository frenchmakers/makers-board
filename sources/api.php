<?php
// Initialisation
include 'makerBoard.class.php';
$makerBoard = new makerBoard();

// Extraction des informations de commandes
$method = strtolower(isset($_SERVER["REQUEST_METHOD"]) ? $_SERVER["REQUEST_METHOD"] : "get"); 
$path = trim(isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : "", " /");

$response = FALSE;

// Commande ping général
if( ($urlData = urlMatch("ping", $path)) !== FALSE && $method == "get" ) {
    $response = $makerBoard->getLastUpdate();
}
// Commande pour actualiser le système
else if( ($urlData = urlMatch("update-system", $path)) !== FALSE && $method == "post" ) {
    $makerBoard->setLastUpdate();
    $response = TRUE;
}
// Commande ping d'un board
else if( ($urlData = urlMatch("board/{board}/ping", $path)) !== FALSE && $method == "get" ) {
    $update = $makerBoard->getBoardLastUpdate($urlData["board"]);
    if($update === FALSE){
        if($urlData["board"] == "default"){
            $response = '0';
        }
    } else {
        $response = $update;
    }
}
// Commande de lecture de la taille d'affichage d'un board
else if( ($urlData = urlMatch("board/{board}/view-size", $path)) !== FALSE && $method == "get" ) {
    $response = $makerBoard->getBoardViewSize($urlData["board"]);
}
// Commande board
else if( ($urlData = urlMatch("board/{board}", $path)) !== FALSE ) {
    if($method == "get") {
        $dw = isset($_REQUEST['dw']) ? intval($_REQUEST['dw']) : 0;
        $dh = isset($_REQUEST['dh']) ? intval($_REQUEST['dh']) : 0;
        if($dw>0 && $dh>0){
            $makerBoard->setBoardViewSize($urlData["board"], $dw, $dh);
        }
        $layout = $makerBoard->getBoardLayout($urlData["board"]);
        if($layout === FALSE) {
            if($urlData["board"] == "default"){
                $response = '{ "lastUpdate": 0, "size": { "width":1024, "height":768 }, "modules": [] }';
            }
        } else {
            $response = $layout;
        }
    }else if($method == "post") {
        $json = file_get_contents('php://input');
        $makerBoard->setBoardLayout($urlData["board"], $json);
        $response = TRUE;
    }
}
// Commande : Récupération du contenu d'un module
else if( ($urlData = urlMatch("board/{board}/module/{module}", $path)) !== FALSE ) {
    if($method == "get") {
        $moduleConfig = $makerBoard->getBoardModuleConfig($urlData['board'], $urlData['module']);
        $module = $makerBoard->getModule($moduleConfig->module);
        if($module !== false) {
            $moduleParams = isset($moduleConfig->params) ? (array)$moduleConfig->params : array();
            $moduleFile = $module['folder']."/module.php";
            if(is_file($moduleFile)) {
                ob_start();
                include($moduleFile);
                $response = ob_get_clean();
            } else {
                $response = "";
            }
        }
    }
}
// Commande : Récupération de la configuration d'un module
else if( ($urlData = urlMatch("board/{board}/module/{module}/config", $path)) !== FALSE ) {
    if($method == "get") {
        $response = array(
            'status' => 'error',
            'message' => "Ce module n'est pas configurable."
        );
        
        $moduleConfig = $makerBoard->getBoardModuleConfig($urlData['board'], $urlData['module']);
        $module = $makerBoard->getModule($moduleConfig->module);
        if($module !== false) {
            $moduleParams = isset($moduleConfig->params) ? (array)$moduleConfig->params : array();
            $moduleConfigFile = $module['folder']."/config.php";
            if(is_file($moduleConfigFile)) {
                ob_start();
                include($moduleConfigFile);
                $response = ob_get_clean();
                $response = array(
                    'status' => 'ok',
                    'html' => $response
                );
            }
        }
    } else if($method == "post") {
        $json = file_get_contents('php://input');
        $response = $makerBoard->setBoardModuleConfig($urlData['board'], $urlData['module'], $json);
        $response = array(
            'status' => 'ok',
            'params' => $response
        );
    }
}

// Traitement de la réponse
if($response === FALSE) {
    $response = '{"status":"error", "message":"Commande inconnue"}';
} else if($response === TRUE) {
    $response = '{"status":"ok"}';
} else if(is_array($response) || is_object($response)){
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
    $regex = "#^{$regex}$#i";
    
    $cnt = preg_match($regex, $url, $matches);
    if($cnt>0) {
        return $matches;
    }
    return FALSE;    
}