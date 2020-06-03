<?php

namespace Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePlugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePluginBase;
use Rs\Json\Pointer;
use Rs\Json\Pointer\InvalidJsonException;
use Rs\Json\Pointer\NonexistentValueReferencedException;


/**
 * Class LoCAuthority
 *
 * @LinkedDataEndpointTypePlugin(
 *   id = "lc_authority",
 *   label = @Translation("LoC Authority Field"),
 *   description = @Translation("Handles LoC's column-based response format.")
 * )
 */
class LoCAuthority extends URLArgument {


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
   * LoC data is in a funny format with all labels in one array
   * and all URL identifiers in a separate array.
   *
   * @param $response
   * @param $endpoint
   *
   * @return array|false
   */
  protected function parseResponse($response, $endpoint) {
    $combined_results = array_combine($response[$endpoint->get('label_key')], $response[$endpoint->get('url_key')]);

    return $combined_results;
  }
}
