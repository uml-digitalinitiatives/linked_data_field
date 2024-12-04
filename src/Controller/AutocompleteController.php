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
   *   Return Autocomplete result string.
   */
  public function handleAutocomplete(LinkedDataEndpointInterface $linked_data_endpoint = NULL, Request $request = NULL) {
    $results = [];
    $endpoint_id = array_slice(explode('/', $request->getPathInfo()), -1)[0];
    $endpoint = $this->entityTypeManager()->getStorage('linked_data_endpoint')->load($endpoint_id);
    $plugin = $this->ldEntityTypePluginManager->createInstance($endpoint->get('type'), ['endpoint' => $endpoint]);

    $debug = $request->query->get('_ldquery_debug');

    if ($input = $request->query->get('q')) {
      // Remove quotes from the input string. These are necessary if you want a comma in the string.
      if (str_starts_with($input, '"')) {
        $input = substr($input, 1);
      }
      if (str_ends_with($input, '"')) {
        $input = substr($input, 0, -1);
      }
      $typed_string = Tags::explode($input);
      if (count($typed_string) > 1) {
        // Tags separates on commas, but we want to treat the entire string as a single search term.
        $typed_string = implode(', ', $typed_string);
      }
      else {
        $typed_string = array_pop($typed_string);
      }
      $typed_string = mb_strtolower($typed_string);
      $service_results = $plugin->getSuggestions($typed_string, $debug);

      // We changed the format of the results from [value => label] to ['value' => $value, 'label' => $label].
      // In case a plugin has not been updatd, we put the results into that format here.
      if (count($service_results) && !isset($service_results[0]['value'])) {
        foreach ($service_results as $label => $url) {
          $results[] = ['value' => $url, 'label' => $label];
        }
      }
      else {
          $results = $service_results;
        }
    }

    return new JsonResponse($results);
  }

}
