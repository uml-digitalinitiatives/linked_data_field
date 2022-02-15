# Linked Data Lookup Field

Provides an autocomplete field widget that
pulls suggested matches and URLs from various authoritative
sources.

As examples, the module provides [Library of Congress Subject Headings][1],
[Research Organization Registry](2)
 entries, and [CrossRef Funder identifiers][3]
and [Australian and New Zealand Standard Research Classifications][4] out of the box. More
endpoints can be added by following the plugin pattern.

The authority link is then stored along with the chosen
item.

[1]: http://id.loc.gov/authorities/subjects.html

[2]:https://ror.org/

[3]: https://www.crossref.org/services/funder-registry/

[4]: https://www.abs.gov.au/ausstats/abs@.nsf/0/6BB427AB9696C225CA2574180004463E

## Installation

This module uses the Jmespath.php library, so you should install it with
composer:

    composer require drupal/linked_data_field


## Linked Data Lookup Endpoint Configuration Entities

Custom endpoints are stored as configuration entities.
The endpoint entities can be managed at /admin/structure/linked_data_endpoint

Config entities also allow you to specify custom JSON keys to pick the
results out of the JSON response body.

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

Then you can use the URL Argument plugin, and the "Label key" field would be "label", and the
"URL key" field would be "url".

If the result has arrays instead of keys, e.g.:

    [
      [
        "First item label",
        "https://results.com/first.html",
      ],
      [
        "second item label"
        "https://results.com/second.html"
      ]
    ]

In this case, the Label key field would be 0
and the URL Key field would be 1.

### JmesPath Expressions

If your results aren't at the top level of the JSON structure, then you can
add a [JmesPath](https://jmespath.org) expression to tell the plugin where the root of the results items is.

For example, if the raw JSON looks like this:

    {
      "result": {
        "numresults": "100",
        "firstresult": "0",
        "items": [
          {
            "name": "First result",
            "url": "https://example.com/",
            "id": "000001"
          }
        ]
      }
    }

Then you can set:

 - Result JSON Path to "result.items"
 - Label Key to "name", and
 - URL Key to "url".

### SPARQL Plugin

If you select the SPARQL endpoint type when adding an endpoint, an extra
text area field appears where you can paste a SPARQL query.

The important parts are:

 - Add "@input" where the string to be quaried should go, and
 - Label JSON key and URL JSON Key contain the variables you are asking for in the query.

You can inspect the supplied examples,
[Australian and New Zealand Standard Research Classification](https://en.wikipedia.org/wiki/Australian_and_New_Zealand_Standard_Research_Classification)
in this module's src/Plugin/LinkedDataEndpointType directory.

### Creating Custom Plugins

The module provides a plugin manager to handle different request formats
and different requirements for parsing results.

To add a custom endpoint type, create a child class of \Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePlugin,
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
the custom plugin. The config entity can have different base URLs, e.g.,
different URL query parameters. The plugin is shared by any requests that share
the same result format and can be parsed in the same way.

## Authors

Alexander O'Neill (Maintainer) https://drupal.org/u/alxp

Alan Stanley (Major improvements) https://drupal.org/u/Alan_Stanley

## Supporting Organization

[University of Prince Edward Island Robertson Library][5]

Development was funded as part of a grant from [CANARIE][6]

[5]: https://library.upei.ca/

[6]: https://www.canarie.ca/

## Special Thanks

Christina Harlow's [LC Reconcile][7] Google Refine plugin was the inspiration for this module.

[7]: https://github.com/cmharlow/lc-reconcile
