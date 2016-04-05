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

    <title><?php echo($makerBoard->title); ?></title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/jquery.gridster.min.css" rel="stylesheet" type="text/css" />
     
<?php 
    
// Génération des styles CSS
foreach($makerBoard->configs as $module => $config) {
    if (isset($config["styles"])) {
        if (is_array($config["styles"])) {
            foreach($config["styles"] as $style) {
                echo("<link href='modules/$module/$style' rel='stylesheet'>");
            }
        } else {
            echo("<link href='modules/$module/{$config["styles"]}' rel='stylesheet'>");
        }
    }
}
?>
</head>

<body data-last-refresh="<?php echo($makerBoard->getLastRefresh()) ?>">
    <h1><?php echo($makerBoard->title); ?></h1>
<?php 
$board = $makerBoard->readBoardConfig();
?>
    <div class="board gridster">
        <ul>
    <?php 
    foreach($board["layout"] as $elm) {
        if ($elm["type"] == "module") {
            $module = $makerBoard->findModule($elm['module']);
        }
    ?>
            <li data-sizey="<?php echo($elm["size_y"]) ?>" data-sizex="<?php echo($elm["size_x"]) ?>" data-col="<?php echo($elm["col"]) ?>" data-row="<?php echo($elm["row"]) ?>" data-type="<?php echo($elm["type"]) ?>" <?php echo(isset($elm["module"]) ? "data-module='".$elm["module"]."'" : "") ?>>
                <?php if ($elm["type"] == "module") { ?>
                <div class="module" data-module="<?php echo($elm["module"]) ?>">
                </div>
                <?php } else { ?>
                <div class="separator">
                </div>
                <?php } ?>
            </li>
            <?php 
    }
    ?>

        </ul>
    </div>
    <script src="assets/js/jquery-1.12.2.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <!--	<script src="assets/js/moduleRendering.js"></script>-->
    <script src="assets/js/board.js" type="text/javascript"></script>
    <script src="assets/js/module.js" type="text/javascript"></script>
    <script src="assets/js/jquery.gridster.min.js" type="text/javascript"></script>
    <?php 
// Génération des scripts JS
foreach($makerBoard->configs as $module => $config) {
    if (isset($config["scripts"])) {
        if (is_array($config["scripts"])) {
            foreach($config["scripts"] as $script) {
                echo("<script src='modules/$module/$script' type='text/javascript'></script>");
            }
        } else {
            echo("<script src='modules/$module/{$config["scripts"]}' type='text/javascript'></script>");
        }
    }
}
?>
    <script type="text/javascript">
        (function($) {
            var gridster = null;
            //$(document).ready(function () {
            gridster = $(".board ul").gridster({
                widget_base_dimensions: ['auto', 96],
                autogenerate_stylesheet: true,
                min_cols: 1,
                max_cols: 12,
                min_rows: 4,
                max_rows: 6,
                widget_margins: [0, 0],
                serialize_params: function($w, wgd) {
                    return {
                        col: wgd.col,
                        row: wgd.row,
                        size_x: wgd.size_x,
                        size_y: wgd.size_y,
                        type: $w.data("type"),
                        module: $w.data("module")
                    }
                },
                resize: {
                    enabled: false
                },
                draggable: {
                    enabled: false,
                }
            }).data('gridster');
        })(jQuery);
    </script>
</body>

</html>