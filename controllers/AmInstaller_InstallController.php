<?php
namespace Craft;

class AmInstaller_InstallController extends BaseController
{
    /**
     * Install a module.
     *
     * @return void
     */
    public function actionInstallModule()
    {
        // Only perform this action if it's a POST
        $this->requirePostRequest();
        // Retrieve POST data
        $moduleName = craft()->request->getPost('module', false);
        // Return result
        if ($moduleName) {
            $result = craft()->amInstaller_install->installModule($moduleName);
            if ($result) {
                craft()->userSession->setNotice('De module is succesvol geïnstalleerd.');
                $this->redirectToPostedUrl();
            }
        }
        craft()->userSession->setError('De module kon niet geïnstalleerd worden.');
        $this->redirectToPostedUrl();
    }
}