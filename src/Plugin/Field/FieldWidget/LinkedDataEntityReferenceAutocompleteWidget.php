<?php

namespace Drupal\linked_data_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'linked_data_entity_reference_autocomplete' widget.
 *
 * @FieldWidget(
 *   id = "linked_data_entity_reference_autocomplete",
 *   label = @Translation("Autocomplete from linked data source"),
 *   description = @Translation("An autocomplete text field. Include data from linked data source."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class LinkedDataEntityReferenceAutocompleteWidget extends EntityReferenceAutocompleteWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $form_element = parent::formElement($items, $delta, $element, $form, $form_state);
    $form_element['target_id']['#type'] = 'linked_data_entity_autocomplete';
    return $form_element;
  }

}
