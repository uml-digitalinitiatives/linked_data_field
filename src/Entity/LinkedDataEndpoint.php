<?php

namespace Drupal\linked_data_field\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Linked Data Lookup Endpoint entity.
 *
 * @ConfigEntityType(
 *   id = "linked_data_endpoint",
 *   label = @Translation("Linked Data Lookup Endpoint"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\linked_data_field\LinkedDataEndpointListBuilder",
 *     "form" = {
 *       "add" = "Drupal\linked_data_field\Form\LinkedDataEndpointForm",
 *       "edit" = "Drupal\linked_data_field\Form\LinkedDataEndpointForm",
 *       "delete" = "Drupal\linked_data_field\Form\LinkedDataEndpointDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\linked_data_field\LinkedDataEndpointHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "linked_data_endpoint",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "type" = "type",
 *     "base_url" = "base_url",
 *     "label_key" = "label_key",
 *     "url_key" = "url_key"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/linked_data_endpoint/{linked_data_endpoint}",
 *     "add-form" = "/admin/structure/linked_data_endpoint/add",
 *     "edit-form" = "/admin/structure/linked_data_endpoint/{linked_data_endpoint}/edit",
 *     "delete-form" = "/admin/structure/linked_data_endpoint/{linked_data_endpoint}/delete",
 *     "collection" = "/admin/structure/linked_data_endpoint"
 *   }
 * )
 */
class LinkedDataEndpoint extends ConfigEntityBase implements LinkedDataEndpointInterface {

  /**
   * The Linked Data Lookup Endpoint ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Linked Data Lookup Endpoint label.
   *
   * @var string
   */
  protected $label;

}
