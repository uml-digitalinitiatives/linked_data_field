<?php

namespace Drupal\linked_data_field\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LinkedDataEndpointForm.
 */
class LinkedDataEndpointForm extends EntityForm {

  /**
   * @var \Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePluginManager
   */
  protected $endpointTypePluginManager;

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $options = $this->getEndpointPlugins();

    $linked_data_endpoint = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $linked_data_endpoint->label(),
      '#description' => $this->t("Label for the Linked Data Lookup Endpoint."),
      '#required' => TRUE,
    ];

    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Endpoint type'),
      '#description' => $this->t('The plugin to construct the query.'),
      '#options' => $options,
      '#default_value' => $linked_data_endpoint->get('type'),
      '#attributes' => [
        'name' => 'linked-data-plugin-type',
      ],
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $linked_data_endpoint->id(),
      '#machine_name' => [
        'exists' => '\Drupal\linked_data_field\Entity\LinkedDataEndpoint::load',
      ],
      '#disabled' => !$linked_data_endpoint->isNew(),
    ];

    $form['base_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Base URL'),
      '#maxlength' => 255,
      '#description' => $this->t("Base URL of the endpoint, including query if applicable."),
      '#default_value' => $linked_data_endpoint->get('base_url'),
      '#required' => TRUE,
    ];

    $form['label_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label JSON key'),
      '#maxlength' => '50',
      '#description' => $this->t('The JSON key that holds the human-readable label of the returned value. Can be a string or a numeric index.'),
      '#default_value' => $linked_data_endpoint->get('label_key'),
      '#required' => TRUE,
    ];

    $form['url_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL JSON key'),
      '#maxlength' => '50',
      '#description' => $this->t('The JSON key that holds the canonical item URL.'),
      '#default_value' => $linked_data_endpoint->get('url_key'),

    ];

    // Get third-party settings form items.
    foreach ($options as $plugin_name => $plugin_label) {
      $instance = $this->endpointTypePluginManager->createInstance($plugin_name, ['endpoint' => $linked_data_endpoint]);
      $third_party_settings = $linked_data_endpoint->getThirdPartySettings('linked_data_field');
      $form[$plugin_name] = [
        '#type' => 'fieldset',
        '#title' => $this->t("@label settings", ['@label' => $plugin_label]),
        '#collapsible' => TRUE,
        '#tree' => TRUE,
        '#states' => [
          'visible' => [
            ':input[name="linked-data-plugin-type"]' => ['value' => $plugin_name],
          ],
        ],
      ];
      $plugin_settings = $instance->getSettingsFormItems($form, $form_state, $third_party_settings);
      foreach($plugin_settings as $plugin_setting_name => $plugin_setting) {
        $form[$plugin_name][$plugin_setting_name] = $plugin_setting;
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $this->savePluginSettings($form_state);

    $status = $this->entity->save();


    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Linked Data Lookup Endpoint.', [
          '%label' => $this->entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Linked Data Lookup Endpoint.', [
          '%label' => $this->entity->label(),
        ]));
    }
    $form_state->setRedirectUrl($this->entity->toUrl('collection'));
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container, $container->get('plugin.manager.linked_data_endpoint_type_plugin'));
  }

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param \Drupal\linked_data_field\Plugin\LinkedDataEndpointTypePluginManager $endpoint_type
   */
  public function __construct(ContainerInterface $container, LinkedDataEndpointTypePluginManager $endpoint_type) {
    $this->endpointTypePluginManager = $endpoint_type;
  }

  /**
   * @return array
   */
  protected function getEndpointPlugins(): array {
    $plugins = $this->endpointTypePluginManager->getDefinitions();

    $options = [];

    foreach ($plugins as $plugin_id => $plugin) {
      $options[$plugin_id] = $plugin['label'];
    }
    return $options;
  }

  /**
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  protected function savePluginSettings(FormStateInterface $form_state): void {
    $linked_data_endpoint = $this->entity;
    $plugins = $this->getEndpointPlugins();
    $values = $form_state->getValues();
    foreach ($plugins as $plugin_name => $plugin_label) {
      foreach ($values[$plugin_name] as $plugin_setting_name => $plugin_setting_value) {
        $linked_data_endpoint->setThirdPartySetting('linked_data_field', $plugin_name . '-' . $plugin_setting_name, $plugin_setting_value);
      }
    }
  }

}
