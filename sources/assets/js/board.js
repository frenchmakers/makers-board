/**
 * Gestion des tableaux de bord
 */
(function ($) {

    // Définition du module board
    $.fn.board = function (command, options) {
        // Validation des options
        if (!command || $.isPlainObject(command)) {
            options = command;
            command = "enable";
        }
        options = $.extend({}, $.fn.board.defauls, options);

        // Application du module
        return this.each(function () {
            var $this = $(this);
            switch (command) {
                case "enable":
                    enableCommand.call($this, options);
                    break;
                case "refresh":
                    refreshCommand.call($this, options);
                    break;
                case "save":
                    saveCommand.call($this, options);
                    break;
            }
        });
    };

    // Valeurs par défaut
    $.fn.board.defauls = {
        api: "api.php",
        editable: false,
        refresh: 5 * 1000
    };

    // Activation d'un board
    var enableCommand = function (settings) {
        var $this = $(this);
        // Si le board est déjà défini on passe
        if ($this.data("board-settings"))
            return false;
        // Enregistrement des paramètres
        $this.data("board-settings", settings);
        // Activation du rafraichissement automatique
        if (settings.refresh !== false && settings.refresh > 0) {
            refreshCommand.call($this);
            setInterval(function () {
                refreshCommand.call($this);
            }, settings.refresh);
        }
        // Activation du mode edition
        if(settings.editable)
            setEditor($this);
    };

    // Actualisation d'un board
    var refreshCommand = function (options) {
        var $this = $(this);
        var settings = $this.data("board-settings");
        if(!settings) return;
        options = $.extend({
            "sendBoardSize": false,
            "updateBoardSize": false
        }, options);
        // 
        var sdata = {
            _t: new Date().getTime()
        };
        if(options.sendBoardSize) {
            sdata.dw = $this.width();
            sdata.dh = $this.height();
        }
        $.get(settings.api + "/board", sdata, function (data) {
            // Recalcul la taille de l'écran ?
            if(options.updateBoardSize) {
                var bw = $this.width() / data.size.width;
                var bh = data.size.height * bw;
                $this.height(bh);                
            }
            // Calcul des ratios
            var ratioX = $this.width() / data.size.width;
            var ratioY = $this.height() / data.size.height;
            // Récupération des modules actuellement affichés
            var cModules = {};
            $(".module", $this).each(function(){
                var $module = $(this);
                var id = $module.data("id");
                // Enregistre le module ou le supprime
                if(id && id!==""){
                    cModules[id] = false;
                }else{
                    $module.remove();
                }
            });
            // Traitement de chaque module recu
            $.each(data.modules, function(idx, mod){
                var id = mod.id;
                if(id && id!=="") {
                    // Recherche un module existant
                    var smodule = $(".module[data-id='"+id+"']", $this);
                    if(smodule.length==0){
                        // Le module n'existe pas on le créé
                        smodule = $('<div class="module"></div>');
                        $this.append(smodule);
                    }
                    // Modification des modules
                    smodule.css({
                        left: (mod.x * ratioX) + "px",
                        top: (mod.y * ratioY) + "px"
                    }).attr({
                        "data-module": mod.module
                    }).width(Math.max(12, mod.w * ratioX))
                    .height(Math.max(12, mod.h * ratioY));
                    // Marque le module comme défini
                    cModules[id] = true;
                }
            });
            // Supprime tous les modules qui n'ont pas été traités
            for (var key in cModules) {
                if (cModules.hasOwnProperty(key)) {
                    if(cModules[key]===false){
                        $(".module[data-id='"+key+"']", $this).remove();
                    }
                }
            }
        }, "json");

    };
    
    // Enregistrement d'un board
    var saveCommand = function(){
        var $this = $(this);
        var settings = $this.data("board-settings");
        if(!settings) return;
        // Extraction des informations de layout
        var layout = {
            size: {
                width: $this.width(),
                height: $this.height()
            },
            modules: []
        };
        $(".module", $this).each(function(){
            var $module = $(this);
            var p = $module.position();
            layout.modules.push({
                x: p.left,
                y: p.top,
                w: $module.width(),
                h: $module.height(),
                id: $module.data("id"),
                module: $module.data("module")
            });
        });
        // Transmission des données
        $.post(settings.api+"/board", layout);
    };
    
    // Définition du mode éditeur
    var setEditor = function($this){
        $(".module", $this).each(function(){
            setModuleEditor($this, $(this));
        });
    };
    var setModuleEditor = function($board, $module){
        // Si le module est déjà défini on passe
        if($module.data("module-settings")) return false;
        // Calcul des informations d'un module
        var msettings = {
        };
        $module.data("module-settings", msettings);
        // Activation du déplacement
        $module.draggable({
            containment: "parent",
            snap: true,
            snapMode: 'outer',
            stop: function () {
                saveCommand.call($board);
            }
        });
        // Activation du dimensionnement
        $module.resizable({
            containment: "parent",
            stop: function () {
                saveCommand.call($board);
            }
        });
        // Activation de la fermeture
        $module.append($("<div class='handle-close'>&times;</div>"));
        $(".handle-close", $module).click(function(e){
            alert("Suppression du module");
        });
    };
    
})(jQuery);
