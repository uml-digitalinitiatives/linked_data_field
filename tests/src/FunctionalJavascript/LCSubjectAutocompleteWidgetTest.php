<?php

namespace Drupal\Tests\lc_subject_field\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

/**
 * Test the subject autocomplete widget.
 *
 * @group lc_subject_field
 */
class LCSubjectAutocompleteWidgetTest extends WebDriverTestBase {

  use LoginAdminTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'node',
    'field',
    'field_ui',
    'link',
    'lc_subject_field'
  ];

  /**
   * A user with permission to administer site configuration.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  protected function setUp()
  {
    parent::setUp();
    $this->user = $this->loginAsAdmin([
      'administer content types',
      'administer node fields',
      'administer node form display',]);

    // Create an article content type that we will use for testing.
    $type = $this->container->get('entity_type.manager')->getStorage('node_type')
      ->create([
        'type' => 'article',
        'name' => 'Article',
      ]);
    $type->save();

    $this->container->get('router.builder')->rebuild();
  }

  /**
   * Tests that autocomplete retrieves suggestions and
   * that the URL field gets filled when an item is selected.
   */
  public function testSubjectFieldAutocomplete() {
    $this->drupalGet('admin/structure/types/manage/article');

    static::fieldUIAddNewField('admin/structure/types/manage/article', 'subjects', 'Subjects', 'lcsubject_field', [
      'cardinality' => '-1',
    ], []);

    // Change the base URL to not hit id.loc.gov.
    $this->config('lc_subject_field.settings')->set('base_url', 'http://test.test/')->save();
    $this->drupalGet('node/add/article');
    $page = $this->getSession()->getPage();

    $assert_session = $this->assertSession();
    $autocomplete_field = $assert_session->waitForElement('css', '[name="' . 'field_subject' . '[0][value]"].ui-autocomplete-input');
    $autocomplete_field->setValue('a');

    $this->getSession()->getDriver()->keyDown($autocomplete_field->getXpath(), 'a');
    $assert_session->waitOnAutocomplete();

    $results = $page->findAll('css', '.ui-autocomplete li');

    $this->assertCount(2, $results);
    $assert_session->pageTextContains('Apple');

    // Select an element and check the URL gets filled.
    $results[0]->click();
    $assert_session->fieldValueEquals('field_subject[0][url]', 'http://nasa.gov/');
  }

  /**
   * Creates a new field through the Field UI.
   * Borrowed from field_ui module tests.
   *
   * @param string $bundle_path
   *   Admin path of the bundle that the new field is to be attached to.
   * @param string $field_name
   *   The field name of the new field storage.
   * @param string $label
   *   (optional) The label of the new field. Defaults to a random string.
   * @param string $field_type
   *   (optional) The field type of the new field storage. Defaults to
   *   'test_field'.
   * @param array $storage_edit
   *   (optional) $edit parameter for drupalPostForm() on the second step
   *   ('Storage settings' form).
   * @param array $field_edit
   *   (optional) $edit parameter for drupalPostForm() on the third step ('Field
   *   settings' form).
   */
  public function fieldUIAddNewField($bundle_path, $field_name, $label = NULL, $field_type = 'test_field', array $storage_edit = [], array $field_edit = []) {
    $label = $label ?: $this->randomString();
    $initial_edit = [
      'new_storage_type' => $field_type,
      'label' => $label,
    ];

    // Allow the caller to set a NULL path in case they navigated to the right
    // page before calling this method.
    if ($bundle_path !== NULL) {
      $bundle_path = "$bundle_path/fields/add-field";
    }

    // First step: 'Add field' page.
    $this->drupalGet($bundle_path);


    $this->submitForm($initial_edit, "Save and continue");
    $initial_edit['field_name'] = 'subject';
    $this->submitForm($initial_edit, "Save and continue");

    //return $this->getSession()->getPage()->getContent();

    //$this->drupalPostForm($bundle_path, $initial_edit, t('Save and continue'));
    $this->assertRaw((string) t('These settings apply to the %label field everywhere it is used.', ['%label' => $label]));

    // Second step: 'Storage settings' form.
    $this->drupalPostForm(NULL, $storage_edit, t('Save field settings'));
    $this->assertRaw((string) t('Updated field %label field settings.', ['%label' => $label]), 'Redirected to field settings page.');

    // Third step: 'Field settings' form.
    $this->drupalPostForm(NULL, $field_edit, t('Save settings'));
    $this->assertRaw((string) t('Saved %label configuration.', ['%label' => $label]));

    // Check that the field appears in the overview form.
    $this->assertFieldByXPath('//table[@id="field-overview"]//tr/td[1]', $label);
  }

}
