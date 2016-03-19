<?php
$module = $_GET["module"];
$file = __DIR__."/modules/$module/module.php";
if(is_file($file)){
    return require($file);
}else{
    return "false";
}
