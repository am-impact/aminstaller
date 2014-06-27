<?php
namespace Craft;

class AmInstallerService extends BaseApplicationComponent
{
    /**
     * Get the information of a module.
     *
     * @param string $moduleName
     *
     * @return array
     */
    public function getModule($moduleName)
    {
        $moduleData = $this->_currentModules($moduleName);
        $this->_currentModuleInstallInformation($moduleName, $moduleData);
        return $moduleData;
    }

    /**
     * Get the information of all modules.
     *
     * @return array
     */
    public function getModules()
    {
        return $this->_currentModules();
    }

    /**
     * Get all available modules.
     *
     * @param string $getModuleByName Get the module information of a specific module.
     *
     * @return array
     */
    private function _currentModules($getModuleByName = '')
    {
        // Find installed modules
        $installedModules = array();
        $allInstalledModules = AmInstallerRecord::model()->findAll();
        if ($allInstalledModules) {
            foreach ($allInstalledModules as $key => $module) {
                $attributes = $module->getAttributes();
                $installedModules[ $attributes['handle'] ] = $attributes['installed'] == '1';
            }
        }
        // Set the information of every module
        $currentModules = array(
            'algemeen' => array(
                'name' => 'Algemeen',
                'description' => 'Veel gebruikte velden toevoegen en standaard pagina\'s zoals contact, zoekresultaat en dergelijke.',
                'installed' => isset($installedModules['algemeen']) ? $installedModules['algemeen'] : false
            ),
            'diensten' => array(
                'name' => 'Diensten',
                'description' => 'Dienst overzicht en diensten.',
                'installed' => isset($installedModules['diensten']) ? $installedModules['diensten'] : false
            ),
            'medewerkers' => array(
                'name' => 'Medewerkers',
                'description' => 'Medewerkers overzicht en medewerkers.',
                'installed' => isset($installedModules['medewerkers']) ? $installedModules['medewerkers'] : false
            ),
            'news' => array(
                'name' => 'Nieuws',
                'description' => 'Nieuws overzicht en nieuwsberichten.',
                'installed' => isset($installedModules['news']) ? $installedModules['news'] : false
            ),
            'producten' => array(
                'name' => 'Producten',
                'description' => 'Producten overzicht en producten.',
                'installed' => isset($installedModules['producten']) ? $installedModules['producten'] : false
            ),
            'referenties' => array(
                'name' => 'Referenties',
                'description' => 'Referenties overzicht en referenties.',
                'installed' => isset($installedModules['referenties']) ? $installedModules['referenties'] : false
            ),
            'vacatures' => array(
                'name' => 'Vacatures',
                'description' => 'Vacatures overzicht en vacatures.',
                'installed' => isset($installedModules['vacatures']) ? $installedModules['vacatures'] : false
            )
        );
        // Return all or a specific module
        if (! empty($getModuleByName)) {
            return isset($currentModules[$getModuleByName]) ? $currentModules[$getModuleByName] : false;
        }
        return $currentModules;
    }

    /**
     * Add additional information for the module installation page.
     *
     * @param string $moduleName  The module name.
     * @param array  &$moduleData The module data.
     */
    private function _currentModuleInstallInformation($moduleName, &$moduleData)
    {
        switch ($moduleName) {
            case 'algemeen':
                break;
            case 'diensten':
                break;
            case 'medewerkers':
                $moduleData['tabs'] = array(
                    'installMain' => array(
                        'label' => 'Algemeen',
                        'url' => '#tab-main'
                    ),
                    'installSections' => array(
                        'label' => 'Secties',
                        'url' => '#tab-sections'
                    ),
                    'installEntries' => array(
                        'label' => 'Entries',
                        'url' => '#tab-entries'
                    )
                );
                $moduleData['main'] = array(
                    array(
                        'name' => 'Aantal secties',
                        'value' => 2
                    ),
                    array(
                        'name' => 'Type secties',
                        'value' => 'Single & channel'
                    )
                );
                $moduleData['sections'] = array(
                    array(
                        'type' => 'Overzicht',
                        'name' => 'overview',
                        'label' => 'Medewerker overzicht',
                        'info' => 'Naam van het overzicht in de CP.'
                    ),
                    array(
                        'type' => 'Entry',
                        'name' => 'entry',
                        'label' => 'Medewerker',
                        'info' => 'Naam van de entry in de CP.'
                    )
                );
                $moduleData['entries'] = array(
                    array(
                        'type' => 'checkbox',
                        'name' => 'installTestEntries',
                        'value' => 1,
                        'checked' => false,
                        'label' => 'Installeer test entries?'
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'testEntriesAmount',
                        'value' => 1,
                        'options' => array_combine(range(1,10), range(1,10)),
                        'label' => 'Het aantal te installeren entries'
                    )
                );
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
    }
}