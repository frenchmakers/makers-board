/**
 * Gestion de l'éditeur de tableau
 */
(function ($) {
    
    // Gestion de la taille d'écran
    var sw = $("body").width();
    var sh = $("body").height();
    $("#current-screen-size").attr({
        "data-width": sw,
        "data-height": sh,
    }).text(sw + "x" + sh);
    $("#screen-size").change(function () {
        var $opt = $("option:selected", this);
        if ($opt.val() != "current") {
            sw = $opt.data("w");
            sh = $opt.data("h");
        } else {
            sw = $("#current-screen-size").data("width");
            sh = $("#current-screen-size").data("height");
        }
        // Recalcul la hauteur de la zone d'édition
        console.log("sw: " + sw, "sh: " + sh);
        var bw = $(".board-editor").width();
        var ratio = bw / sw;
        var bh = sh * ratio;
        console.log("bw: " + bw, "ratio: " + ratio, "bh: " + bh);
        $(".board-editor").height(bh);
        //$(".board-editor").board("save");
    });

    // Activation du board en mode edition
    $(".board-editor").board({
        refresh: false,
        editable: true,
        board: function () { return $("#current-board").val(); }
    }).board("load", {
        done: function (data) {
            var $this = this;
            var bw = $this.width() / data.size.width;
            var bh = data.size.height * bw;
            //$this.height(bh);
            var size = $("#screen-size option[data-w=" + data.size.width + "][data-h=" + data.size.height + "]").val();
            if(!size || size=="") size = "current";
            $("#screen-size").val(size);
            $(".board-editor").board("resize",{
                'height': bh
            });
        }
    });

})(jQuery);
