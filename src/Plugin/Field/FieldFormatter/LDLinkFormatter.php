<?php


namespace Drupal\linked_data_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Render\Markup;
use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'lcsubject_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "ld_link_formatter",
 *   label = @Translation("Prints a Linked Data field as a link to the definition URL."),
 *   field_types = {
 *     "linked_data_field",
 *     "lcsubject_field",
 *     "grid_id_field",
 *     "crossref_funder_field"
 *   }
 * )
 */
class LDLinkFormatter extends FormatterBase{

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        // Implement default settings.
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
        // Implement settings form.
      ] + parent::settingsForm($form, $form_state);
  }


  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      try {
        $url = Url::fromUri($item->url);
        $elements[$delta] = [
          '#title' => Markup::create($this->viewValue($item)),
          '#type' => 'link',
          '#url' => $url,
        ];
      }
      catch (\InvalidArgumentException $e) {
        $elements[$delta] = ['#markup' => $this->viewValue($item)];
      }
    }

    return $elements;
  }


  /**
   * Generate the output appropriate for one field item.
   *
   * @param Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return nl2br(Html::escape($item->value));
  }

}
