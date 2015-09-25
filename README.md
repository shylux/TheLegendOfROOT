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
