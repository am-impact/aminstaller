<?php
namespace Craft;

class AmInstaller_InstallService extends BaseApplicationComponent
{
    public $returnMessage = '';
    private $currentSections;
    private $currentFieldGroups;
    private $currentFields;
    private $currentFieldsTypes;
    private $currentMatrixBlockTypes;

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
        $this->_setCurrentFields();
        // Install the module
        $result = $this->_installModule($moduleInformation);
        // Add the module to the database, as installed
        if ($result) {
            $result = $this->_addModuleToDatabase($moduleName);
        }
        return $result;
    }

    /**
     * Convert a string to a camel cased string.
     *
     * @param string $string
     *
     * @return string
     */
    private function _camelString($string)
    {
        return str_replace(' ', '', lcfirst(ucwords(strtr($string, '_-', '  '))));
    }

    /**
     * Conver a string to an URI string.
     *
     * @param string $string
     *
     * @return string
     */
    private function _uriString($string)
    {
        return str_replace(' ', '-', strtolower($string));
    }

    /**
     * Set a message that'll be shown to the user upon page load.
     *
     * @param string $message
     */
    private function _setReturnMessage($message)
    {
        $this->returnMessage = $message;
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
            $this->currentSections[ $section->id . '-name' ]      = $section->name;
            $this->currentSections[ $section->id . '-handle' ]    = $section->handle;
            $this->currentSections[ $section->id . '-urlFormat' ] = $section->urlFormat;
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
            $sectionTypes = array(
                'single'    => SectionType::Single,
                'channel'   => SectionType::Channel,
                'structure' => SectionType::Structure
            );
            $available = array();
            foreach ($sections as $sectionKey => $sectionValues) {
                $sectionName = craft()->request->getPost($sectionKey, false);
                $sectionUrlFormat = craft()->request->getPost($sectionKey . 'UrlFormat', false);
                if (! $sectionName || ! $sectionUrlFormat) {
                    return false;
                }
                elseif (in_array($sectionName, $this->currentSections) || in_array($this->_camelString($sectionName), $this->currentSections) || in_array($sectionUrlFormat, $this->currentSections)) {
                    return false;
                }
                $available[$sectionKey] = array(
                    'name'      => $sectionName,
                    'type'      => $sectionTypes[ strtolower($sectionValues['type']) ],
                    'urlFormat' => $sectionUrlFormat
                );
            }
            return $available;
        }
        return false;
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
                AmInstallerPlugin::log(Craft::t('Field group `{groupName}` created successfully.', $vars));
            } else {
                AmInstallerPlugin::log(Craft::t('Could not save the `{groupName}` field group.', $vars), LogLevel::Warning);
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
        foreach ($fields as $field) {
            $this->currentFields[$field->id] = $field->handle;
            $this->currentFieldsTypes[$field->handle] = $field->type;
        }
    }

    /**
     * Get current Matrix Block Types.
     *
     * @param int    $fieldId
     * @param string $typeHandle
     */
    private function _getMatrixBlockTypeId($fieldId, $typeHandle)
    {
        if (! isset($this->currentMatrixBlockTypes[$fieldId])) {
            $results = craft()->db->createCommand()
                ->select('id, fieldId, fieldLayoutId, name, handle, sortOrder')
                ->from('matrixblocktypes')
                ->where('fieldId = :fieldId', array(':fieldId' => $fieldId))
                ->order('sortOrder')
                ->queryAll();
            foreach ($results as $result) {
                $this->currentMatrixBlockTypes[$fieldId][ $result['id'] ] = $result['handle'];
            }
        }
        if (isset($this->currentMatrixBlockTypes[$fieldId])) {
            foreach ($this->currentMatrixBlockTypes as $fieldId => $types) {
                $typeId = array_search($typeHandle, $types);
                if ($typeId) {
                    return $typeId;
                }
            }
        }
        return false;
    }

    /**
     * Create all fields from a module.
     *
     * @param array $fields
     */
    private function _createFields($fields)
    {
        // Process the field groups first
        foreach ($fields as $fieldGroupName => $fieldGroupFields) {
            // Get field group ID
            $fieldGroupId = $this->_getFieldGroupId($fieldGroupName);
            // Process each field inside a field group
            foreach ($fieldGroupFields as $field) {
                $vars = array(
                    'fieldName' => $field['name']
                );

                // Check whether the field already exists
                if (in_array($field['handle'], $this->currentFields)) {
                    AmInstallerPlugin::log(Craft::t('Skip creating the `{fieldName}` field, because it already exists.', $vars));
                    continue;
                }

                // Create a new field
                AmInstallerPlugin::log(Craft::t('Creating the `{fieldName}` field.', $vars));

                $newField = new FieldModel();
                $newField->groupId      = $fieldGroupId;
                $newField->name         = $field['name'];
                $newField->handle       = $field['handle'];
                $newField->translatable = true;
                $newField->type         = $field['type'];
                if (isset($field['settings'])) {
                    $newField->settings = $field['settings'];
                }

                if (craft()->fields->saveField($newField)) {
                    $this->currentFields[$newField->id] = $newField->handle; // Add to current fields
                    $this->currentFieldsTypes[$newField->handle] = $newField->type; // Add to current fields types
                    AmInstallerPlugin::log(Craft::t('Field `{fieldName}` created successfully.', $vars));
                } else {
                    AmInstallerPlugin::log(Craft::t('Could not save the `{fieldName}` field.', $vars), LogLevel::Warning);
                }
            }
        }
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
     * @return Section model
     */
    private function _createSection($name, $type, $hasUrls, $template, $locale, $urlFormat)
    {
        $vars = array(
            'sectionName' => $name
        );
        AmInstallerPlugin::log(Craft::t('Creating the `{sectionName}` section.', $vars));

        $newSection = new SectionModel();
        $newSection->type     = $type;
        $newSection->name     = $name;
        $newSection->handle   = $this->_camelString($name);
        $newSection->hasUrls  = $hasUrls;
        $newSection->template = $this->_uriString($template);

        $newSection->setLocales(array(
            $locale => SectionLocaleModel::populateModel(array(
                'locale'    => $locale,
                'urlFormat' => $this->_uriString($urlFormat)
            ))
        ));

        if (craft()->sections->saveSection($newSection)) {
            AmInstallerPlugin::log(Craft::t('Section `{sectionName}` created successfully.', $vars));
        } else {
            AmInstallerPlugin::log(Craft::t('Could not save the `{sectionName}` section.', $vars), LogLevel::Warning);
        }
        return $newSection;
    }

    /**
     * Create an entry type.
     *
     * @param object $section     The section model.
     * @param object $fieldLayout The fieldLayout model.
     */
    private function _createEntryType($section, $fieldLayout)
    {
        $vars = array(
            'sectionName' => $section->name
        );
        AmInstallerPlugin::log(Craft::t('Creating the Entry Type for the `{sectionName}` section.', $vars));

        $sectionEntryTypes = $section->getEntryTypes();
        $sectionEntryType = $sectionEntryTypes[0];
        $sectionEntryType->setFieldLayout($fieldLayout);

        if (craft()->sections->saveEntryType($sectionEntryType)) {
            AmInstallerPlugin::log(Craft::t('Entry type for the `{sectionName}` section saved successfully.', $vars));
        } else {
            AmInstallerPlugin::log(Craft::t('Could not save the entry type for the `{sectionName}` section.', $vars), LogLevel::Warning);
        }
        return $sectionEntryType;
    }

    /**
     * Create a field layout for an entry type.
     *
     * @param array $layout
     *
     * @return Field Layout
     */
    private function _createFieldLayout($layout)
    {
        // Arrays that we will parse to assembleLayout function
        $tabs = array();
        $required = array();
        // Get the fields
        foreach ($layout as $tabName => $fields) {
            // Create a new set for each tab
            $tabs[$tabName] = array();
            foreach ($fields as $field) {
                $fieldId = array_search($field['name'], $this->currentFields);
                if ($fieldId) {
                    // Add field to current tab
                    $tabs[$tabName][] = $fieldId;
                    // Is the field required?
                    if ($field['required']) {
                        $required[] = $fieldId;
                    }
                }
            }
        }
        // Create layout
        $sectionLayout = craft()->fields->assembleLayout($tabs, $required, true);
        $sectionLayout->type = ElementType::Entry;
        return $sectionLayout;
    }

    private function _createEntries($amount, $locale, $section, $fieldLayout, $entryType)
    {
        $vars = array(
            'sectionName' => $section->name
        );
        // Get the current user information
        $user = craft()->userSession->getUser();
        for ($i = 1; $i <= $amount; $i++) {
            // Ready entry model
            $newEntry = new EntryModel();
            $newEntry->sectionId  = $section->id;
            $newEntry->typeId     = $entryType->id;
            $newEntry->locale     = $locale;
            $newEntry->authorId   = $user->id;
            $newEntry->enabled    = true;
            // Set entry title
            $newEntry->getContent()->title = $section->name . ' ' . $i; // E.g.: Medewerker 1
            // Set entry fields
            $attributes = array();
            foreach ($fieldLayout as $fieldGroupName => $fieldGroupFields) {
                foreach ($fieldGroupFields as $field) {
                    // Is the field type known?
                    if (! isset($this->currentFieldsTypes[ $field['name'] ])) {
                        continue;
                    }
                    // Only parse the content if we have any at all
                    if (! isset($field['testContent'])) {
                        continue;
                    }
                    $fieldName = $field['name'];
                    $fieldId   = array_search($field['name'], $this->currentFields);
                    $content   = $field['testContent'];
                    // Process the field based on it's type
                    switch ($this->currentFieldsTypes[$fieldName]) {
                        case 'Assets':
                            $attributes[$fieldName] = $content;
                            break;
                        case 'Matrix':
                            foreach ($content as $blockModule) {
                                // Get the Matrix Block Type ID based on current field and type from fieldLayout
                                if(($typeId = $this->_getMatrixBlockTypeId($fieldId, $blockModule['type'])) !== false) {
                                    // Create Matrix Block
                                    $matrixBlock = new MatrixBlockModel();
                                    $matrixBlock->fieldId = $fieldId;
                                    $matrixBlock->typeId  = $typeId;
                                    $matrixBlock->ownerId = $newEntry->id;
                                    $matrixBlock->locale  = $locale;
                                    // Create content for Matrix Block
                                    $blockContent = array();
                                    foreach ($blockModule['fields'] as $fieldHandle => $fieldValue) {
                                        // TODO random test content kunnen plaatsen
                                        $blockContent[$fieldHandle] = $fieldValue;
                                    }
                                    $matrixBlock->setContent($blockContent);
                                    // Add block to Matrix Field
                                    $attributes[$fieldName][] = $matrixBlock;
                                }
                            }
                            break;
                        default:
                            // Multiple test content
                            if (is_array($content)) {
                                $content = $content[ rand(0, (count($content) - 1)) ];
                            }
                            $attributes[$fieldName] = $content;
                            break;
                    }
                }
            }
            $newEntry->getContent()->setAttributes($attributes);
            // Save entry
            if (craft()->entries->saveEntry($newEntry)) {
                AmInstallerPlugin::log(Craft::t('New entry for `{sectionName}` created successfully.', $vars));
            } else {
                AmInstallerPlugin::log(Craft::t('Could not save a new entry for the `{sectionName}` section.', $vars), LogLevel::Warning);
            }
        }
    }

    /**
     * Install module.
     *
     * @return bool Installation result.
     */
    private function _installModule($moduleInformation)
    {
        // Check section availability
        $sectionResult = $this->_checkSectionAvailability($moduleInformation['sections']);
        if (! $sectionResult) {
            AmInstallerPlugin::log('One of the sections that was about to be installed, already exists.');
            $this->_setReturnMessage('Eén van de aangegeven secties bestaat al.');
            return false;
        }
        // Gather install information
        $templateGroupName = craft()->request->getPost($moduleInformation['templateGroup']['name'], false);
        if (! $templateGroupName || empty($templateGroupName)) {
            AmInstallerPlugin::log('The template group field doesn\'t contain correct data.');
            $this->_setReturnMessage('De template groep voor de secties bevat geen goede data.');
            return false;
        }
        $primaryLocaleId = craft()->i18n->getPrimarySiteLocaleId();
        $templateGroupName = $this->_uriString($templateGroupName);
        // Install fields
        $this->_createFields($moduleInformation['fields']);
        // Install sections
        foreach ($sectionResult as $sectionKey => $sectionValues) {
            // Add section
            $hasUrls = ($sectionValues['type'] != SectionType::Single);
            $addToTemplateGroupName = ($sectionValues['type'] != SectionType::Single) ? '/_entry' : '';
            $createdSection = $this->_createSection($sectionValues['name'], $sectionValues['type'], $hasUrls, $templateGroupName . $addToTemplateGroupName, $primaryLocaleId, $sectionValues['urlFormat']);
            // Add field layout
            $fieldLayout = isset($moduleInformation['fieldLayout'][$sectionKey]) ? $moduleInformation['fieldLayout'][$sectionKey] : array();
            $createdLayout = $this->_createFieldLayout($fieldLayout);
            // Add entry type
            $createdEntryType = $this->_createEntryType($createdSection, $createdLayout);
            // Add entries
            if (isset($moduleInformation['entries'][$sectionKey]) && count($fieldLayout)) {
                $installTestEntries = craft()->request->getPost('installTestEntries', '0') == '1';
                if ($installTestEntries) {
                    $totalTestEntries = (int)craft()->request->getPost('totalTestEntries', 1);
                    $this->_createEntries($totalTestEntries, $primaryLocaleId, $createdSection, $fieldLayout, $createdEntryType);
                }
            }
        }
        return true;
    }
}