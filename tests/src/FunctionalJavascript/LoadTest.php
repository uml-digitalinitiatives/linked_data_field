<?php

namespace Drupal\Tests\lc_subject_field\Functional;

use Drupal\Core\Url;
use Drupal\field_ui\Tests\FieldUiTestTrait;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Tests\lc_subject_field\FunctionalJavascript\LoginAdminTrait;


/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group lc_subject_field
 */
class LoadTest extends WebDriverTestBase {

  use LoginAdminTrait;
  use FieldUiTestTrait;

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
   * Tests that the home page loads with a 200 response.
   */
  public function testSubjectField() {
    $this->drupalGet('admin/structure/types/manage/article');
   // admin/structure/types/manage/article/fields/add-field
    static::fieldUIAddNewField('admin/structure/types/manage/article', 'subjects', 'Subjects', 'lcsubject_field', [
//      'settings[target_type]' => 'paragraph',
      'cardinality' => '-1',
    ], []);

    $this->assertSession()->pageTextContains('');

  }

}
