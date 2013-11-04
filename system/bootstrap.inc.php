<?php

    /* Bootstrapping the site */
    _jsmart_constants_initialize();
    
    
    /* Site files */
    require_once SITE_DEFAULT_FOLDER_PATH . 'settings.php';
    require_once SITE_DEFAULT_FOLDER_PATH . 'constants.inc.php';
    require_once SITE_DEFAULT_FOLDER_PATH . 'includes.inc.php';

    /* Load System Files */
    require_once INCLUDES_PATH . 'functions.inc.php';
    require_once CLASSES_PATH . 'Database.php';
    require_once CLASSES_PATH . 'JSmart.php';
    require_once CLASSES_PATH . 'ScreenMessage.php';
    require_once CLASSES_PATH . 'JModule.php';
    require_once CLASSES_PATH . 'JModuleManager.php';
    require_once CLASSES_PATH . 'EMail.php';
    require_once CLASSES_PATH . 'Image.php';
    require_once CLASSES_PATH . 'Session.php';
    require_once CLASSES_PATH . 'Template.php';
    require_once CLASSES_PATH . 'Theme.php';
    require_once CLASSES_PATH . 'JUser.php';
    require_once CLASSES_PATH . 'User.php';
    require_once CLASSES_PATH . 'HTML.php';
    require_once CLASSES_PATH . 'JPager.php';
    require_once CLASSES_PATH . 'System.php';
    require_once CLASSES_PATH . 'JPath.php';
    require_once CLASSES_PATH . 'Role.php';
    require_once CLASSES_PATH . 'URL.php';
    require_once THEME_PATH . 'theme.inc.php';


    /*
     * Add includes for different frameworks being used
     */
    
    $THEME->addScript(LIBRARIES_URL . "jquery/jquery-2.0.3.min.js");
    $THEME->addCss(LIBRARIES_URL . "jqueryui/jquery-ui.css");
    $THEME->addCss("http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css");
    $THEME->addScript("http://code.jquery.com/ui/1.10.3/jquery-ui.js");
    $THEME->addScript(LIBRARIES_URL . "other/css-browser-selector.js");

    function _jsmart_constants_initialize()
    {
       /* Add our constants that are commonly used and will be used a lot throughout the site */

       /* Generating our Base Path and Base URL */
       $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https://' : 'http://';
       $host = $_SERVER['HTTP_HOST'];

       /* Is the site in a specific folder within your web directory */
       define("SITE_FOLDER", "jsmart");
       
       define("BASE_URL", rtrim($protocol . $host . '/' . SITE_FOLDER, '/') . '/');
       define("BASE_PATH", rtrim($_SERVER['DOCUMENT_ROOT'] . '/' . SITE_FOLDER, '/') . '/');
       define("SITE_DEFAULT_FOLDER_PATH", BASE_PATH . 'site/default/');
    }