<?php
namespace Craft;

class AmInstaller_InstallService extends BaseApplicationComponent
{
    public function installModule($moduleName)
    {
        $result = false;
        switch ($moduleName) {
            case 'algemeen':
                break;
            case 'diensten':
                break;
            case 'medewerkers':
                $result = $this->_installEmployees();
                break;
            case 'news':
                break;
            case 'producten':
                break;
            case 'referenties':
                break;
            case 'vacatures':
                break;
        }
        if ($result) {
            $result = $this->_addModuleToDatabase($moduleName);
        }
        return $result;
    }

    /**
     * Add module as installed in the database.
     *
     * @param string $moduleName
     *
     * @return bool
     */
    private function _addModuleToDatabase($moduleName)
    {
        // Check if a record of the module already exists within the database
        $installerRecord = AmInstallerRecord::model()->findByAttributes(array(
            'handle' => $moduleName
        ));
        if($installerRecord) {
            $attributes = array(
                'installed' => true
            );
            $installerRecord->setAttributes($attributes, false);
            return $installerRecord->save();
        } else {
            $installerRecord = new AmInstallerRecord;
            $attributes = array(
                'handle' => $moduleName,
                'installed' => true
            );
            $installerRecord->setAttributes($attributes, false);
            return $installerRecord->save();
        }
    }

    private function _installEmployees()
    {
        return true;
    }
}