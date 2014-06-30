<?php
namespace Craft;

class AmInstaller_InstallService extends BaseApplicationComponent
{
    private $currentSections;
    private $currentFieldGroups;
    private $currentFields;

    /**
     * Install a module.
     *
     * @param string $moduleName
     *
     * @return bool Installation result.
     */
    public function installModule($moduleName)
    {
        // Get module install information
        $moduleInformation = craft()->amInstaller->getModule($moduleName, true);
        // Get current sections and field groups
        $this->_setCurrentSections();
        $this->_setCurrentFieldGroups();
        // Install the module
        $result = false;
        switch ($moduleName) {
            case 'algemeen':
                break;
            case 'diensten':
                break;
            case 'medewerkers':
                $result = $this->_installEmployees($moduleInformation);
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
        // Add the module to the database, as installed
        if ($result) {
            $result = $this->_addModuleToDatabase($moduleName);
        }
        return $result;
    }

    /**
     * Camel case a string.
     *
     * @param string $string
     *
     * @return string
     */
    private function _camelCase($string)
    {
        return str_replace(' ', '', ucwords(strtr($string, '_-', '  ')));
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

    /**
     * Get current sections.
     */
    private function _setCurrentSections()
    {
        $sections = craft()->sections->getAllSections();
        foreach ($sections as $section) {
            $this->currentSections[$section->id] = $section->name;
        }
    }

    /**
     * Check whether the given sections are able to be created.
     *
     * @param type $sections
     *
     * @return mixed Array with available section names and their url formats, false on error.
     */
    private function _checkSectionAvailability($sections)
    {
        if (count($sections)) {
            $available = array();
            foreach ($sections as $sectionKey => $sectionValues) {
                $sectionName = craft()->request->getPost($sectionKey, false);
                $sectionUrlFormat = craft()->request->getPost($sectionKey . 'UrlFormat', false);
                if (! $sectionName || ! $sectionUrlFormat || in_array($sectionName, $this->currentSections)) {
                    return false;
                }
                $available[$sectionKey] = array(
                    'name' => $sectionName,
                    'urlFormat' => $sectionUrlFormat
                );
            }
            return $available;
        }
        return false;
    }

    /**
     * Create a new section.
     *
     * @param string   $name      Section name.
     * @param constant $type      Section type (e.g.:SectionType::Single)
     * @param bool     $hasUrls   Whether the section has it's own URLs.
     * @param string   $template
     * @param string   $locale
     * @param string   $urlFormat
     *
     * @return bool Creation result.
     */
    private function _createSection($name, $type, $hasUrls, $template, $locale, $urlFormat)
    {
        $vars = array(
            'sectionName' => $name
        );
        AmInstaller::log(Craft::t('Creating the `{sectionName}` section.', $vars));

        $section = new SectionModel();
        $section->type     = $type;
        $section->name     = $name;
        $section->handle   = $this->_camelCase($name);
        $section->hasUrls  = $hasUrls;
        $section->template = 'news/_entry';

        $section->setLocales(array(
            $inputs['locale'] => SectionLocaleModel::populateModel(array(
                'locale'    => $locale,
                'urlFormat' => $urlFormat,
            ))
        ));

        if (craft()->sections->saveSection($section)) {
            AmInstaller::log(Craft::t('Section `{sectionName}` created successfully.', $vars));
        } else {
            AmInstaller::log(Craft::t('Could not save the `{sectionName}` section.', $vars), LogLevel::Warning);
        }
    }

    /**
     * Get current field groups.
     */
    private function _setCurrentFieldGroups()
    {
        $fieldGroups = craft()->fields->getAllGroups();
        foreach ($fieldGroups as $fieldGroup) {
            $this->currentFieldGroups[$fieldGroup->id] = $fieldGroup->name;
        }
    }

    /**
     * Get a field group ID.
     *
     * @param string $name Field group name.
     *
     * @return int
     */
    private function _getFieldGroupId($name)
    {
        $fieldGroupId = array_search($name, $this->currentFieldGroups);
        if (! $fieldGroupId) {
            $group = new FieldGroupModel();
            $group->name = $name;
            $vars = array(
                'groupName' => $name
            );
            if (craft()->fields->saveGroup($group)) {
                $this->currentFieldGroups[$group->id] = $group->name; // Add to current field groups
                AmInstaller::log(Craft::t('Field group `{groupName}` created successfully.', $vars));
            } else {
                AmInstaller::log(Craft::t('Could not save the `{groupName}` field group.', $vars), LogLevel::Warning);
            }
            return $group->id;
        }
        return $fieldGroupId;
    }

    /**
     * Get current fields.
     */
    private function _setCurrentFields()
    {
        $fields = craft()->fields->getAllFields();
    }

    /**
     * Module installation: Employees.
     *
     * @return bool Installation result.
     */
    private function _installEmployees($moduleInformation)
    {
        // Check section availability
        $sectionResult = $this->_checkSectionAvailability($moduleInformation['sections']);
        if (! $sectionResult) {
            AmInstaller::log('One of the sections that was about to be installed, already exists.');
            return false;
        }
        // Gather install information
        $primaryLocaleId = craft()->i18n->getPrimarySiteLocaleId();
        $templateGroupName = $this->_camelCase($sectionResult['entrySection']['name']);
        // Install overview section
        $this->_createSection($sectionResult['overviewSection']['name'], SectionType::Single, false, $templateGroupName, $primaryLocaleId, $sectionResult['overviewSection']['urlFormat']);
        // Install entry section
        $this->_createSection($sectionResult['entrySection']['name'], SectionType::Channel, true, $templateGroupName . '/_entry', $primaryLocaleId, $sectionResult['entrySection']['urlFormat']);
        return true;
    }
}