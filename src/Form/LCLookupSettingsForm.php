<?php

namespace Drupal\linked_data_field\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class LCLookupSettingsForm.
 */
class LCLookupSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'linked_data_field.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ld_lookup_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('linked_data_field.settings');
    $form['base_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Base URL'),
      '#description' => $this->t('Service endpoint URL'),
      '#default_value' => $config->get('base_url'),
    ];
    $form['grid_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Wikidata URL'),
      '#description' => $this->t('URL to wikidata SPARQL endpoint'),
      '#default_value' => $config->get('grid_url'),
    ];
    $form['funder_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Crossref URL'),
      '#description' => $this->t('URL to Crossref endpopint'),
      '#default_value' => $config->get('funder_url'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('linked_data_field.settings')
      ->set('base_url', $form_state->getValue('base_url'))
      ->set('grid_url', $form_state->getValue('grid_url'))
      ->set('funder_url', $form_state->getValue('funder_url'))
      ->save();
  }

}
