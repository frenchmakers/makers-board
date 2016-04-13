<?php
//var_dump($_POST);
//var_dump($_SERVER);
if($_SERVER["REQUEST_METHOD"]=="POST" && $_SERVER["PATH_INFO"]=="/display"){
    file_put_contents("datas/display.json", json_encode($_POST));
    echo("{status:'ok'}");    
} else if($_SERVER["REQUEST_METHOD"]=="GET" && $_SERVER["PATH_INFO"]=="/display") {
    if(file_exists("datas/display.json")){
        echo(file_get_contents("datas/display.json"));
    }else{
        echo("{modules:[]}");    
    }
} else {
    echo("{status:'error', message:'Commande inconnue'}");
}
