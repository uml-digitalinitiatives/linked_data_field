<?php

namespace Drupal\linked_data_field\Plugin;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface ;



/**
 * Base class for Linked data endpoint type plugin plugins.
 */
abstract class LinkedDataEndpointTypePluginBase extends PluginBase implements LinkedDataEndpointTypePluginInterface, ContainerFactoryPluginInterface {

  use LoggerChannelTrait;

  use StringTranslationTrait;

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
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\Session\AccountInterface $account
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $http_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
    $this->logger = $this->getLogger('linked_data_field');
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

  public function getSettingsFormItems(array &$form, FormStateInterface $form_state, $plugin_settings) {
    // TODO: Implement getSettingsFormItems() method.
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
   * @param \Exception|\GuzzleHttp\Exception\GuzzleException $error
   *
   * @return array
   */
  protected function handleHttpException(\Exception $error) {
    // Get the original response
    $response = $error->getResponse();
    $message = '';
    $debug_message = '';
    if (!is_null($response)) {
      // Get the info returned from the remote server.
      $response_info = $response->getBody()->getContents();

      // Using FormattableMarkup allows for the use of <pre/> tags, giving a more readable log item.
      $message = new FormattableMarkup('API connection error. Error details are as follows:<pre>@response</pre>', ['@response' => $response->getReasonPhrase()]);
    }
    $this->logger->warning('Linked Data API endpoint for {plugin_name}: {error} : {message}',
      ['plugin_name' => $this->getPluginId(), 'error' => $error->getMessage(), 'message' => $message]);
      if ($debug_message !== '') {
        $this->logger->debug('Full text of {plugin_name} error: <pre>{debug_message}</pre>',
          ['plugin_name' => $this->getPluginId(), 'debug_message' => $debug_message]);
      }

    return [];
  }
}
