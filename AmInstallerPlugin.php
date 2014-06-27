<?php
/**
 * Easy module installer.
 *
 * @package   Am Installer
 * @author    Hubert Prein
 */
namespace Craft;

class AmInstallerPlugin extends BasePlugin
{
    public function getName()
    {
         return 'a&m impact installer';
    }

    public function getVersion()
    {
        return '0.1';
    }

    public function getDeveloper()
    {
        return 'a&m impact';
    }

    public function getDeveloperUrl()
    {
        return 'http://www.am-impact.nl';
    }

    /**
     * Plugin has control panel section.
     *
     * @return boolean
     */
    public function hasCpSection()
    {
        return true;
    }

    /**
     * Register control panel urls.
     *
     * @return array
     */
    public function registerCpRoutes()
    {
        return array(
            'aminstaller/install/(?P<moduleName>.*+)' => 'aminstaller/_install'
        );
    }
}