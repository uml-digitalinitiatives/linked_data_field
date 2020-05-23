<?php

namespace Drupal\linked_data_field\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Linked data endpoint type plugin plugin manager.
 */
class LinkedDataEndpointTypePluginManager extends DefaultPluginManager {


  /**
   * Constructs a new LinkedDataEndpointTypePluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/LinkedDataEndpointTypePlugin', $namespaces, $module_handler, 'Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePluginInterface', 'Drupal\linked_data_field\Annotation\LinkedDataEndpointTypePlugin');

    $this->alterInfo('linked_data_field_linked_data_endpoint_type_plugin_info');
    $this->setCacheBackend($cache_backend, 'linked_data_field_linked_data_endpoint_type_plugin_plugins');
  }

}
