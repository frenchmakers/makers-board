<?php
$message="";
$command = $_GET["command"];
if($command==="refresh-dashboard"){
    $file = __DIR__."/datas/refresh-dashboard";
    file_put_contents($file, time());
    $message = "L'ordre de rafraichissement est lancé.";
}
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
	<script src="assets/js/jquery-1.12.2.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
</head>
<body>
	<h1>Configuration Maker Board</h1>
	<div class="container">
            <?php
            if($message!==""){
                ?>
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button>
                <?php echo($message); ?>
            </div>
                <?php
            }
            ?>
            
            <div class="row">
                <div class="col-md-8">
                    
                </div>
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">                        
                            Commandes
                        </div>
                        <div class="panel-body">
                            <ul>
                                <li><a href="config.php?command=refresh-dashboard">Actualiser le tableau de bord</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
	</div>
</body>
</html>
