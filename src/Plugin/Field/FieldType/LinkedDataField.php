<?php


namespace Drupal\linked_data_field\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;


/**
 * Plugin implementation of the 'linked_data_field' field type.
 *
 * @FieldType(
 *   id = "linked_data_field",
 *   label = @Translation("Linked Data Lookup Field"),
 *   description = @Translation("Field for storing  fields with remote lookup sources"),
 *   default_widget = "linked_data_widget",
 *   default_formatter = "ld_link_formatter"
 * )
 */
class LinkedDataField extends FieldItemBase {


  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Text value'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(TRUE);

    $properties['url'] = DataDefinition::create('string')
      ->setLabel('Definition URL')
      ->setRequired(FALSE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'value' => [
          'type' => 'varchar',
          'length' => 200,
          'binary' => FALSE,
        ],
        'url' => [
          'type' => 'varchar',
          'length' => 100,
          'binary' => FALSE,
        ],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraints = parent::getConstraints();

    if ($max_length = 200) {
      $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
      $constraints[] = $constraint_manager->create('ComplexData', [
        'value' => [
          'Length' => [
            'max' => $max_length,
            'maxMessage' => t('%name: may not be longer than @max characters.', [
              '%name' => $this->getFieldDefinition()->getLabel(),
              '@max' => $max_length,
            ]),
          ],
        ],
      ]);
    }

    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $random = new Random();
    $values['value'] = $random->word(mt_rand(1, 200));
    return $values;
  }

  /**
   * Get property that this field considers it's 'main' value.
   *
   * @return string
   *   The value field name.
   */
  public static function mainPropertyName() {
    return 'value';
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  public static function defaultFieldSettings() {
    return [
        // Declare a single setting, 'size', with a default
        // value of 'large'
        'source' => 'lc_subject_field',
      ] + parent::defaultFieldSettings();
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = [];
    // The key of the element should be the setting name
    $settings = $this->getSettings();

    $e = array_keys(\Drupal::entityQuery('linked_data_endpoint')->execute());

    $entities = \Drupal::entityTypeManager()->getStorage('linked_data_endpoint')->loadMultiple($e);
    $options = [];
    foreach ($entities as $entity_id => $entity) {
      $options[$entity_id] = $entity->label();
    }
    $element['source'] = [
      '#title' => $this->t('Data source'),
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $settings['source'],
    ];

    return $element;
  }
}
