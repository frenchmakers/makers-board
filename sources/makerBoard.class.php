<?php

class makerBoard {

	var $style;
	var $modules;
	var $title;

	function init(){
		$this->title = 'testrere';
		$this->loadModule();
	}
	
	function loadModule(){
		$i = 0;
		$dir = opendir('modules');
		while ($file = readdir($dir)) {
			if ($file == '.' || $file == '..') {
                        	continue;
                  	}
			$json_data = file_get_contents("modules/$file/module.json");
			$config = json_decode($json_data, true);
   			$this->modules[$i] = '<div id="num'. $i .'">
			    		<script type="text/javascript">activate_module("num' . $i .'","' . $file  . '", "' . $config["file"] . '", "1");</script>
    				</div>';
			$i++;
                }
                closedir($dir);
	}

}

