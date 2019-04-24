<?php

namespace Drupal\lc_subject_field;
use GuzzleHttp\ClientInterface;
use Drupal\Core\Config\ConfigFactoryInterface;


/**
 * Class LCLookupService.
 */
class LCLookupService implements LCLookupServiceInterface {

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;


  /**
   * GuzzleHttp\ClientInterface definition.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Constructs a new LCLookupService object.
   */
  public function __construct(ClientInterface $http_client, ConfigFactoryInterface $config_factory) {
    $this->httpClient = $http_client;
    $this->configFactory = $config_factory;
  }

  public function getSuggestions($candidate) {
    // Get base URL from config.
    $config = $this->configFactory->get('lc_subject_field.settings');
    $base_url = $config->get('base_url');
    // For automated testing.
    if ((string) $base_url == 'http://test.test/') {
      return $this->testData($candidate);
    }
    $request = $this->httpClient->get($base_url . 'authorities/subjects/suggest/?q=' .
    urlencode($candidate));
    $response = json_decode($request->getBody());
    return array_combine($response[1], $response[3]);
  }

  protected function testData($candidate) {
    if ($candidate[0] == 'a') {
      return ['Alien Abduction' => 'http://nasa.gov/',
        'Apple, Inc.' => 'http://apple.com/',];
    }
    else {
      return ['borax' => 'http://bbc.co.uk/',
        'Bat' => 'http://bat.cave'];
    }
  }
}
