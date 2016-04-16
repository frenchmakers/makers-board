<?php 

class makerBoard {
    /*
        Calcul le dossier de données d'un tableau de board
    */
    private function getBoardDataFolder($board) {
        return __DIR__."/datas/$board";
    }
    
    /*
        Lecture de la configuration d'un tableau de bord
    */
    public function getBoadLayout($board) {
        $folder = $this->getBoardDataFolder($board);
        $file = "$folder/layout.js";
        return is_file($file) ? file_get_contents($file) : FALSE;
    }
    
    /*
        Lecture de la dernière mise à jour d'un tableau de bord
    */
    public function getBoadLastUpdate($board) {
        $folder = $this->getBoardDataFolder($board);
        $file = "$folder/last-update.txt";
        return is_file($file) ? file_get_contents($file) : FALSE;
    }
    
}

class makerBoardX{    
    /*
        Lecture de la dernière actualisation du tableau de bord
    */
    public function getLastRefresh() {
        $file = __DIR__."/datas/refresh-dashboard";
        if (is_file($file)) {
            return file_get_contents($file);
        } else {
            return "0";
        }
    }

    /*
        Actualisation du tableau de bord
    */
    public function setLastRefresh() {
        $file = __DIR__."/datas/refresh-dashboard";
        file_put_contents($file, time());
    }

    /*
        Enregistrement du layout de tableau de bord
    */
    public function writeBoardLayout($layout) {
        $config = $this->readBoardConfig();
        $config["layout"] = json_decode($layout, TRUE);
        $configData = json_encode($config);
        $file = __DIR__."/datas/board.json";
        file_put_contents($file, $configData);
    }

    /*
        Lecture du layout de tableau de bord
    */
    public function readBoardConfig() {
        $file = __DIR__."/datas/board.json";
        $configData = is_file($file) ? file_get_contents($file) : FALSE;
        if ($configData !== FALSE) {
            $config = json_decode($configData, TRUE);
        } else {
            $config = array("title" => "", "layout" => array());
        }
        return $config;
    }
    
    /*
        Recherche un module d'après un code 
    */
    public function findModule($module){
        foreach ($this->configs as $mod) {
            if($mod['code']==$module) return $mod;
        }
        return false;
    }

    var $style;
    var $modules;
    var $title;
    var $lastRefresh;
    var $configs = array();

    function init() {
        $json_data = file_get_contents(dirname(__FILE__)."/config/application.json");
        $config = json_decode($json_data, true);
        $this->title = $config["title"];

        $file = __DIR__."/datas/refresh-dashboard";
        if (is_file($file)) {
            $this->lastRefresh = file_get_contents($file);
        } else {
            $this->lastRefresh = "0";
        }

        $this->loadModule();
    }

    function loadModule() {
        $i = 0;
        $dir = opendir('modules');
        while ($file = readdir($dir)) {
            if ($file == '.' || $file == '..' || is_dir($file)) {
                continue;
            }
            $configFile = "modules/$file/module.json";
            if (!is_file($configFile)) {
                continue;
            }
            $json_data = file_get_contents($configFile);
            $config = json_decode($json_data, true);
            if(!isset($config["w"])) $config["w"] = 1;
            if(!isset($config["h"])) $config["h"] = 1;
            $config["config"] = file_exists("modules/$file/config.php") === true;
            $this->configs[$file] = $config;
            //$this->modules[$i] = '<div id="num'.$i.'">
			//    		<script type="text/javascript">activate_module("num'.$i.'","'.$file.'", "'.$config["file"].'", "1");</script>
    		//		</div>';
            $i++;
        }
        closedir($dir);
    }

}
