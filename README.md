# TheLegendOfROOT

## Setup

### Apache

`<git root>/config/include.conf`
```
Include <git root>/config/apache.macro
Use TheLegendOfROOT "<git root>"
UndefMacro TheLegendOfROOT
```

`httpd.conf`
Load macro module and link to our new server config.
```
LoadModule macro_module modules/mod_macro.so
Include <git root>/config/include.conf
```
