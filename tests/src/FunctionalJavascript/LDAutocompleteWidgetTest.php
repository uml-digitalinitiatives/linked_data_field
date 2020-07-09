<?php

namespace Drupal\Tests\linked_data_field\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Test the Linked Data Lookup Field autocomplete widget.
 *
 * @group linked_data_field
 */
class LDAutocompleteWidgetTest extends WebDriverTestBase {

  /**
   * Default theme to use now taht 'classy' isn't assumed.
   *
   * @var string
   */
  protected $defaultTheme = 'stable';

  /**
   * Don't error out due to third-party settings.
   *
   * @var bool
   */
  protected $strictConfigSchema = FALSE;

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
    'linked_data_field',
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
  protected function setUp(): void {
    parent::setUp();

    $this->page = $this->getSession()->getPage();
    $this->webAssert = $this->assertSession();

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

    $endpoint = \Drupal::entityTypeManager()->getStorage('linked_data_endpoint')->create(
      ['id' => 'test', 'type' => 'lc_authority', 'label' => 'Test', 'base_url' => 'http://test.test/', 'label_key' => 0, 'url_key' => 1]
    )->save();

    // Add Linked Data lookup field
    $field_storage = FieldStorageConfig::create([
      'field_name' => 'test_subject_field',
      'entity_type' => 'node',
      'type' => 'linked_data_field',
      'cardinality' => -1,
    ]);
    $field_storage->save();
    $field = FieldConfig::create([
      'field_storage' => $field_storage,
      'label' => 'Test subject',
      'bundle' => 'article',
      'required' => FALSE,
      'settings' => [
        'source' => 'test',
      ]
    ]);
    $field->save();

    //Explicitly add the widget to the form mode.
    $form_display_manager = \Drupal::entityTypeManager()
      ->getStorage('entity_form_display');

    $ids = \Drupal::entityQuery('entity_form_display')->execute();

    $form_display_id = $form_display_manager
      ->create([
        'id' => 'node.article.default',
        'targetEntityType' => 'node',
        'bundle' => 'article',
        'mode' => 'default',
        'status' => TRUE,
      ])->save();
    $form_display = $form_display_manager->load('node.article.default');

    $form_display->setComponent('title', [
        'type' => 'string_textfield',
        'region' => 'content',
      ])
      ->setComponent('body', [
        'type' => 'text_textarea_with_summary',
        'region' => 'content',
      ])
      ->setComponent('field_test_subject', [
        'region' => 'content',
        'type' => 'linked_data_widget',
      ])->save();


    $this->drupalLogin($this->drupalCreateUser([
      'administer node display',
      'administer node form display',
      'administer node fields',
      'administer content types',
    ]));

    // Enable layout builder and overrides.
    $this->drupalGet(
      'admin/structure/types/manage/article/form-display');
    $this->click('.tabledrag-toggle-weight');

    $page = $this->getSession()->getPage();

    $assert_session = $this->assertSession();

    $field_display_item = $assert_session->waitForElement('css', '[name="fields[test_subject_field][region]"]');
    $field_display_item->setValue('content');
    $assert_session->assertWaitOnAjaxRequest(2000);
    $this->click('#edit-submit');
    $assert_session->assertWaitOnAjaxRequest(2000);

    $this->drupalLogout();



    $account = $this->drupalCreateUser([
      'administer nodes',
      'administer content types',
      'administer node fields',
      'create article content',
    ]);
    $this->drupalLogin($account);


    // Change the base URL to not hit id.loc.gov.
    $this->drupalGet('node/add/article');
    $page = $this->getSession()->getPage();

    $assert_session = $this->assertSession();
    $autocomplete_field = $assert_session->waitForElement('css', '[name="test_subject_field[0][value]"].ui-autocomplete-input');
    $autocomplete_field->setValue('a');

    $this->getSession()->getDriver()->keyDown($autocomplete_field->getXpath(), 'a');
    $assert_session->waitOnAutocomplete();

    $results = $page->findAll('css', '.ui-autocomplete li');

    $this->assertCount(2, $results);
    $assert_session->pageTextContains('Apple');

    // Select an element and check the URL gets filled.
    $results[0]->click();
    $assert_session->fieldValueEquals('test_subject_field[0][url]', 'http://nasa.gov/');
  }

}
