# Accounting plugin

The plugin contains a widget which allows for filtering articles by authors, publication dates and expoting this as an .xls file.

### Featurelist
* Filter article lists by author (via autocomplete)
* Filter article list by publication date, via month selection or via manual start and end date selection
* Export the filtered articles as .xls file
* Add switches to article types which will be displayed in the article list and can contain additional information

### Switches
Adding switches to article types allow for easy storing additional information and displaying them. For the switch data to become visible in the widget, the name of the switch should start with the prefix *rep_*. One can add translations to switches, they will be used as values displayed in the table. If no translation has been added, the *Template Field Name* will be used.
