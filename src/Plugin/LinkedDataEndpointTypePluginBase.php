<?php

namespace Drupal\linked_data_field\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for Linked data endpoint type plugin plugins.
 */
abstract class LinkedDataEndpointTypePluginBase extends PluginBase implements LinkedDataEndpointTypePluginInterface, ContainerFactoryPluginInterface {


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
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\Session\AccountInterface $account
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $http_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client')
    );
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
  public function getSuggestions($candidate) {

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


}
