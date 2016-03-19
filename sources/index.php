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
	<script src="assets/js/jquery-1.12.2.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
	<script src="assets/js/moduleRendering.js"></script>

</head>
<body>
	<?= $makerBoard->title; ?>
	<h1>Makers Board</h1>
	<div class="container">


		<?php
		     	foreach($makerBoard->modules as $module) {
				echo $module;
        		}
	        ?>		
	</div>

</body>
</html>
