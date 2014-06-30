<?php
namespace Craft;

class AmInstallerVariable
{
    /**
     * Get the Plugin's name.
     *
     * @example {{ craft.amInstaller.name }}
     * @return string
     */
    public function getName()
    {
        $plugin = craft()->plugins->getPlugin('aminstaller');
        return $plugin->getName();
    }

    /**
     * Get the information of a module.
     *
     * @param string $moduleName
     *
     * @return mixed Array with module information, false if module doesn't exist.
     */
    public function getModuleInformation($moduleName)
    {
        return craft()->amInstaller->getModule($moduleName);
    }

    /**
     * Get available modules information.
     *
     * @return array
     */
    public function getModules()
    {
        return craft()->amInstaller->getModules();
    }
}