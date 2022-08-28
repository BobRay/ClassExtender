<?php

/* MODX 2 provides an include in the classes
   so this needs require_once */
function ce_autoload($class) {
    global $modx;
    $modelPath = $modx->getOption('core_path') .
        'components/classextender/model/';

    /* Get directories from System Setting
       and convert to array */
    $dirString = $modx->getOption('ce_autoload_directories', null,
        'extendeduser,extendedresource', true);

    $directories = explode(',', $dirString);

    if (strpos($class, '\\') !== false) {
        /* Namespaced */
        $file = basename($class);
    } else {
        /* non-namespaced */
        $file = $class;
    }

    foreach ($directories as $dir) {
        $dir .= '/';
        $paths = array(
            $modelPath . $dir . $file . '.php',
            $modelPath . $dir . 'mysql/' . $file . '.php',
            $modelPath . $dir . $file . '.class.php',
            $modelPath . $dir . 'mysql/' . $file . '.class.php',
        );

        foreach ($paths as $path) {
            if (is_readable($path)) {
                require_once $path;
            }
        }
    }
}

spl_autoload_register('ce_autoload');