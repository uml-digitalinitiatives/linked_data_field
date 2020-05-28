<?php

namespace Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePlugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePluginBase;

/**
 * Class URLArgument
 *
 * @LinkedDataEndpointTypePlugin(
 *   id = "url_argument",
 *   label = @Translation("URL ARgument type"),
 *   description = @Translation("Query is simply added to the end of a URL endpoint.")
 * )
 */
class URLArgument extends LinkedDataEndpointTypePluginBase {


  /**
   * Return suggestions from the lookup service API based on given input.
   *
   * @param string $candidate
   *   The input string to get suggestions based on.
   *
   * @return mixed
   *   Array of suggestions from the API.
   */
  public function getSuggestions($candidate) {
    // Get base URL from config.
    $endpoint = $this->configuration['endpoint'];
    $base_url = $endpoint->get('base_url');
    // For automated testing.
    if ((string) $base_url == 'http://test.test/') {
      return $this->getTestData($candidate);
    }
    $request = $this->httpClient->get($endpoint->get('base_url') .
      urlencode($candidate));
    $response = json_decode($request->getBody());
    $combined_results = array_combine($response[$endpoint->get('label_key')], $response[$endpoint->get('url_key')]);

    return $combined_results;
  }

  function getSettingsFormItems(array &$form, FormStateInterface $form_state, $plugin_settings) {
    // TODO: Implement getSettingsFormItems() method.
  }
}
