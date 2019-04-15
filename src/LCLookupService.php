<?php

namespace Drupal\lc_subject_field;
use GuzzleHttp\ClientInterface;

/**
 * Class LCLookupService.
 */
class LCLookupService implements LCLookupServiceInterface {

  /**
   * GuzzleHttp\ClientInterface definition.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;
  /**
   * Constructs a new LCLookupService object.
   */
  public function __construct(ClientInterface $http_client) {
    $this->httpClient = $http_client;
  }

  public function getSuggestions($candidate) {

  }


}
