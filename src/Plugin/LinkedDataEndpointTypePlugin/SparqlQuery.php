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
    $query = $this->t($endpoint->configuration['sparql_query'], ['%input', $candidate]);
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
        '#description' => $this->t('SPARQL Query to return array with label and URL values for candidate string. Use "@input" to insert the string being searched for.'),
        '#default_value' => $plugin_settings['sparql_query-sparql_query'],
      ]
    ];
  }
}
