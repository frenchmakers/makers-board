(function($) {
	var displayNextImage = function($module) {
		var $current = $("image:visible", $module).first();
		var $next = $current.next();
		if($next.length == 0) $next = $("image", $module).first();
		$("image", $module).hide();
		$next.show();
	};
	var displayNextImageToAllModules=function(){
		$(".module[data-module='image']").each(function(){
			displayNextImage($(this));
		});
	};
	setInterval(displayNextImageToAllModules, 5*1000);
	$(document).on('module.image.loaded', function(e, $module){
		displayNextImage($module);
	});
})(jQuery);
