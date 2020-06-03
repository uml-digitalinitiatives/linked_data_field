<?php

namespace Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePlugin;

use Drupal\Component\Render\FormattableMarkup;
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
    $query = new FormattableMarkup($endpoint->getThirdPartySetting('linked_data_field', 'sparql_query-sparql_query'), ['@input' => $input]);
    $response = $this->httpClient->request('GET', $sparql_endpoint,
      [
        'headers' => [
          'Accept' => 'application/sparql-results+json, application/json',
        ],
        'query' => ['query' => (string) $query],
      ]);

    $json = $response->getBody()->getContents();
    $data = json_decode($json);


    $items = $data->results->bindings;
    $label_key = $endpoint->get('label_key');
    $url_key = $endpoint->get('url_key');

    foreach ($items as $item) {
      $label = $item->{$label_key}->value;
      $url = $item->$url_key->value;
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
        '#rows' => 25,
        '#default_value' => $plugin_settings['sparql_query-sparql_query'],
      ]
    ];
  }
}
