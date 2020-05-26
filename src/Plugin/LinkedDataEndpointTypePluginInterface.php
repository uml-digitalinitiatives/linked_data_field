<?php

namespace Drupal\linked_data_field\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Linked data endpoint type plugin plugins.
 */
interface LinkedDataEndpointTypePluginInterface extends PluginInspectionInterface {

  /**
   * Return suggestions from the lookup service API.
   *
   * @param string $candidate
   *   The input string to get suggestions based on.
   *
   * @return mixed
   *   Array of suggestions from the API.
   */
  public function getSuggestions($candidate);

}
