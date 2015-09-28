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

## Dungeon JSON Format

Check out the [example JSON](dungeons/exampleDungeon.json).

The *x* and *y* root is at the top left corner. This way we can access the values with `terrain[y][x]`.

| `terrain` id | meaning | passable |
|:-------------|:--------|:---------|
| 0            | grass   | yes      |
| 1            | rock    | no       |
