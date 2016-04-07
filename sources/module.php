<?php
$module = isset($_GET["module"]) ? $_GET["module"] : "";
$cmd = isset($_GET["cmd"]) ? $_GET["cmd"] : "";
if($cmd==="config"){
    $file = __DIR__."/modules/$module/config.php";
}else{
    $file = __DIR__."/modules/$module/module.php";
}
if(is_file($file)){
    return require($file);
}else{
    echo("false");
}
