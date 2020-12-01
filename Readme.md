# Build Spec Generator
This module generates build spec related configurations to md file. This is experimental module so it might break depending on module setup.

## Installation

## Setup
Currently, we have two types of storage plugins:
* Local file export: It exports configuration to markdown files.
* Google Sheet export: It exports configuration to Google Sheet files.

## Settings for local file export:
Primarily local file export allows us to export configuration in one directory.
It uses markdown format to export the configuration. It display configuration in table format.

For this storage plugin, we just need to provide destination directory. Our plugin will export markdown files in specified destination directory.
**Destination directory:**

Destination directory is where markup files will be stored. These marekup files are in md format.
```
$settings['build_spec_directory'] = '../docs/spec';
```

## Settings for Google Sheet Export:

**Google API Credentials directory:**
Credentials directory can be outside of your docroot or any other place. This is where we need to store the `credentials.json` file provided by Google API. Google API will also generate `token.json` file in the same directory.

For reference, i've created outside docroot.
```
$settings['google_credentials_directory'] = '../credentials';
```
**Generating Google API credentials:**
You will need [Google API Credentials](https://developers.google.com/sheets/api/quickstart/php) - just complete step 1 and save your credentials.json to your credentials-path.
In our case, store it in `credentials` directory, created in above step.

So at the end of this step we will have valid `credentials/credentials.json` file.
Google API also, exports `token.json` in the same directory. We don't need to worry about it as it happens automatically.

**Google Spreadsheet ID:**

In-case we are storing specification in Google sheet we need to provide google sheet ID.
```
$settings['google_spreadsheet_id'] = 'TEST';
```
Spreadsheet id can be found from the Google Sheet URL
https://docs.google.com/spreadsheets/d/spreadsheet-ID/


## Extend
Create additional "BuildSpec" plugins related to other requirement.
