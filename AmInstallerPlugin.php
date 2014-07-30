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
        return '0.2';
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

    /**
     * Redirect to plugins page if the user's license isn't good enough.
     */
    public function onBeforeInstall()
    {
        if (craft()->getEdition() == Craft::Personal) {
            craft()->userSession->setNotice(Craft::t('You can\'t use “{plugin}” with the Craft personal license.', array('plugin' => $this->getName())));
            craft()->request->redirect('plugins');
        }
    }

    /**
     * Redirect to plugin after install.
     */
    public function onAfterInstall()
    {
        craft()->userSession->setNotice(Craft::t('Plugin installed.'));
        craft()->request->redirect('../aminstaller');
    }

    /**
     * Drop plugin tables
     */
    public function dropTables()
    {
        $installerRecord = new AmInstallerRecord();
        $installerRecord->dropTable();
    }
}