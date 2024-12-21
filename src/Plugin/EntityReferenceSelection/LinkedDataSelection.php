<?php

namespace Drupal\linked_data_field\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginBase;
use Drupal\Core\Entity\EntityReferenceSelection\SelectionWithAutocreateInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePluginManager;
use Drupal\user\EntityOwnerInterface;
use Drupal\views\Plugin\views\style\DefaultStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation for using Linked Data Lookup fields as taxonomy term selector.
 *
 * @EntityReferenceSelection(
 *   id = "linked_data",
 *   label = @Translation("Linked Data Lookup"),
 *   group = "linked_data",
 *   weight = 0,
 *   deriver = "Drupal\Core\Entity\Plugin\Derivative\DefaultSelectionDeriver"
 * )
 */
class LinkedDataSelection extends DefaultSelection implements ContainerFactoryPluginInterface, SelectionWithAutocreateInterface {


  /**
   * The plugin manager for endpoint types.
   *
   * @var \Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePluginManager
   */
  protected $ldEntityTypePluginManager;


  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity field manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Entity type bundle info service.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  public $entityTypeBundleInfo;

  /**
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a new DefaultSelection object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle info service.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, ModuleHandlerInterface $module_handler, AccountInterface $current_user, EntityFieldManagerInterface $entity_field_manager, EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL, EntityRepositoryInterface $entity_repository, $ld_entity_type_plugin_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager, $module_handler, $current_user, $entity_field_manager, $entity_type_bundle_info, $entity_repository);

    $this->entityTypeManager = $entity_type_manager;
    $this->moduleHandler = $module_handler;
    $this->currentUser = $current_user;
    $this->entityFieldManager = $entity_field_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->entityRepository = $entity_repository;
    $this->ldEntityTypePluginManager = $ld_entity_type_plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('module_handler'),
      $container->get('current_user'),
      $container->get('entity_field.manager'),
      $container->get('entity_type.bundle.info'),
      $container->get('entity.repository'),
      $container->get('plugin.manager.linked_data_endpoint_type_plugin')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

    $configuration = $this->getConfiguration();

    $bundles = $this->entityTypeBundleInfo->getBundleInfo('taxonomy_term');
    $ldbundles = [];
    $allbundles = [];
    foreach ($bundles as $bundle_key => $bundle) {
      $fields = $this->entityFieldManager->getFieldDefinitions('taxonomy_term', $bundle_key);
      foreach ($fields as $field) {
        if ($field->getType() == 'linked_data_field') {
          // Make the field name part of the label
          $ldbundles[$bundle_key] = $bundle['label'] . ' (' . $field->getLabel() . ')';
          break;
        }
      }
    }

    $form = parent::buildConfigurationForm($form, $form_state);

    $form['target_bundles']['#description'] = $this->t('Taxonomy terms that have a Linked Data Lookup field attached. If the list is empty, go to /admin/structure/taxonomy and click on a vocabulary, and then the Manage Fields tab to add one.');
    $form['target_bundles']['#options'] = $ldbundles;

    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function createNewEntity($entity_type_id, $bundle, $label, $uid) {
[$label, $uri] = explode(' -- ', $label);

    $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);

    $values = [
      $entity_type->getKey('label') => $label,
    ];

    if ($bundle_key = $entity_type->getKey('bundle')) {
      $values[$bundle_key] = $bundle;
    }

    $fields = $this->entityFieldManager->getFieldDefinitions($entity_type->id(), $bundle);
    foreach ($fields as $field) {
      if ($field->getType() == 'linked_data_field') {
        $values[$field->getName()] = [
          'value' => $label,
          'url' => $uri
        ];
        break;
      }
    }

    $entity = $this->entityTypeManager->getStorage($entity_type_id)->create($values);

    if ($entity instanceof EntityOwnerInterface) {
      $entity->setOwnerId($uid);
    }

    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getReferenceableEntities($match = NULL, $match_operator = 'STARTS WITH', $limit = 0) {
    // We ignore the default configuration because we can't control it.
    $match_operator = 'LIKE';
    $results = parent::getReferenceableEntities($match . '%', $match_operator, $limit);
    $vocab_name = !empty($this->configuration['auto_create_bundle'])
    ? $this->configuration['auto_create_bundle']
    : reset($this->configuration['target_bundles']);

    // Get the configuration object
    $fields = $this->entityFieldManager->getFieldDefinitions('taxonomy_term', $vocab_name);

    foreach($fields as $candidate_field) {
      if ($candidate_field->getType() == 'linked_data_field') {
        $field = $candidate_field;
        break;
      }
    }

    $lookup_field_definition = $fields[$field->getName()];
    $settings = $lookup_field_definition->getSettings();

    $endpoint = $this->entityTypeManager->getStorage('linked_data_endpoint')->load($settings['source']);
    $plugin = $this->ldEntityTypePluginManager->createInstance($endpoint->get('type'), ['endpoint' => $endpoint]);
    $service_results = $plugin->getSuggestions($match);
    if (!empty($service_results)) {
      foreach ($service_results as $result_label => $result_uri) {
        $results['other']['new_' . $result_label ] = $result_label . ' -- ' . $result_uri . ' -- ';
      }
    }

    return $results;
  }

  /**
   * {@inheritdoc}
   */
  public function countReferenceableEntities($match = NULL, $match_operator = 'CONTAINS') {
    return 0;
  }

  /**
   * {@inheritdoc}
   */
  public function validateReferenceableEntities(array $ids) {
    $result = parent::validateReferenceableEntities($ids);

    return $result;
  }


  /**
   * {@inheritdoc}
   */
  public function validateReferenceableNewEntities(array $entities) {
    return $entities;
  }

  public function submitConfigForm(&$form, $form_state) {
    $x = 1;
  }
}
