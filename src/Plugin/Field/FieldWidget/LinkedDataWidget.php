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

    $element['value'] = $element + [
        '#type' => 'textfield',
        '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
        '#description' => $this->t('Subject field'),
        '#maxlength' => 200,
        '#prefix' => '<div class="field__label">Linked Data Field Label</div>',
        '#autocomplete_route_name' => 'grid_field.autocomplete',
        '#autocomplete_route_parameters' => ['candidate' => 'linked_data_field'],
        '#size' => 200,
        '#ajax' => [
          'event' => 'autocomplete-close',
        ],
      ];

    $form['#attached']['library'][] = 'linked_data_field/ld-autocomplete';

    $element['url'] = [
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->url) ? $items[$delta]->url : NULL,
      '#delta' => $delta,
      '#size' => 200,
      '#prefix' => '<div class="field__label">LD Field Label</div>',
      '#weight' => $element['#weight'],
      '#maxlength' => 200,
    ];

    $element['url']['#attributes']['class'][] = 'subject-url-input';

    return $element;
  }

}
