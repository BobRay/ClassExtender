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
$chunkName = $modx->getOption('chunkName', $sp, '', true);
$dirPermission = $modx->getOption('dirPermission',
    $sp, 0755, true);
$dir = MODX_CORE_PATH . 'components/' .
    $packageLower . '/temp';

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
    $output .= '<p>Could not get Manager</p>';
}
$generator = $manager->getGenerator();
if (!$generator) {
    $output .= '<p>Could not get Generator</p>';
}

if ($generator->writeSchema($fileName,
    $package, $baseClass, $tablePrefix, true)) {
    $output .= '<p>Schema written to ' . $fileName . '</p>';
} else {
    $output .= '<p>Error writing schema file - writeSchema() failed</p>';
}
$chunk = $modx->getObject($classPrefix . 'modChunk',
    array('name' => $chunkName));

if (!$chunk) {
    $output .= '<p>Creating Chunk' . $chunkName . '</p>';
    $chunk = $modx->newObject($classPrefix . 'modChunk');
    $chunk->set('name', $chunkName);
}
$content = file_get_contents($fileName);
if (empty($content)) {
    $output .= '<p>file_get_contents() failed' . '</p>';
}
$chunk->set('snippet', $content);

if (!$chunk->save()) {
    $output .= '<p>Could not save chunk' . '</p>';
} else {
    $output .= '<p>Saved schema in ' . $chunkName . ' chunk' . '</p>';
}

return $output;