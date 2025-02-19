<?php

class ClassFix {
    protected string $base = '';
    protected string $prefix = '';
    protected modX $modx;
    protected string $uSearch = 'userData';
    protected string $uReplace = 'UserData';
    protected string $rSearch = 'resourceData';
    protected string $rReplace = 'ResourceData';

    protected array $searchArray = array();
    protected array $replaceArray = array();

    protected $objects = array();

    function __construct($modx) {
        $this->modx = $modx;

    }

    public function init() {

        $modx = $this->modx;
        $this->base = MODX_CORE_PATH . 'components/classextender/model/';

        $this->prefix = $modx->getVersionData()['version'] >= 3
            ? 'MODX\Revolution\\'
            : '';

        /*$uSearch = 'UserData';
        $uReplace = 'userData';
        $rSearch = 'ResourceData';
        $rReplace = 'resourceData';*/

        $this->searchArray = array(
            $this->uSearch,
            $this->rSearch,
        );
        $this->replaceArray = array(
            $this->uReplace,
            $this->rReplace,
        );

        $this->objects = array (
            'files' => array(
                /* User files */
                'extendeduser/metadata.mysql.php',
                'extendeduser/mysql/userdata.class.php',
                'extendeduser/mysql/userdata.map.inc.php',
                'extendeduser/userdata.class.php',
                'schema/extendeduser.mysql.schema.xml',

                /* Resource Files */
                'extendedresource/metadata.mysql.php',
                'extendedresource/mysql/resourcedata.class.php',
                'extendedresource/mysql/resourcedata.map.inc.php',
                'extendedresource/resourcedata.class.php',
                'schema/extendedresource.mysql.schema.xml',
            ),
            'resources' => array(
                'Extend modUser',
                'Extend modResource',
                'My Extend modUser',
                'My Extend modResource',
                'MyExtend modUser',
                'MyExtend modResource',
            ),
            'chunks' => array(
                'ExtUserSchema',
                'MyExtUserSchema',
                'ExtResourceSchema',
                'MyExtResourceSchema',
                'My ExtResourceSchema',
                'My ExtUserSchema',
            ),
            'snippets' => array(
                'GetExtUsers',
                'SetUserPlaceholders',
                'GetExtResources',
                'MyGetExtUsers',
                'MySetUserPlaceholders',
                'MyGetExtResources',
                'My GetExtResources',
                'My GetExtUsers',
                'My SetUserPlaceholders',
            ),
        );
    }

    public function process() {
        $this->doFiles();
        $this->doResources();
        $this->doChunks();
        $this->doSnippets();

    }

    public function doFiles() {
        $count = 0;
        foreach ($this->objects['files'] as $file) {
            $file = $this->base . $file;
            if (file_exists($file)) {
                $content = file_get_contents($file);
                if ($this->checkContent($this->searchArray, $content)) {
                    $content = str_replace($this->searchArray, $this->replaceArray, $content);
                    file_put_contents($file, $content);
                    $count++;
                }
            }
        }
        if ($count) {
            $this->output($count . ' Class names updated in files');
        }
    }

    public function doResources() {
        $count = 0;
        foreach ($this->objects['resources'] as $resource) {
            $resourceObj = $this->modx->getObject($this->prefix . 'modResource', array('pagetitle' => $resource));
            if ($resourceObj) {
                $content = $resourceObj->getContent();
                if ($this->checkContent($this->searchArray, $content)) {

                    $content = str_replace($this->searchArray, $this->replaceArray, $content);
                    $resourceObj->setContent($content);
                    $resourceObj->save();
                    $count++;
                }
            }
        }
        if ($count) {
            $this->output($count . ' Class names updated in resources');
        }
    }

    public function doChunks() {
        $count = 0;
        foreach ($this->objects['chunks'] as $chunk) {
            $chunkObj = $this->modx->getObject($this->prefix . 'modChunk', array('name' => $chunk));
            if ($chunkObj) {
                $content = $chunkObj->getContent();
                if ($this->checkContent($this->searchArray, $content)) {
                    $content = str_replace($this->searchArray, $this->replaceArray, $content);
                    $chunkObj->setContent($content);
                    $chunkObj->save();
                    $count++;
                }
            }
        }
        if ($count) {
            $this->output($count . ' Class names updated in chunks');
        }
    }

    public function doSnippets() {
        $snippetCount = 0;
        $propertyCount = 0;
        $propertySetCount = 0;

        foreach ($this->objects['snippets'] as $snippet) {
            $snippetObj = $this->modx->getObject($this->prefix . 'modSnippet', array('name' => $snippet));
            if ($snippetObj) {
                $dirty = false;
                $content = $snippetObj->getContent();
                if ($this->checkContent($this->searchArray, $content)) {
                    $content = str_replace($this->searchArray, $this->replaceArray, $content);
                    $snippetObj->setContent($content);
                    $dirty = true;
                    $snippetObj->save();
                    $snippetCount++;
                }
                $properties = $snippetObj->getProperties();
                $x = $snippetObj->get('properties');
                if (isset($properties['UserDataClass'])) {
                    if ($properties['UserDataClass'] === $this->uSearch) {
                        $properties['UserDataClass'] = $this->uReplace;
                        $snippetObj->setProperties($properties);
                        $propertyCount++;
                        $dirty = true;
                    }
                }
                if (isset($properties['ResourceDataClass'])) {
                    if ($properties['ResourceDataClass'] === $this->rSearch) {
                        $properties['ResourceDataClass'] = $this->rReplace;
                        $snippetObj->setProperties($properties);
                        $dirty = true;
                        $propertyCount++;
                    }
                }
                if ($dirty) {
                    $snippetObj->save();
                }

                /* Check property sets, if any */
                $intersects = $snippetObj->getMany('PropertySets');

                if (!empty($propertySets)) {
                    foreach ($intersects as $intersect) {
                        $dirty = false;
                        $propsetId = $intersect->get('property_set');
                        $actualPropertySet = $this->modx->getObject($this->prefix . 'modPropertySet', $propsetId);

                        $props = $actualPropertySet->get('properties');

                        if (isset($props['UserDataClass'])) {
                            if ($props['UserDataClass']['value'] === $this->uSearch) {
                                $fixedPropertySetCount++;
                                $props['UserDataClass']['value'] = $this->uReplace;
                                $actualPropertySet->set('properties', $props);
                                $dirty = true;
                                $propertySetCount++;
                            }
                        }
                        if (isset($props['ResourceDataClass'])) {

                            if ($props['ResourceDataClass']['value'] === $this->rSearch) {
                                $props['ResourceDataClass']['value'] = $this->rReplace;
                                $actualPropertySet->set('properties', $props);
                                $dirty = true;
                                $propertySetCount++;
                            }
                        }
                        if ($dirty) {
                            $actualPropertySet->save();
                        }
                    }
                }
            }
        }
        if ($snippetCount) {
            $this->output($snippetCount . ' Class names updated in snippets');
        }
        if ($propertyCount) {
            $this->output($propertyCount . ' Class names updated in snippet properties');
        }
        if ($propertySetCount) {
            $this->output($propertySetCount . ' Class names updated in property sets');
        }
    }

    /**
     * @param array $terms - array of search terms
     * @param string $content - string to search
     * @return bool - true only if content contains any $terms
     */
    protected function checkContent(array $terms, string $content)  {

        foreach ($terms as $term) {
            if (strpos($content, $term) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $msg;
     */
    protected function output($msg) {
        if (php_sapi_name() === 'cli') {
            echo "\n" . $msg;
        } else {
            $this->modx->log(modX::LOG_LEVEL_INFO, $msg);
        }
    }
}
