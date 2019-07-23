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
   *   The input to generate suggestions from.
   *
   * @return array|false
   *   The set of suggested subjects from the API.
   */
  public function getSuggestions(string $candidate) {
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
  protected function getTestData(string $candidate) {
    if ($candidate[0] == 'a') {
      return [
        'Alien Abduction' => 'http://nasa.gov/',
        'Apple, Inc.' => 'http://apple.com/',
      ];
    }
    else {
      return [
        'borax' => 'http://bbc.co.uk/',
        'Bat' => 'http://bat.cave',
      ];
    }
  }

  /**
   * Query the Wikidata for GRID references.
   *
   * @param string $candidate
   *   The input to generate suggestions from.
   *
   * @return array|false
   *   The set of suggested subjects from the API.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getGridSuggestions(string $candidate) {
    $config = $this->configFactory->get('lc_subject_field.settings');
    $sparql_endpoint = $config->get('grid_url');
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
    return $data->results->bindings;
  }

  /**
   * Query the Crossref for Funders .
   *
   * @param string $input
   *   The input to generate suggestions from.
   *
   * @return array|false
   *   The set of suggested subjects from the API.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getFunderSuggestion(string $input) {
    $config = $this->configFactory->get('lc_subject_field.settings');
    $crossref_endpoint = $config->get('funder_url');
    $response = $this->httpClient->request('GET', $crossref_endpoint, [
      'query' => [
        'query' => $input,
        'rows' => 10,
      ],
    ]);
    $json = $response->getBody()->getContents();
    $data = json_decode($json);
    $items = $data->message->items;
    $items = array_slice($items, 0, 10);
    return $items;

  }

}
