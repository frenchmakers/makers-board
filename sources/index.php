<?php
// Initialisation
include 'makerBoard.class.php';
$makerBoard = new makerBoard();

// Extraction du nom du tableau de bord
$board = trim(isset($_REQUEST["board"]) ? $_REQUEST["board"] : "default");

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Maker Board</title>

    <link href="assets/css/board.css" rel="stylesheet" type="text/css" />
    <?php
    // Chargement des styles de tous les modules définis
    foreach ($makerBoard->getModules() as $module) {
        if(isset($module["styles"])) {
            $styles = array();
            if(is_array($module["styles"])) {
                $styles = $module["styles"];
            } else {
                $styles = array($module["styles"]);
            }
            foreach ($styles as $style) {
                echo("<link href='{$module['folder']}/$style' rel='stylesheet' type='text/css' />\n");
            }
        }
    }
    ?>
    
</head>

<body>
    <div class="layout">
        <h1></h1>
        <div class="board" data-board="<?php echo($board) ?>">
        </div>
    </div>

    <script src="assets/js/jquery-1.12.2.min.js"></script>
    <script src="assets/js/board.js"></script>
    <?php
    // Chargement des scripts de tous les modules définis
    foreach ($makerBoard->getModules() as $module) {
        if(isset($module["scripts"])) {
            $scripts = array();
            if(is_array($module["scripts"])) {
                $scripts = $module["scripts"];
            } else {
                $scripts = array($module["scripts"]);
            }
            foreach ($scripts as $script) {
                echo("<script src='{$module['folder']}/$script'></script>\n");
            }
        }
    }
    ?>
    <script type="text/javascript">
        (function($) {
            // Activation du tableau
            $(".board").board({
                refresh: 3 * 1000
            }).on("board.loaded", function(){
                $this = $(this);
                $(".module", $this).each(function(){
                    var $module = $(this);
                    $module.board('module.load', {
                        done: function() {
                            var $this = $(this);
                            $(document).trigger('module.'+$this.data("module")+'.loaded', [$this]);
                        }
                    });
                });
                $(document).trigger('board.loaded', $this);
            });
        })(jQuery);
    </script>

</body>

</html>