<?php

function ce_autoload($class) {
    global $modx;
    $debug = true;
    $corePath = $modx->getOption('core_path');
    $modx->log(modX::LOG_LEVEL_ERROR, 'Class: ' . $class);


    $modelPath = $corePath .
        'components/classextender/model/';

    if (strpos($class, '\\') !== false) {
        /* Namespaced */
        /* $filePath includes class name */
        $filePath = str_replace('\\', '/', $class);


        $path = $modelPath . $filePath . '.php';
        $modx->log(modX::LOG_LEVEL_ERROR, 'Path: ' . $path);
        if (@is_readable($path)) {
            require $path;
            if ($debug) {
                $modx->log(modX::LOG_LEVEL_ERROR, 'Found');
            }
            return;
        }
        $path = $modelPath . $filePath . '.class.php';
        if ($debug) {
            $modx->log(modX::LOG_LEVEL_ERROR, 'Path: ' . $path);
        }
        if (@is_readable($path)) {
            require $path;
            return;
        }

    } else { /* non-namespaced */

        /* Get directories from System Setting
       and convert to array */
        $dirString = $modx->getOption('ce_autoload_directories', null,
            'extendeduser,extendedresource', true);

        $directories = explode(',', $dirString);

        $file = $class;
        $paths = array(
            '/mysql/' . $file . '.class.php',
            '/mysql/' . $file . '.php',
        );

        foreach ($directories as $dir) {

            $paths = array(
                $modelPath . $dir . '/mysql/' . $file . '.class.php',
                $modelPath . $dir . '/mysql/' . $file . '.php',
            );

            foreach ($paths as $path) {
                if (@is_readable($path))
                $modx->log(modX::LOG_LEVEL_ERROR, 'Path: ' . $path);
                if (@is_readable($path)) {
                    $modx->log(modX::LOG_LEVEL_ERROR, 'Found');
                    require_once $path;
                    /* Stop looking */
                    return;
                }
            }
        }
    }
}

spl_autoload_register('ce_autoload');
