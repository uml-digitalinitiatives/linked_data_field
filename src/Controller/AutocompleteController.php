<?php

namespace Drupal\lc_subject_field\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \Symfony\Component\HttpFoundation\JsonResponse;
use GuzzleHttp\ClientInterface;


/**
 * Class AutocompleteController.
 */
class AutocompleteController extends ControllerBase {

  /**
   * GuzzleHttp\ClientInterface definition.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Constructs a new AutocompleteController object.
   */
  public function __construct(ClientInterface $http_client) {
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client')
    );
  }

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function handleAutocomplete($field_name, $count) {
    $results = [];

    $results = [['value' => 'first value', 'label' => 'First label'],['value' => 'second value', 'label' => 'Second Label']];
    return new JsonResponse($results);

  }

}
