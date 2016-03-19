<?php
$module = $_GET["module"];
if($module=="dashboard"){
    $file = __DIR__."/datas/refresh-dashboard";
    if(is_file($file)){
        echo(file_get_contents($file));
    }else{
        echo("0");
    }
}else{
    $file = __DIR__."/modules/$module/module.php";
    if(is_file($file)){
        return require($file);
    }else{
        echo("false");
    }
}
