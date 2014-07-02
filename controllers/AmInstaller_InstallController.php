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
        $redirectUrlOnError = craft()->request->getPost('redirectOnError', false);
        // Return result
        if ($moduleName) {
            $result = craft()->amInstaller_install->installModule($moduleName);
            if ($result) {
                craft()->userSession->setNotice('De module is succesvol geïnstalleerd.');
                $this->redirectToPostedUrl();
            }
        }
        if (($returnMessage = craft()->amInstaller_install->returnMessage) !== '') {
            craft()->userSession->setError($returnMessage);
        } else {
            craft()->userSession->setError('De module kon niet geïnstalleerd worden.');
        }
        // Redirect to correct URL
        if ($redirectUrlOnError) {
            $this->redirect($redirectUrlOnError);
        } else {
            $this->redirectToPostedUrl();
        }
    }
}