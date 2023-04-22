<?php
/**
 * CreateSchema Snippet
 * Copyright 2023 Bob Ray <https://bobsguides.com>
 * Created on 03/01/2023
 *
 * ClassExtender is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * ClassExtender is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ClassExtender; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package classextender
 */

/**
 * Description - Creates a schema file from one or more
 * database tables. Writes schema to file and specified chunk.
 * Properties
 * ---------
 * @var string $package - Package name
 * @var string $packageLower - lowercase package name
 * @var string $tablePrefix - Prefix of your table
 * @var string $baseClass - MODX object being extended
 *     (e.g., xPDOSimpleObject)
 * @var string $fileName - Name of schema file to be
 *     written (without path). Goes to
 *     core/components/{package}/temp/schema/{fileName}
 * @var string $chunkName - Name of chunk to write schema to
 * @var array $sp - used as shorthand for $scriptProperties
 * @var $scriptProperties array sent to snippet
 * @var int $dirPermission - permission for temp directory
 * @var modX $modx - modX object
 */

/* Set class prefix for MODX 2 or MODX 3 */
$classPrefix = $modx->getVersionData()['version'] >= 3
    ? 'MODX\Revolution\\'
    : '';
$modx->lexicon->load('classextender:default');
$modx->lexicon->load('classextender:form');
$output = '';
$sp = $scriptProperties;
$package = $modx->getOption('package', $sp);
$packageLower = strtolower($package);
$tablePrefix = $modx->getOption('tablePrefix', $sp,
    'ext_', true);
$baseClass = $modx->getOption('baseClass', $sp,
    'xPDOSimpleObject', true);
$fileName = $modx->getOption('fileName', $sp, $packageLower .
    '.mysql.schema.xml', true);
    
   

$defaultChunk = $packageLower . 'SchemaTpl';
$chunkName = $modx->getOption('chunkName', $sp, $defaultChunk, true);

$dirPermission = $modx->getOption('dirPermission',
    $sp, 0755, true);

$corePath = $modx->getOption('ce.core_path', NULL,
    $modx->getOption('core_path'));

$modelPath = $corePath . 'model/';

$schemaPath = $modelPath . 'schema';

$dir = $schemaPath;

$displayPath = 'core/components/classextender/<br>' . '&nbsp;&nbsp;&nbsp;&nbsp;' . 'model/schema/' . $fileName;

$cssFile = $modx->getOption('cssFile', $sp,
    '[[++assets_url]]components/classextender/css/classextender.css',
     true);

if (!empty($cssFile)) {
    $modx->regClientCSS($cssFile);
}

if (isset($_POST['submitVar']) && $_POST['submitVar'] == 'submitVar') {
    /* Create directory if it doesn't exist */
    if (!is_dir($dir)) {
        $oldMask = umask(0);
        mkdir($dir, 0755, true);
        umask($oldMask);
        clearstatcache();
    }

    $fileName = $dir . '/' . $fileName;

    $manager = $modx->getManager();
    if (!$manager) {
        $output .= '<h3 style="color:red">' .
            $modx->lexicon('ce.could_not_get_manager') . '</h3>';
        return $output;
    }

    $generator = $manager->getGenerator();

    if (!$generator) {
        $output .= '<h3 style="color:red">' .
            $modx->lexicon('ce.could_not_get_generator') . '</h3>';
        return $output;
    }


    if ($generator->writeSchema($fileName,
        $package, $baseClass, $tablePrefix, true)) {
        $output .= '<h3 style="color:green">' .
            $modx->lexicon('ce.schema_written_to_file') . ':<br>' . $displayPath . '</h3>';
    } else {
        $output .= '<h3 style="color:red">' .
            $modx->lexicon('write_schema_failed') . '</h3>';
        return $output;
    }
    $chunk = $modx->getObject($classPrefix . 'modChunk',
        array('name' => $chunkName));

    $catObj = $modx->getObject($classPrefix . 'modCategory', array('category' => 'ClassExtender'));
    $categoryId = $catObj ? $catObj->get('id') : 0;

    if (!$chunk) {
        $output .= '<h3 style="color:green">' .
            $modx->lexicon('ce.creating_chunk') .
            ': ' . $chunkName . '</h3>';
        $chunk = $modx->newObject($classPrefix . 'modChunk');
        $chunk->set('name', $chunkName);
    }
    $chunk->set('category', $categoryId);

    $content = file_get_contents($fileName);
    if (empty($content)) {
        $output .= '<h3 style="color:red">' .
            $modx->lexicon('ce.file_get_contents_failed') . '' . '</h3>';
        return $output;
    }
    $chunk->set('snippet', $content);

    if (!$chunk->save()) {
        $output .= '<h3 style="color:red">' .
            $modx->lexicon('ce.could_not_save_chunk') . $chunkName . '</h3>';
        return $output;
    } else {
        $output .= '<h3 style="color:green">' .
            $modx->lexicon('ce.saved_schema') . $chunkName . '</h3>';
    }

    return $output;
} else {
    /* Not a submission; Display Form */
    return $modx->getChunk('CreateSchemaForm', $sp);


}