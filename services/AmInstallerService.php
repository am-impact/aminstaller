<?php
namespace Craft;

class AmInstallerService extends BaseApplicationComponent
{
    private $installedModules = array();

    /**
     * Get the information of a module.
     *
     * @param string $moduleName
     * @param bool   $getInstallInformation [Optional] Only get the install information of a module.
     *
     * @return array
     */
    public function getModule($moduleName, $getInstallInformation = false)
    {
        // Retrieve the requested module only
        $moduleData = $getInstallInformation ? array() : $this->_getAvailableModules($moduleName);
        // Add the additional installation information
        $this->_getModuleInstallInformation($moduleName, $moduleData);
        return $moduleData;
    }

    /**
     * Get the information of all modules.
     *
     * @return array
     */
    public function getModules()
    {
        return $this->_getAvailableModules();
    }

    /**
     * Get installed modules.
     */
    private function _setInstalledModules()
    {
        $allInstalledModules = AmInstallerRecord::model()->findAll();
        if ($allInstalledModules) {
            foreach ($allInstalledModules as $key => $module) {
                $attributes = $module->getAttributes();
                $this->installedModules[ $attributes['handle'] ] = $attributes['installed'] == '1';
            }
        }
    }

    /**
     * Check whether a module is installed.
     *
     * @param string $moduleName
     *
     * @return bool
     */
    private function _isModuleInstalled($moduleName)
    {
        return isset($this->installedModules[$moduleName]) ? $this->installedModules[$moduleName] : false;
    }

    /**
     * Get all available modules.
     *
     * @param string $getModuleByName Get the module information of a specific module.
     *
     * @return array
     */
    private function _getAvailableModules($getModuleByName = '')
    {
        // Find installed modules
        $this->_setInstalledModules();
        // Find available modules
        $availableModules = array();
        $dir = craft()->path->getPluginsPath() . 'aminstaller/resources/install/information/';
        $handle = opendir($dir);
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $moduleFile = $dir . $file . '/module.php';
            if (file_exists($moduleFile)) {
                $moduleContent = include($moduleFile);
                $moduleContent['installed'] = $this->_isModuleInstalled($file);
                $availableModules[$file] = $moduleContent;
            }
        }
        closedir($handle);
        // Return all or a specific module
        if (! empty($getModuleByName)) {
            return isset($availableModules[$getModuleByName]) ? $availableModules[$getModuleByName] : false;
        }
        return $availableModules;
    }

    /**
     * Add additional information for the module installation page.
     *
     * @param string $moduleName  The module name.
     * @param array  &$moduleData The module data.
     */
    private function _getModuleInstallInformation($moduleName, &$moduleData)
    {
        $files = array(
            'tabs',
            'main',
            'sections',
            'fields',
            'fieldLayout',
            'templateGroup',
            'entries'
        );
        foreach ($files as $file) {
            $fileLocation = craft()->path->getPluginsPath() . 'aminstaller/resources/install/information/' . $moduleName . '/' . $file . '.php';
            if (file_exists($fileLocation)) {
                $fileContent = include($fileLocation);
                $moduleData[$file] = $fileContent;
            }
        }
    }
}