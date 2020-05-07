<?php

namespace Drupal\linked_data_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'grid_widget' widget.
 *
 * @FieldWidget(
 *   id = "grid_widget",
 *   label = @Translation("GRID Identifier widget type"),
 *   field_types = {
 *     "grid_field"
 *   }
 * )
 */
class GridFieldWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element['value'] = $element + [
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
      '#description' => $this->t('Subject field'),
      '#maxlength' => 200,
      '#prefix' => '<div class="field__label">Name of institution</div>',
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
      '#prefix' => '<div class="field__label">GRID Identifier</div>',
      '#weight' => $element['#weight'],
      '#maxlength' => 200,
    ];

    $element['url']['#attributes']['class'][] = 'subject-url-input';

    return $element;
  }

}
