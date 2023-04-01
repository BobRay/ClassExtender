<?php

function ce_autoload($class) {

    /* Bail out if 'data' isn't in class name */
    if (strpos(strtolower($class), 'data') === false) {
        return;
    }
    global $modx;
    $debug = false;
    $ds = DIRECTORY_SEPARATOR;

    $corePath = $modx->getOption('core_path');
    if ($debug) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'Class: ' . $class);
    }

    $modelPath = $corePath .
        'components' . $ds . 'classextender' . $ds . 'model' . $ds;

    if (strpos($class, '\\') !== false) {
        /* Namespaced */
        /* $filePath includes class name */
        $filePath = str_replace('\\', DIRECTORY_SEPARATOR, $class);


        $path = $modelPath . $filePath . '.php';
        if ($debug) {
            $modx->log(modX::LOG_LEVEL_ERROR, 'Path: ' . $path);
        }
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
            '', true);
        if (empty($dirString)) {
            return;
        }
        $directories = explode(',', $dirString);

        $file = $class;

        $paths = array(
           $ds . 'mysql' . $ds . $file . '.class.php',
           $ds . 'mysql' . $file . '.php',
        );

        foreach ($directories as $dir) {

            $paths = array(
                $modelPath . $dir . $ds . 'mysql' .$ds . $file . '.class.php',
                $modelPath . $dir . $ds . 'mysql' . $ds . $file . '.php',
            );

            foreach ($paths as $path) {
                if (@is_readable($path))
                $modx->log(modX::LOG_LEVEL_ERROR, 'Path: ' . $path);
                if (@is_readable($path)) {
                    $modx->log(modX::LOG_LEVEL_ERROR, 'Found');
                    require $path;
                    /* Stop looking */
                    return;
                }
            }
        }
    }
}

spl_autoload_register('ce_autoload');
