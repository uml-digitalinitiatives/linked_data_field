<?php

namespace Drupal\linked_data_field\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;

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

  /**
   * Get third-party settings from the plugin.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @param array $plugin_settings
   *
   * @return mixed
   */
  public function getSettingsFormItems(array &$form, FormStateInterface $form_state, $plugin_settings);

}
