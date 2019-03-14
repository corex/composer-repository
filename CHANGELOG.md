# Changelog

## 0.6.1

### Fixed
- Fixed long list of versions.

## 0.6.0

### Fixed
- Removed unused property in Browser::class.
- Added phpdoc exception.

### Added
- Added notifications of new/updated packages.

## 0.5.2

### Fixed
- Added nobr on packages list.

## 0.5.1

### Fixed
- Fixed sorting of packages.

## 0.5.0

### Changed.
- Refactored codebase to use corex/site, controllers and templates.

## 0.4.0

### Added
- Added command to order a build.

### Changed
- Require php 7.2+ due to package requirements.
- Moved Input::class to project.
- "satis.json" is not removed after build.
- Moved Path::class to /Helpers.

### Fixed
- Removed deprecated composer package corex/support in favor of corex/helpers, corex/filesystem, corex/sessions, corex/terminal.
- Added missing validation of tabs on Tabs command.
- Fixed getting version on show:all command.
- Fixed hiding on all commands.

## 0.3.4

### Added
- Added link to Packagist on standard template.

## 0.3.3

### Fixed
- Fixed build counter.

## 0.3.2

### Fixed
- Fixed quiet option.

## 0.3.1

### Changed
- Commands package:add-signature and package:add-url combined into package:add.

## 0.3.0

### Added
- Added build ordering.

## 0.2.2

### Fixed
- Fixed loading page when no packages has been build.

## 0.2.1

### Fixed
- Added "white-space:nowrap;" so that package-name does not wrap on page packages.

## 0.2.0

### Added
- Added source of package on page package.
- Added page location to show composer repository location for "composer.json".

## 0.1.1

### Fixed
- Fixed allowed tabs.

## 0.1.0

### Added
- Added option to set allowed tabs for specific signatures through command config:tabs.

## 0.0.5

### Fixed
- Better detection of vendor directory.

## 0.0.4

### Fixed
- Checking for path on build- or clear-commands.

## 0.0.3

### Added
- Fixed initial creation of config path.

## 0.0.2

### Removed
- Removed content commands since it is available through web and limited on cli.

## 0.0.1

### Added
- Initial dev release.
