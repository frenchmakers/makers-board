(function($){
    setInterval(function(){
        $(".module").board("module.load");
    }, 15 * 60 * 1000);
    //var refreshMeteo=function(){
    //	$.refreshModule('module_meteo', function(data){
	//      	$.module('meteo').html(data);
	//});
    //};
    //var meteo=function(){
    //    setTimeout(function () {
    //        refreshHorloge();
    //        meteo();
    //    }, 100000);
    //};
    //refreshMeteo();
    //meteo();
})(jQuery);
