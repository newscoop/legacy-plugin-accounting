# Accounting plugin

The plugin contains a widget which allows for filtering articles by authors,
publication dates and exporting this as an .xls file.

### Featurelist
* Filter article lists by author (via autocomplete)
* Filter article list by publication date, via month selection or via manual start and end date selection
* Export the filtered articles as .xls file
* Add switches to article types which will be displayed in the article list and can contain additional information

### Switches
Adding switches to article types allow for easy storing additional information and displaying them. For the switch data to become visible in the widget, the name of the switch should start with the prefix *rep_*. One can add translations to switches, they will be used as values displayed in the table. If no translation has been added, the *Template Field Name* will be used.

### Configuration
For the plugin to work the following parameters need to be set.

```yaml
accounting:
    transliterate_data: true
    transliteration_language_id: 1
```

**transliterate_data**: enable translateration of data
**transliteration_language_id**: language id into which the data will be transliterated

For now this feature only works with author names, but it could be easily extended
to include all values in Newscoop which can be translated. It would even be possible
to add transliteration to values which are not translatable by default.
