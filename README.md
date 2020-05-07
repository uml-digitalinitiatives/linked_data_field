# Linked Data Field

Provides an autocomplete field widget that
pulls suggested matches and URLs from various authoritative
sources.

The module supports [Library of Congress Subject Headings][1],
[Global Research Identifier Database (GRID)][2] entries,
and [CrossRef Funder identifiers][3] out of the box. More
endpoints can be added by following the plugin pattern.

The authority link is then stored along with the chosen
item.

[1]: http://id.loc.gov/authorities/subjects.html

[2]: https://www.grid.ac

[3]: https://www.crossref.org/services/funder-registry/

## Installation

Enable the module as you normally would.

By default the LC Subjects API is set to http://id.loc.gov.
If you want to change this for whatever reason you can go to
/admin/config/linked_data_field/settings.

Add one of the Linked Data Fields to a content type.

## Authors

Alexander O'Neill (Maintainer) https://drupal.org/u/alxp

Alan Stanley (Major improvements) https://drupal.org/u/Alan_Stanley

## Supporting Organization

[University of Prince Edward Island Robertson Library][4]

Development was funded as part of a grant from [CANARIE][4]

[4]: https://library.upei.ca/

[5]: https://www.canarie.ca/

## Special Thanks

Christina Harlow's [LC Reconcile][6] Google Refine plugin was the inspiration for this module.

[6]: https://github.com/cmharlow/lc-reconcile
