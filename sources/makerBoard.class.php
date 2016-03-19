<?php

class makerBoard {

	var $style;
	var $modules;
	var $title;
        var $configs = array();

	function init(){
            $json_data = file_get_contents(dirname(__FILE__)."/config/application.json");
            $config = json_decode($json_data, true);
            $this->title = $config["title"];
            $this->loadModule();
	}
	
	function loadModule(){
		$i = 0;
		$dir = opendir('modules');
		while ($file = readdir($dir)) {
			if ($file == '.' || $file == '..' || is_dir($file)) {
                        	continue;
                  	}
                        $configFile = "modules/$file/module.json";
                        if(!is_file($configFile)) {  
                            continue;
                        }
			$json_data = file_get_contents($configFile);
			$config = json_decode($json_data, true);
                        $this->configs[$file] = $config;
   			$this->modules[$i] = '<div id="num'. $i .'">
			    		<script type="text/javascript">activate_module("num' . $i .'","' . $file  . '", "' . $config["file"] . '", "1");</script>
    				</div>';
			$i++;
                }
                closedir($dir);
	}

}

