<?php

namespace Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePlugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePluginBase;

/**
 * Class URLArgument
 *
 * @LinkedDataEndpointTypePlugin(
 *   id = "sparql_query",
 *   label = @Translation("SPARQL Query"),
 *   description = @Translation("SPARQL Query endpoint such as GRID or WikiData")
 * )
 */
class SparqlQuery extends LinkedDataEndpointTypePluginBase {


  public function getSuggestions($candidate) {
    $endpoint = $this->configuration['endpoint'];
    $sparql_endpoint = $endpoint->get('base_url');
    $input = strtolower($candidate);
    $query = <<< QUERY
SELECT DISTINCT ?org ?grid ?orglabel
WHERE
{
  {
  ?org wdt:P2427 ?grid.
       ?org rdfs:label ?orglabel
   }
  union
    {
  ?org wdt:P2427 ?grid.
       ?org skos:altLabel ?orglabel
   }
  FILTER CONTAINS(lcase(?orglabel), '$input') .
  FILTER(lang(?orglabel) = 'en')
}
LIMIT 10

QUERY;
    $response = $this->httpClient->request('GET', $sparql_endpoint,
      [
        'headers' => [
          'Accept' => 'application/sparql-results+json, application/json',
        ],
        'query' => ['query' => $query],
      ]);

    $json = $response->getBody()->getContents();
    $data = json_decode($json);


    $items = $data->results->bindings;

    foreach ($items as $item) {
      $label = $item->orglabel->value;
      $url = "https://www.grid.ac/institutes/{$item->grid->value}";
      $output[$label] = $url;
    }
    return $output;
  }

  public function getSettingsFormItems(array &$form, FormStateInterface $form_state, $plugin_settings) {
    $endpoint = $this->configuration['endpoint'];

    return ['sparql_query' =>
      [
        '#type' => 'textarea',
        '#title' => $this->t('SPARQL Query'),
        '#description' => $this->t('SPARQL Query to return array with label and URL values for candidate string.'),
        '#default_value' => $plugin_settings['sparql_query-sparql_query'],
      ]
    ];
  }
}
