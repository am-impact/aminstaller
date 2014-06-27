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
     * Get a module.
     *
     * @param string $moduleName
     *
     * @return bool|string
     */
    public function getModuleInformation($moduleName)
    {
        return craft()->amInstaller->getModule($moduleName);
    }

    /**
     * Get available modules.
     *
     * @return array
     */
    public function getModules()
    {
        return craft()->amInstaller->getModules();
    }
}