# a&m installer

_Install modules in Craft that'll create most things you need, automatically_

## Changelog

### v0.4

- Added the option to install the fields that you want by using checkboxes.
- Installing sections is no longer blocked if one that is about to be installed, already exists, we just skip it.
- Fixed a bug where global fields could give an error when that field wasn't amended dynamically.
- Fixed a bug where globals couldn't be installed if the set already exists.


### v0.3

- Ability to install parts, rather than the whole package (E.g.: only install fields or globals).
- Simplified some of the installation logic that was defined in module files.
- Globals added as a new tab page in the installation overview.
- Fields now has it's own tab page in the installation overview.
- A tabs file for a module is no longer required.
- Module files explanations added.
- Everything that wasn't translated, is now translated.

### v0.2

- Only allow the plugin to be installed if the user doesn't have a personal Craft license.
- Ability to create a template group with templates, if the module has any available templates.
- Ability to set dynamic values from installed sections, as the global field value.
- Fields within a global set can have their value set with the installation of the field.
- Ability to create global sets with fields.
- Added an option to make fields translatable.
- Translations added.
- Fixed _camelString fuction to no longer allow quotes and such in the string.

### v0.1

- Overview of what will be created once you install a module.
- Ability to create sections.
- Ability to create field groups & fields.
- Ability to create test entries with test content.
- Ability to create dynamic modules.