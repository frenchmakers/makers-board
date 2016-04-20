<?php 

class makerBoard {
    
    private $_Modules = NULL;
    
    /*
        Calcul le dossier de données d'un tableau de board
    */
    private function getBoardDataFolder($board) {
        return __DIR__."/datas/boards/$board";
    }
    
    /*
        Lecture d'un contenu de tableau de bord
    */
    private function readBoardDataFile($board, $file) {
        $folder = $this->getBoardDataFolder($board);
        $file = "$folder/$file";
        return is_file($file) ? file_get_contents($file) : FALSE;
    }
    
    /*
        Ecriture d'un contenu de tableau de bord
    */
    private function writeBoardDataFile($board, $file, $content) {
        $folder = $this->getBoardDataFolder($board);
        $file = "$folder/$file";
        if(!is_dir($folder)) mkdir($folder, 0777, true);
        file_put_contents($file, $content);
    }
    
    /*
        Lecture du layout d'un tableau de bord
    */
    public function getBoardLayout($board) {
        return $this->readBoardDataFile($board, "layout.json");
    }
    
    /*
        Ecriture du layout d'un tableau de bord
    */
    public function setBoardLayout($board, $json) {
        $layout = json_decode($json);
        // Calcul la valeur de mise à jour
        $lastUpdate = $this->getBoardLastUpdate($board);
        if($lastUpdate === FALSE) $lastUpdate = 1;
        else $lastUpdate++;
        $layout->lastUpdate = $lastUpdate;
        // Enregistrement des modifications
        $this->writeBoardDataFile($board, "layout.json", json_encode($layout));
        $this->setBoardLastUpdate($board, $lastUpdate);
    }
    
    /*
        Lecture de la dernière mise à jour d'un tableau de bord
    */
    public function getBoardLastUpdate($board) {
        return $this->readBoardDataFile($board, "last-update.txt");
    }
    
    /*
        Ecriture de la dernière mise à jour d'un tableau de bord
    */
    public function setBoardLastUpdate($board, $lastUpdate) {
        return $this->writeBoardDataFile($board, "last-update.txt", $lastUpdate);
    }
    
    /*
        Lecture de la configuration d'un module dans un board
    */
    public function getBoardModuleConfig($board, $module) {
        $layout = $this->getBoardLayout($board);
        if($layout === false) return false;
        $layout = json_decode($layout);
        foreach ($layout->modules as $mod) {
            if($mod->id == $module) return $mod;
        }
        return false;
    }
    
    /*
        Retourne la liste des modules disponibles
    */
    public function getModules() {
        if($this->_Modules === NULL) {
            $this->_Modules = array();
            $dir = opendir('modules');
            while ($cnt = readdir($dir)) {
                if ($cnt == '.' || $cnt == '..') {
                    continue;
                }
                $configFile = "modules/$cnt/module.json";
                if (!is_file($configFile)) {
                    continue;
                }
                $json_data = file_get_contents($configFile);
                $config = json_decode($json_data, true);
                $config["folder"] = "modules/$cnt";
                $config["config"] = file_exists("modules/$cnt/config.php") === true;
                $this->_Modules[$config['code']] = $config;
            }
            closedir($dir);            
        }
        return $this->_Modules;
    }
    
    /*
        Retourne la définition d'un module
    */
    public function getModule($code) {
        $modules = $this->getModules();
        return isset($modules[$code]) ? $modules[$code] : false;
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
