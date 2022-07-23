# PocketMine Tools

- Tools to simplify developing PocketMine plugins.

## Project contains these tools:
- Workspace generator - Script which generates plugin workspace
- Plugin build script - An advanced script for building plugin. Allows to include composer libraries

## Workspace generator
- The easiest way is to copy the code below and run it.

```php
<?php
file_put_contents("workspace-create-script.php", file_get_contents("https://raw.githubusercontent.com/CzechPMDevs/PocketMineTools/master/scripts/workspace-create-script.php"));
require "workspace-create-script.php";
```