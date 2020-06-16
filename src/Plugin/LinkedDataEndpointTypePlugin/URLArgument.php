<?php

namespace Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePlugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePluginBase;
use JmesPath\Env as JM;
/**
 * Class URLArgument
 *
 * @LinkedDataEndpointTypePlugin(
 *   id = "url_argument",
 *   label = @Translation("URL Argument type"),
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

    return $this->parseResponse($response, $endpoint);
  }

  /**
   * @param $response
   * @param $endpoint
   *
   * @return array|false
   */
  protected function parseResponse($response, $endpoint) {
    $root = $endpoint->get('result_json_path');
    $data = JM::search($root, $response);

    $output = [];
    $label_key = $endpoint->get('label_key');
    $url_key = $endpoint->get('url_key');

    foreach ($data as $i => $result) {
      $output[$result->{$label_key}] = $result->{$url_key};
    }
    return $output;
  }
}
