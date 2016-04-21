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

        // Application du module
        return this.each(function () {
            var $this = $(this);
            switch (command) {
                case "enable":
                    $.fn.board.commands.enable.call($this, options);
                    break;
                case "refresh":
                    $.fn.board.commands.refresh.call($this, options);
                    break;
                case "load":
                    $.fn.board.commands.load.call($this, options);
                    break;
                case "module.add":
                    $.fn.board.commands.moduleAdd.call($this, options);
                    break;
                case "module.load":
                    $.fn.board.commands.moduleLoad.call($this, options);
                    break;
                case "module.delete":
                    $.fn.board.commands.moduleDelete.call($this, options);
                    break;
                case "api":
                    $.fn.board.commands.api.call($this, options);
                    break;
            }
        });
    };

    // Valeurs par défaut
    $.fn.board.defaults = {
        'enable': {
            api: "api.php",
            editable: false,
            refresh: 5 * 1000,
            board: function(){ return $(this).data("board"); },
            error: function(message){ if(console) console.log(message); },
            moduleTemplate : '<div class="module" data-module="{{module}}" title="{{title}}"></div>',
            moduleAdded: function($module){ },
            moduleDeleted: function($module){ }
        },
        'api': {
            method: "GET",
            data: null,
            path: "/",
            callType: "",
            dataType: "json",
            module: 0,
            beforeSend: null,
            done: null,
            fail: null,
            always: null
        }
    };
    
    // Commandes
    $.fn.board.commands = {
        // Activation d'un board
        'enable': function (settings) {
            var $this = $(this);
            // Si le board est déjà défini on passe
            if ($this.data("board-settings"))
                return false;
            // Enregistrement des paramètres
            settings = $.extend({}, $.fn.board.defaults.enable, settings);
            $this.data("board-settings", settings);
            // Activation du rafraichissement automatique
            if (settings.refresh !== false && settings.refresh > 0) {
                $.fn.board.commands.refresh.call($this);
                setInterval(function () {
                    $.fn.board.commands.refresh.call($this);
                }, settings.refresh);
            }
        },
        // Actualisation d'un board
        'refresh': function (options) {
            var $this = $(this);
            var settings = $this.data("board-settings");
            if(!settings) return;
            // Provoque un ping
            $this.board("api",{
                callType: "board",
                path: "/ping",
                dataType: "text",
                data: {
                    _t: new Date().getTime()
                },
                done: function(data){
                    // Si la valeur de dernière mise à jour à changée alors on charge le tableau
                    if(data!=$this.data("lastUpdate")) {
                        $this.data("lastUpdate", data);
                        $.fn.board.commands.load.call($this, options);
                    }
                }
            });
        },
        // Chargement d'un board
        'load': function (options) {
            var $this = $(this);
            var settings = $this.data("board-settings");
            if(!settings) return;
            options = $.extend({
                "sendBoardSize": false
            }, options);
            // 
            var sdata = {
                _t: new Date().getTime()
            };
            if(options.sendBoardSize) {
                sdata.dw = $this.width();
                sdata.dh = $this.height();
            }
            var boardName = getBoardName($this, settings);
            $.get(settings.api + "/board/" + boardName, sdata, function (data) {
                // Erreur ?
                if(data.status && data.status=="error") {
                    if($.isFunction(settings.error)) {
                        settings.error.call($this, data.message);
                    }
                    return;
                }
                // Enregistre la dernière mise à jour
                if(data.lastUpdate){
                    $this.data("lastUpdate", data.lastUpdate);
                }
                // Enregistre la taille de référence
                $this.attr({
                    "data-screen-width": options.width,
                    "data-screen-height": options.height
                });
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
                            smodule = $.fn.board.commands.moduleAdd.call($this, {
                                "id": id,
                                "module": mod.module,
                                "title": mod.title 
                            });
                        }
                        // Modification des modules
                        smodule.css({
                            left: (mod.x * ratioX) + "px",
                            top: (mod.y * ratioY) + "px"
                        }).attr({
                            "data-module": mod.module
                        }).outerWidth(Math.max(12, mod.w * ratioX))
                        .outerHeight(Math.max(12, mod.h * ratioY));
                        // Marque le module comme défini
                        cModules[id] = true;
                    }
                });
                // Supprime tous les modules qui n'ont pas été traités
                for (var key in cModules) {
                    if (cModules.hasOwnProperty(key)) {
                        if(cModules[key]===false){
                            $.fn.board.commands.moduleDelete.call($this, {
                                "module": key
                            });
                        }
                    }
                }
                // Evénément 'done' ?
                if($.isFunction(options.done)) {
                    options.done.call($this, data);
                }
                
                // Evénément 'board.loaded'
                $this.trigger('board.loaded');
            }, "json");
        },    
        // Insertion d'un module 
        'moduleAdd': function(options) {
            var $this = $(this);
            var settings = $this.data("board-settings");
            if(!settings) return false;
            
            options = $.extend({
                "id": 0,
                "module": "",
                "title": "",
                "done": function($board, $module){},
                "save": true
            }, options);
            
            // Calcul le prochain id si nécessaire
            if(options.id === 0) {
                var lastId = 0;
                $(".module", $this).each(function(){
                    var mid = parseInt($(this).data("id"));
                    if(!isNaN(mid)) lastId = Math.max(mid, lastId);
                });
                options.id = lastId + 1;            
            }
            
            // Création du module
            $module = $( parseTemplate(settings.moduleTemplate, options) );
            $module.attr("data-id", options.id);
            $module.attr("data-module", options.module);
            $module.attr("data-title", options.title != "" ? options.title : options.module);
            $module.data("module-settings", {
                'board': $this
            });
            
            // Insertion 
            $this.append($module);
            
            // Evénément 'done'
            if($.isFunction(options.done)) {
                options.done($this, $module);
            }
            
            // Evénément 'module.added'
            if($.isFunction(settings.moduleAdded)) {
                settings.moduleAdded.call($this, $module);
            }
            $this.trigger('module.added', $module);
            
            return $module;
        },        
        // Chargement du contenu d'un module 
        'moduleLoad': function(options) {
            var $this = $(this);
            var settings = $this.data("board-settings");
            if(!settings) return false;
            
            options = $.extend({module: 0}, options);
            
            $this.board('api', {
                callType: 'module',
                module: options.module,
                dataType: 'html',
                done: function(data) {
                    $(".module[data-id='"+options.module+"']").html(data);
                }
            });
        },
        // Suppression d'un module 
        'moduleDelete': function(options) {
            var $this = $(this);
            var settings = $this.data("board-settings");
            if(!settings) return false;
            
            // Construction des options
            console.log(options);
            if(!($.isArray(options) || $.isPlainObject(options))){
                options = {
                    "module": options
                };
            }
            console.log(options);
            options = $.extend({
                "done": function($board, $module){}
            }, options);
            console.log(options);
            // Extraction du module
            var $module = options.module;
            if(!($module instanceof jQuery)){
                $module = $(".module[data-id='"+$module+"']", $this);  
            }
            if($module.length==0) return;
            
            // Supprime le module
            $module.remove();
            
            // Evénément 'done'
            if($.isFunction(options.done)) {
                options.done($this, $module);
            }
            
            // Evénément 'module.deleted'
            if($.isFunction(settings.moduleDeleted)) {
                settings.moduleDeleted.call($this, $module);
            }
            $this.trigger('module.deleted', $module);
        },
        // Appel d'api
        'api': function(options){
            var $this = $(this);
            var settings = $this.data("board-settings");
            if(!settings) return;
            
            // Calcul des options
            options = $.extend({}, $.fn.board.defaults.api, options);
            options.data = $.extend({ _t: new Date().getTime()}, options.data);
            
            // Calcul l'url a appeler
            var url = settings.api;
            if(options.callType == "board") {
                var boardName = getBoardName($this, settings);
                url += "/board/" + boardName;
            } else if(options.callType == "module") {
                var boardName = getBoardName($this, settings);
                url += "/board/" + boardName + "/module/" + options.module;
            } 
            url += options.path;
            
            // Provoque l'appel
            $.ajax({
                url: url,
                type: options.method.toUpperCase(),
                data: ($.isPlainObject(options.data) && options.method.toUpperCase() == "POST" ) ? JSON.stringify(options.data) : options.data,
                dataType: options.dataType,
                beforeSend: function(xhr) {
                    if($.isFunction(options.beforeSend))
                        options.beforeSend.call($this, xhr);
                }
            }).done(function(data, textStatus, jqXHR){
                if($.isFunction(options.done))
                    options.done.call($this, data, textStatus, jqXHR);
            }).fail(function(jqXHR, textStatus, errorThrown){
                if($.isFunction(options.fail))
                    options.fail.call($this, jqXHR, textStatus, errorThrown);
            }).always(function(data, textStatus, jqXHR){
                if($.isFunction(options.always))
                    options.always.call($this, data, textStatus, jqXHR);
            });
        }
    };
    
    // Extraction du nom du tableau de bord
    var getBoardName = function($board, settings){
        if(settings && settings.board){
            if($.isFunction(settings.board)){
                return settings.board.call($board);
            }
            return settings.board;
        }
        return $($board).data("board");
    };
    
    // Traitement des templates
    var parseTemplate = function(template, data){
        return template.replace(/\{\{[\w._-]+\}\}/gi, function(tag){
            var n = tag.substring(2, tag.length-2);
            return (n in data) ? data[n] : "";
        });
    };
    
})(jQuery);
