<?php

namespace Drupal\linked_data_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'grid_widget' widget.
 *
 * @FieldWidget(
 *   id = "linked_data_widget",
 *   label = @Translation("Linked Data Lookup Widget"),
 *   field_types = {
 *     "linked_data_field"
 *   }
 * )
 */
class LinkedDataWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $source = $items->getSetting('source');

    $element['value'] = $element + [
        '#type' => 'textfield',
        '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
        '#description' => $this->t('Autocomplete field'),
        '#maxlength' => 200,
        '#prefix' => '<div class="field__label">' . $items->getFieldDefinition()->label() . '</div>',
        '#autocomplete_route_name' => 'linked_data_lookup.autocomplete',
        '#autocomplete_route_parameters' => ['linked_data_endpoint' => $source],
        '#size' => 200,
        '#ajax' => [
          'event' => 'autocomplete-close',
        ],
      ];
    unset($element['value']['#title']);
    $form['#attached']['library'][] = 'linked_data_field/ld-autocomplete';

    $element['url'] = [
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->url) ? $items[$delta]->url : NULL,
      '#delta' => $delta,
      '#size' => 200,
      '#prefix' => '<div class="field__label">Definition URL</div>',
      '#weight' => $element['#weight'],
      '#maxlength' => 200,
    ];

    $element['url']['#attributes']['class'][] = 'subject-url-input';

    return $element;
  }

}
