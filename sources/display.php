<?php 
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Maker Board</title>

    <link href="assets/css/display.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="layout">
        <h1>MakerBoard</h1>
        <div class="board">
            <div class="module">
            </div>
            <div class="module">
            </div>
            <div class="module">
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-1.12.2.min.js"></script>
    <script src="assets/js/display.js"></script>
    <script type="text/javascript">
        (function($) {
            // Activation en mode non éditable
            $(".board").display({
                refresh: 3*1000,
                editable: false
            });
            
            var updateLayout = function(){
                $.get("api.php/display", {_t:new Date().getTime()}, function(data){
                    $.each(data.modules, function(idx, mod){
                        //console.log(mod);
                        $(".board .module:eq("+idx+")").css({
                            left: (parseInt(mod.x)*1)+"px",
                            top: (parseInt(mod.y)*1)+"px",
                            width: mod.w+"px",
                            height: mod.h+"px"
                        });
                    });
                }, "json");
            };
            var timerUpdateLayout = function(){
                updateLayout();
                setTimeout(timerUpdateLayout, 1000);
            };
            //timerUpdateLayout();
        })(jQuery);
    </script>

</body>

</html>