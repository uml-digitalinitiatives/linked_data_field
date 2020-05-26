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

## Architecture

Custom endpoints are stored as configuration entities.
The endpoint entities can be managed at /admin/structure/linked_data_endpoint

The module provides a plugin type to handle different request formats
and different requirements for parsing results.

To add a custom endpoint, create a child class of \Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePlugin,
in the namespace \Drupal\[my_module]\Plugin\LinkedDataEndpointTypePlugin.

Add the following annotation fields to your plugin class:

    /**
     * Class MyEndpointTypePlugin
     *
     * @LinkedDataEndpointTypePlugin(
     *   id = "my_endpoint_type",
     *   label = @Translation("My custom linked data endpoint type"),
     *   description = @Translation("Query is simply added to the end of a URL endpoint.")
     * )
     */

You can then create multiple endpoint configuration entities that employ
teh custom plugin. The config entity can have different base URLs, e.g.,
different URL query parameters. The plugin is shared by any requests that share
the same result format and can be parsed in the same way.

Config entiteis also allow you to specify custom JSON keys to pick the
results out of.

If a response looks like:

    [
      {
        label: "First result",
        url: "https://resultsite.com/canonicalpath/233332.html"
      },
      {
        label: "Second result",
        url: "https://resultsite.com/canonicalpath/342422.html"
      }
    ]

Then then the "Label key" config entity field would be "label", and the
"URL key" config entity field would be "url".

If the result has arrays instead of keys, e.g.

    [
      [
        "First item label",
        "second item label"
      ],
      [
        "https://results.com/first.html",
        "https://results.com/second.html"
      ]
    ]

Then you can use the Library of Congress subject headings entity type
plugin to parse the results. In this case, the Label key field would be 0
and the URL Key field would be 1.

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
