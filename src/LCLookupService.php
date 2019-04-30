<?php

namespace Drupal\lc_subject_field;

use Drupal\Core\Config\ConfigFactoryInterface;
use GuzzleHttp\ClientInterface;

/**
 * Class LCLookupService.
 */
class LCLookupService implements LCLookupServiceInterface {

  /**
   * Configuration service.
   *
   * @var Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;


  /**
   * GuzzleHttp\ClientInterface definition.
   *
   * @var GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Constructs a new LCLookupService object.
   */
  public function __construct(ClientInterface $http_client, ConfigFactoryInterface $config_factory) {
    $this->httpClient = $http_client;
    $this->configFactory = $config_factory;
  }

  /**
   * Query the Subject Authority API for suggestions based on the given input.
   *
   * @param string $candidate
   *   The input to generate sugestions from.
   *
   * @return array|false
   *   The set of suggested subjects from the API.
   */
  public function getSuggestions($candidate) {
    // Get base URL from config.
    $config = $this->configFactory->get('lc_subject_field.settings');
    $base_url = $config->get('base_url');
    // For automated testing.
    if ((string) $base_url == 'http://test.test/') {
      return $this->getTestData($candidate);
    }
    $request = $this->httpClient->get($base_url . 'authorities/subjects/suggest/?q=' .
    urlencode($candidate));
    $response = json_decode($request->getBody());
    return array_combine($response[1], $response[3]);
  }

  /**
   * Return pre-defined content for test mode.
   *
   * @param string $candidate
   *   The input to the autocomplete service.
   *
   * @return array
   *   Array of test suggested completions.
   */
  protected function getTestData($candidate) {
    if ($candidate[0] == 'a') {
      return [
        'Alien Abduction' => 'http://nasa.gov/',
        'Apple, Inc.' => 'http://apple.com/',
      ];
    }
    else {
      return [
        'borax' => 'http://bbc.co.uk/',
        'Bat' => 'http://bat.cave'
      ];
    }
  }

}
