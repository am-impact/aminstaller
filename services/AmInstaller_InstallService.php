<?php
namespace Craft;

class AmInstaller_InstallService extends BaseApplicationComponent
{
    public $returnMessage = '';
    private $currentSections = array();
    private $currentFieldGroups = array();
    private $currentFields = array();
    private $currentFieldsTypes = array();
    private $currentMatrixBlockTypes = array();
    private $currentGlobalSets = array();

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
        $module = AmInstaller_ModuleModel::populateModel($moduleInformation);
        // Get current craft data
        $this->_setCurrentSections();
        $this->_setCurrentFieldGroups();
        $this->_setCurrentFields();
        $this->_setCurrentGlobalSets();
        // Install the module
        $result = $this->_installModule($moduleName, $module);
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
        $string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);
        return str_replace(' ', '', lcfirst(ucwords(strtolower(strtr($string, '_-', '  ')))));
    }

    /**
     * Convert a string to an URI string.
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
        AmInstallerPlugin::log($message);
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
        if ($installerRecord) {
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
     * Get current global sets.
     */
    private function _setCurrentGlobalSets()
    {
        $globalSets = craft()->globals->getAllSets();
        foreach ($globalSets as $globalSet) {
            $this->currentGlobalSets[ $globalSet->id . '-name' ] = $globalSet->name;
            $this->currentGlobalSets[ $globalSet->id . '-handle' ] = $globalSet->handle;
        }
    }

    /**
     * Get a global set.
     *
     * @param string $name Global set name.
     *
     * @return GlobalSetModel
     */
    private function _getGlobalSet($name)
    {
        $globalSetId = array_search($name, $this->currentGlobalSets);
        if (! $globalSetId) {
            $vars = array(
                'setName' => $name
            );
            $globalSet = new GlobalSetModel();
            $globalSet->name   = $name;
            $globalSet->handle = $this->_camelString($name);
            if (craft()->globals->saveSet($globalSet)) {
                $this->currentGlobalSets[$globalSet->id] = $globalSet->name; // Add to current global sets
                $this->currentGlobalSets[$globalSet->id] = $globalSet->handle; // Add to current global sets
                AmInstallerPlugin::log(Craft::t('Global set `{setName}` created successfully.', $vars));
            } else {
                AmInstallerPlugin::log(Craft::t('Could not save the `{setName}` global set.', $vars), LogLevel::Warning);
            }
            return $globalSet;
        } else {
            $globalSetId = explode('-');
            $globalSetId = (int)$globalSetId[0];
        }
        return craft()->globals->getSetById($globalSetId);
    }

    /**
     * Create all fields from a module.
     *
     * @param array $fields
     * @param array $editData     [Optional] Includes additional information for a field where needed.
     * @param bool  $returnFields [Optional] Return the information of fields that have been created.
     */
    private function _createFields($fields, $editData = array(), $returnFields = false)
    {
        // Remember created fields?
        if ($returnFields) {
            $fieldsInformation = array();
        }
        // Process the field groups first
        foreach ($fields as $fieldGroupName => $fieldGroupFields) {
            // Get field group ID
            $fieldGroupId = $this->_getFieldGroupId($fieldGroupName);
            // Process each field inside a field group
            foreach ($fieldGroupFields as $field) {
                // Edit field before we continue?
                if (isset($field['for']) && isset($editData[ $field['for'] ])) {
                    // Remember old name
                    if ($returnFields) {
                        $nameBeforeEdit = $field['name'];
                    }
                    // Get data from installed section
                    $foundEditData = $editData[ $field['for'] ];
                    // Translations
                    $vars = array(
                        'sectionId'   => $foundEditData->id,
                        'sectionName' => $foundEditData->name
                    );
                    $field['name'] = Craft::t($field['name'], $vars);
                    // Add fieldName to translations
                    $vars['fieldName'] = $field['name'];
                    // Edit the field handle
                    $field['handle'] = $this->_camelString(Craft::t($field['handle'], $vars));
                    // Edit the field instructions
                    if (isset($field['instructions'])) {
                        $field['instructions'] = Craft::t($field['instructions'], $vars);
                    }
                    // Edit the field value
                    if (isset($field['value'])) {
                        $field['value'] = Craft::t($field['value'], $vars);
                    }
                }

                // Translations
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
                $newField->translatable = isset($field['translatable']) ? $field['translatable'] : true;
                $newField->type         = $field['type'];
                if (isset($field['instructions'])) {
                    $newField->instructions = $field['instructions'];
                }
                if (isset($field['settings'])) {
                    // Check whether it's an Entries field type, which is connected to a section that might not exist
                    if ($newField->type == 'Entries' && isset($field['settings']['section'])) {
                        $sectionId = array_search($field['settings']['section'], $this->currentSections);
                        if ($sectionId) {
                            $sectionId = explode('-', $sectionId);
                            $sectionId = $sectionId[0];
                            $field['settings']['sources'] = array('section:' . $sectionId);
                        }
                    }
                    $newField->settings = $field['settings'];
                }
                if (craft()->fields->saveField($newField)) {
                    $this->currentFields[$newField->id] = $newField->handle; // Add to current fields
                    $this->currentFieldsTypes[$newField->handle] = $newField->type; // Add to current fields types
                    // Add to fields information for returning
                    if ($returnFields) {
                        $fieldsInformation[$nameBeforeEdit] = array(
                            'handle' => $newField->handle
                        );
                        if (isset($field['value'])) {
                            $fieldsInformation[$nameBeforeEdit]['value'] = $field['value'];
                        }
                    }
                    // Add Matrix Fields
                    if ($newField->type == 'Matrix' && isset($field['settings']['blockTypes'])) {
                        foreach ($field['settings']['blockTypes'] as $blockType) {
                            foreach ($blockType['fields'] as $field) {
                                $this->currentFields[ 'MatrixBlock-' . $newField->id ] = $field['handle']; // Add to current fields
                                $this->currentFieldsTypes[ 'MatrixBlock-' . $newField->handle ] = $field['type']; // Add to current fields types
                            }
                        }
                    }
                    AmInstallerPlugin::log(Craft::t('Field `{fieldName}` created successfully.', $vars));
                } else {
                    AmInstallerPlugin::log(Craft::t('Could not save the `{fieldName}` field.', $vars), LogLevel::Warning);
                }
            }
        }
        // Return information of created fields
        if ($returnFields) {
            return $fieldsInformation;
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
                    $fieldId   = array_search($field['name'], $this->currentFields);
                    $fieldName = $field['name'];
                    $content   = $this->_getEntryContentForField($newEntry, $fieldId, $fieldName, $field['testContent']);
                    if ($content) {
                        $attributes[$fieldName] = $content;
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
     * Get the content for an Entry Field.
     *
     * @param object $entry
     * @param int    $fieldId
     * @param string $fieldName
     * @param mixed  $testContent
     *
     * @return mixed Content or false on failure / unknown field.
     */
    private function _getEntryContentForField($entry, $fieldId, $fieldName, $testContent)
    {
        if (! isset($this->currentFieldsTypes[$fieldName])) {
            return false;
        }
        switch ($this->currentFieldsTypes[$fieldName]) {
            case 'Assets':
                return $testContent;
                break;
            case 'Matrix':
                $blocks = array();
                foreach ($testContent as $blockModule) {
                    // Get the Matrix Block Type ID based on current field and type from fieldLayout
                    if (($typeId = $this->_getMatrixBlockTypeId($fieldId, $blockModule['type'])) !== false) {
                        // Create Matrix Block
                        $matrixBlock = new MatrixBlockModel();
                        $matrixBlock->fieldId = $fieldId;
                        $matrixBlock->typeId  = $typeId;
                        $matrixBlock->ownerId = $entry->id;
                        $matrixBlock->locale  = $entry->locale;
                        // Create content for Matrix Block
                        $blockContent = array();
                        foreach ($blockModule['fields'] as $fieldHandle => $fieldValue) {
                            // Get the field content based on the field's type
                            $fieldContent = $this->_getEntryContentForField($entry, $fieldId, 'MatrixBlock-' . $fieldName, $fieldValue);
                            if ($fieldContent) {
                                $blockContent[$fieldHandle] = $fieldContent;
                            }
                        }
                        $matrixBlock->setContent($blockContent);
                        // Add block to Matrix Field
                        $blocks[] = $matrixBlock;
                    }
                }
                if (count($blocks)) {
                    return $blocks;
                }
                break;
            default:
                // Multiple test content
                if (is_array($testContent)) {
                    $testContent = $testContent[ rand(0, (count($testContent) - 1)) ];
                }
                return $testContent;
                break;
        }
    }

    /**
     * Create all globals from a module.
     *
     * @param array $globals
     * @param array $installedSections The installed sections from a module, with the SectionModels as array values.
     */
    private function _createGlobals($globals, $installedSections)
    {
        // Create GlobalSet as Field Group set and create the fields
        $createdFields = $this->_createFields($globals, $installedSections, true);
        // Process the global sets first
        foreach ($globals as $setName => $setFields) {
            // Get GlobalSet
            $globalSet = $this->_getGlobalSet($setName);
            // Process each field inside a GlobalSet
            $tabs = array();
            $required = array();
            $content = array();
            foreach ($setFields as $field) {
                // Add each field to the layout
                $createdField = $createdFields[ $field['name'] ];
                $fieldId = array_search($createdField['handle'], $this->currentFields);
                if ($fieldId) {
                    // Add field to GlobalSet tab
                    $tabs[0][] = $fieldId;
                    // Is the field required?
                    if (isset($field['required']) && $field['required']) {
                        $required[] = $fieldId;
                    }
                    // Add content
                    if (isset($createdField['value'])) {
                        $content[ $createdField['handle'] ] = $createdField['value'];
                    }
                }
            }
            // Set the field layout
            $fieldLayout = craft()->fields->assembleLayout($tabs, $required, false);
            $fieldLayout->type = ElementType::GlobalSet;
            $globalSet->setFieldLayout($fieldLayout);
            // Store GlobalSet
            craft()->globals->saveSet($globalSet);
            // Store content
            $globalSet->setContent($content);
            craft()->globals->saveContent($globalSet);
        }
    }

    /**
     * Create a template group folder, with templates from the module.
     *
     * @param string $moduleName
     * @param string $templateGroupName
     *
     * @return bool
     */
    private function _createTemplateGroup($moduleName, $templateGroupName)
    {
        $vars = array(
            'moduleName' => $moduleName
        );
        $moduleTemplatesDir = craft()->path->getPluginsPath() . 'aminstaller/resources/install/' . $moduleName . '/templates/';
        $newTemplateDir = craft()->path->getSiteTemplatesPath() . $templateGroupName;
        if (! is_dir($moduleTemplatesDir) || is_dir($newTemplateDir)) {
            AmInstallerPlugin::log(Craft::t('The module `{moduleName}` doesn\'t have any templates that could be copied.', $vars));
            return false;
        }
        try {
            $fileHelper = new \CFileHelper();
            @mkdir($newTemplateDir);
            $fileHelper->copyDirectory($moduleTemplatesDir, $newTemplateDir);
        } catch (\Exception $e) {
            AmInstaller::log($e->getMessage(), LogLevel::Warning);
        }
        AmInstallerPlugin::log(Craft::t('The templates for module `{moduleName}` have been added.', $vars));
        return true;
    }

    /**
     * Install module.
     *
     * @param string      $moduleName
     * @param ModuleModel $moduleInformation
     *
     * @return bool Installation result.
     */
    private function _installModule($moduleName, $moduleInformation)
    {
        // Install fields
        if (! is_null($moduleInformation->fields)) {
            $this->_createFields($moduleInformation->fields);
        }
        // Install sections & templates
        $installedSections = array(); // Variable is used by the globals installation as well
        if (! is_null($moduleInformation->sections)) {
            // The template group is required
            if (is_null($moduleInformation->templateGroup)) {
                $this->_setReturnMessage(Craft::t('If you wish to install sections, a `templateGroup` file is required.'));
                return false;
            }
            // Check section availability
            $sectionResult = $this->_checkSectionAvailability($moduleInformation->sections);
            if (! $sectionResult) {
                $this->_setReturnMessage(Craft::t('One of the sections that was about to be installed, already exists.'));
                return false;
            }
            // Gather install information
            $templateGroupName = craft()->request->getPost('templateGroup', false);
            if (! $templateGroupName || empty($templateGroupName)) {
                $this->_setReturnMessage(Craft::t('The template group field doesn\'t contain correct data.'));
                return false;
            }
            $primaryLocaleId = craft()->i18n->getPrimarySiteLocaleId();
            $templateGroupName = $this->_uriString($templateGroupName);

            // Install sections
            foreach ($sectionResult as $sectionKey => $sectionValues) {
                // Add section
                $hasUrls = ($sectionValues['type'] != SectionType::Single);
                $addToTemplateGroupName = ($sectionValues['type'] != SectionType::Single) ? '/_entry' : '';
                $createdSection = $this->_createSection($sectionValues['name'], $sectionValues['type'], $hasUrls, $templateGroupName . $addToTemplateGroupName, $primaryLocaleId, $sectionValues['urlFormat']);
                $installedSections[$sectionKey] = $createdSection;

                // Add field layout if available, otherwise an empty layout
                $fieldLayout = array();
                if (! is_null($moduleInformation->fieldLayout) && isset($moduleInformation->fieldLayout[$sectionKey])) {
                    $fieldLayout = $moduleInformation->fieldLayout[$sectionKey];
                }
                $createdLayout = $this->_createFieldLayout($fieldLayout);

                // Add entry type
                $createdEntryType = $this->_createEntryType($createdSection, $createdLayout);

                // Add entries
                if (! is_null($moduleInformation->entries) && isset($moduleInformation->entries[$sectionKey]) && count($fieldLayout)) {
                    $installTestEntries = craft()->request->getPost($sectionKey . 'installTestEntries', '0') == '1';
                    if ($installTestEntries) {
                        $totalTestEntries = (int)craft()->request->getPost($sectionKey . 'totalTestEntries', 1);
                        $this->_createEntries($totalTestEntries, $primaryLocaleId, $createdSection, $fieldLayout, $createdEntryType);
                    }
                }
            }

            // Install templates
            $this->_createTemplateGroup($moduleName, $templateGroupName);
        }

        // Install globals
        if (! is_null($moduleInformation->globals)) {
            $this->_createGlobals($moduleInformation->globals, $installedSections);
        }
        return true;
    }
}