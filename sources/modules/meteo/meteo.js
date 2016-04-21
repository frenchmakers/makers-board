(function($){
    setInterval(function() {
        $(".module[data-module='meteo']").board("module.load");
    }, 15 * 60 * 1000);
})(jQuery);
