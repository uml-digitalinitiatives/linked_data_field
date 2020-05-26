<?php

namespace Drupal\linked_data_field\Controller;

use Drupal\Component\Utility\Tags;
use Drupal\Core\Controller\ControllerBase;
use Drupal\linked_data_field\Entity\LinkedDataEndpointInterface;
use Drupal\linked_data_field\LDLookupServiceInterface;
use Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AutocompleteController.
 */
class AutocompleteController extends ControllerBase {

  /**
   * Service to do LC Subject lookups.
   *
   * @var Drupal\linked_data_field\LDLookupServiceInterface
   */
  protected $ldLookup;

  /**
   * The plugin manager for endpoint types.
   *
   * @var \Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePluginManager
   */
  protected $ldEntityTypePluginManager;

  /**
   * AutocompleteController constructor.
   *
   * @param Drupal\linked_data_field\LDLookupServiceInterface $ld_lookup_service
   *   The lookup service to query against.
   */
  public function __construct(LinkedDataEndpointTypePluginManager $ld_entity_type_plugin_manager) {
    $this->ldEntityTypePluginManager = $ld_entity_type_plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.linked_data_endpoint_type_plugin')
    );
  }

  /**
   * Autocomplete controller.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The page request object.
   *
   * @return string
   *   Return Hello string.
   */
  public function handleAutocomplete(LinkedDataEndpointInterface $linked_data_endpoint = NULL, Request $request = NULL) {
    $results = [];
    $endpoint_id = array_pop(explode('/', $request->getPathInfo()));
    $endpoint = $this->entityTypeManager()->getStorage('linked_data_endpoint')->load($endpoint_id);
    $plugin = $this->ldEntityTypePluginManager->createInstance($endpoint->get('type'), ['endpoint' => $endpoint]);

    if ($input = $request->query->get('q')) {
      $typed_string = Tags::explode($input);
      $typed_string = mb_strtolower(array_pop($typed_string));
      $service_results = $plugin->getSuggestions($typed_string);
      foreach ($service_results as $subject => $url) {
        $results[] = ['value' => $url, 'label' => $subject];
      }
    }
    return new JsonResponse($results);
  }

  /**
   * Autocomplete Controller for GRID requests.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The page request object.
   *
   * @return string
   *   Return Hello string.
   */
  public function handleGridAutocomplete(Request $request) {
    $output = [];
    if ($input = $request->query->get('q')) {
      $items = $this->ldLookup->getGridSuggestions($input);
      foreach ($items as $item) {
        $label = $item->orglabel->value;
        $url = "https://www.grid.ac/institutes/{$item->grid->value}";
        $output[] = [
          'value' => $url,
          'label' => $label,
        ];
      }
    }
    return new JsonResponse($output);
  }

  /**
   * Autocomplete Controller for Crossref Funder requests.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The page request object.
   *
   * @return string
   *   Return Hello string.
   */
  public function handleFunderAutocomplete(Request $request) {
    $output = [];
    if ($input = $request->query->get('q')) {
      $items = $this->ldLookup->getFunderSuggestion($input);
      foreach ($items as $item) {

        $output[] = [
          'value' => $item->uri,
          'label' => $item->name,
        ];
      }
    }
    return new JsonResponse($output);
  }

}
// Todo on Monday
// Add routes, build service, add fields to config.
