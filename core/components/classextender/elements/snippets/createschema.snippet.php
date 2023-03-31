<?php
/**
 * CreateSchema Snippet
 * Copyright 2023 Bob Ray
 * Created on 03/01/2023
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

$corePath = $this->modx->getOption('ce.core_path', NULL,
    $this->modx->getOption('core_path'));

$modelPath = $corePath . 'components/classextender/';

$schemaPath = $modelPath . $packageLower . '/' . 'schema';

$dir = $schemaPath;

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
    $output .= '<h3 style="color:red">Could not get Manager</h3>';
}
$generator = $manager->getGenerator();
if (!$generator) {
    $output .= '<h3 style="color:red">Could not get Generator</h3>';
    return $output;
}

if ($generator->writeSchema($fileName,
    $package, $baseClass, $tablePrefix, true)) {
    $output .= '<h3 style="color:green">Schema written to ' . $fileName . '</h3>';
} else {
    $output .= '<h3 style="color:red">Error writing schema file - writeSchema() failed</h3>';
    return $output;
}
$chunk = $modx->getObject($classPrefix . 'modChunk',
    array('name' => $chunkName));

if (!$chunk) {
    $output .= '<h3 style="color:green">Creating Chunk' . $chunkName . '</h3>';
    $chunk = $modx->newObject($classPrefix . 'modChunk');
    $chunk->set('name', $chunkName);
}
$content = file_get_contents($fileName);
if (empty($content)) {
    $output .= '<h3 style="color:red">file_get_contents() failed' . '</h3>';
    return $output;    
}
$chunk->set('snippet', $content);

if (!$chunk->save()) {
    $output .= '<h3 style="color:red">Could not save chunk' . '</h3>';
    return $output;
} else {
    $output .= '<h3 style="color:green">Saved schema in ' . $chunkName . ' chunk' . '<h3>';
}

return $output;