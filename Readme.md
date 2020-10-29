# Build Spec Generator
This module generates build spec related configurations to md file. This is experimental module so it might break depending on module setup.

## Installation

## Setup
Add following configurations in settings.php

#### Settings for local file export:
**Destination directory:**

Destination directory is where markup files will be stored. These marekup files are in md format.
```
$settings['build_spec_directory'] = '../docs/spec';
```

#### Settings for Google Sheet Export:

**Google API Credentials directory:**


Credentials directory can be outside of your docroot or any other place. This is where we need to store the `credentials.json` file provided by Google API. Google API will also generate `token.json` file in the same directory.
```
$settings['google_credentials_directory'] = '../credentials';
```

**Google Spreadsheet ID:**

In-case we are storing specification in Google sheet we need to provide google sheet ID.
```
$settings['google_spreadsheet_id'] = 'TEST';
```
Spreadsheet id can be found from the Google Sheet URL
https://docs.google.com/spreadsheets/d/spreadsheet-ID/


## Extend
Create additional "BuildSpec" plugins related to other requirement.
