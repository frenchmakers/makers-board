/**
 * Gestion du tableau de boad
 */
(function($){
    // Configuration
    var refreshDelay = 10 * 1000;               // Vérification toutes 10 secondes
    
    // Actualisation du dashboard
    var lastRefresh=$("body").data("last-refresh");
    var refreshDashboard = function(){
        $.get("board.php", { "cmd": "refresh", "_t":new Date().getTime() }, function(data){
            var rVal = parseInt(data);
            if(lastRefresh<rVal){
                document.location.reload();
                lastRefresh = rVal;
            }            
        });
    };
    
    // Définition du timer d'actualisation
    var timerRefreshDashboard = function(){
        setTimeout(function(){
            refreshDashboard();
            timerRefreshDashboard();
        }, refreshDelay);
    };
    
    // Démarrage d'une actualisation
    timerRefreshDashboard();
})(jQuery);
