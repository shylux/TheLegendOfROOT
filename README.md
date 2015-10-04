# TheLegendOfROOT

## Setup

### Apache

`httpd.conf`
```
LoadModule macro_module modules/mod_macro.so
Include <git root>/config/apache.macro
Use TheLegendOfROOT "<git root>"
UndefMacro TheLegendOfROOT
```
### Configuration

The class class.configuration.php stores the configuration object. It loads it's data from a config file - this file must exist and must be writable. To use the configuration object just do

$config = new Configuration("filePath...");
$config->setConfiguration("key", "value"); // The key nor the value must include a '=', otherwise it will not work. In the class the control char can be set (private $controlChar).
$config->saveConfiguration();

Access data from the configuration:
$config->getConfiguration("key");


## Dungeon JSON Format

Check out the [example JSON](dungeons/exampleDungeon.json).

The *x* and *y* root is at the top left corner. This way we can access the values with `terrain[y][x]`.

| `terrain` id | meaning    | passable   | note                |
|:-------------|:-----------|:-----------|:--------------------|
| 0            | grass      | yes        |                     |
| 1            | rock       | no         |                     |
| 2            | water      | no (boat?) |                     |
| 3            | bridge     | yes        |                     |
| 4            | tall grass | yes        | entities are hidden |
| 5            | road       | yes        |                     |
| 6            | sand       | yes        |                     |
