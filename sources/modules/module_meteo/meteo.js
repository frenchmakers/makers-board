(function($){
    var refreshMeteo=function(){
    	$.refreshModule('module_meteo', function(data){
	      	$.module('meteo').html(data);
	});
    };
    var meteo=function(){
        setTimeout(function () {
            refreshHorloge();
            meteo();
        }, 100000);
    };
    refreshMeteo();
    meteo();
})(jQuery);
