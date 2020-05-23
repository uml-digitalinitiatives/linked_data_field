<?php

namespace Drupal\linked_data_field\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Linked data endpoint type plugin item annotation object.
 *
 * @see \Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePluginManager
 * @see plugin_api
 *
 * @Annotation
 */
class LinkedDataEndpointTypePlugin extends Plugin {


  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The description of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

}

