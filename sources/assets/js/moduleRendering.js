function activate_module(name, directory,type, seconds, args) {
  	render_module(name,directory, type, args);
  	if (seconds > 0) {
    		setInterval("render_module('" + name + "', '" + directory + "', '"+type+"', '"+args+"')", (seconds * 1000));
	}
}

function render_module(name, directory, type, args) {
        $('#' + name).load('modules/'+ directory + '/'  + type + '/?' + args);
}
