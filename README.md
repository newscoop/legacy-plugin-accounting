# Accounting plugin

The plugin contains a widget which allows for filtering articles by authors,
publication dates and exporting this as an .xls file. 

This plugin is compatible with Newscop 4.3 and above.

### Featurelist
* Filter article lists by author (via autocomplete)
* Filter article list by publication date, via month selection or via manual start and end date selection
* Export the filtered articles as .xls file
* Add switches to article types which will be displayed in the article list and can contain additional information

### Switches
Adding switches to article types allow for easy storing additional information and displaying them. For the switch data to become visible in the widget, the name of the switch should start with the prefix *rep_*. One can add translations to switches, they will be used as values displayed in the table. If no translation has been added, the *Template Field Name* will be used.

### Installation
1. Copy the release archive file to the /newscoop/plugins/ directory
2. Extract all files from the archive
3. Add the configuration parameters to /newscoop/application/configs/parameters/custom_parameters.yml
4. Clear the cache directory (to reload the parameters)
5. Go to the plugins backend and enable the plugin
6. Add the widget on your dashboard

If the widget is not visible in the available widgets list, please check if the
files have been properly copied.
The directory /newscoop/plugins/accounting/extensions/accounting/ should be copied
into /newscoop/extensions/.  
Enabling and disabling the plugin will copy and
delete the files automatically, if permissions are set correct.

### Configuration
For the plugin to work the following parameters need to be set.

```yaml
accounting:
    transliterate_data: false
    transliteration_language_id: 1
```

**transliterate_data** (bool): enable translateration of data  
**transliteration_language_id** (int): language id into which the data will be transliterated

For now this feature only works with author names, but it could be easily extended
to include all values in Newscoop which can be translated. It would even be possible
to add transliteration to values which are not translatable by default.
