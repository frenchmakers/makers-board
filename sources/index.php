<?php
	include 'makerBoard.class.php';
	$makerBoard = new makerBoard();
	$makerBoard->init();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
   
    	<title>MakerBoard</title>
    	<!-- Bootstrap -->
     	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/css/main.css" rel="stylesheet" type="text/css"/>
	<script src="assets/js/jquery-1.12.2.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
<!--	<script src="assets/js/moduleRendering.js"></script>-->
        <script src="assets/js/module.js" type="text/javascript"></script>
        <?php
        // Génération des styles CSS
            foreach ($makerBoard->configs as $module => $config) {
                if(isset($config["styles"])){
                    if(is_array($config["styles"])){
                        foreach ($config["styles"] as $style) {
                            echo("<link href='modules/$module/$style' rel='stylesheet'>");
                        }
                    }else{
                        echo("<link href='modules/$module/{$config["styles"]}' rel='stylesheet'>");
                    }
                }
            }
        ?>
</head>
<body>
	<h1><?php echo($makerBoard->title); ?></h1>
        <div class="container-fluid grid">
            <div class="row">
                <div class="module" data-module="horloge">
                </div>
		<div class="module" data-module="meteo">
		</div>
            </div>
        </div>

	<div class="container">


		<?php
//		     	foreach($makerBoard->modules as $module) {
//				echo $module;
//        		}
	        ?>		
	</div>
        <?php
        // Génération des scripts JS
            foreach ($makerBoard->configs as $module => $config) {
                if(isset($config["scripts"])){
                    if(is_array($config["scripts"])){
                        foreach ($config["scripts"] as $script) {
                            echo("<script src='modules/$module/$script' type='text/javascript'></script>");
                        }
                    }else{
                        echo("<script src='modules/$module/{$config["scripts"]}' type='text/javascript'></script>");
                    }
                }
            }
        ?>
</body>
</html>
