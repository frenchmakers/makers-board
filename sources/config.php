<?php
/**
 * Configuration du système
 */

// Dossier des données
$config['datas-folder'] = readEnv('APPSETTING_DATAS_FOLDER', __DIR__.'/datas');

// Utilitaire de lecture des paramètres dans les variables d'environnement
function readEnv($name, $default) {
    $result = getenv($name);
    if($result!==FALSE && $result!=='') return $result;
    return $default;
}
