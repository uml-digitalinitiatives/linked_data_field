<?php

namespace Drupal\lc_subject_field\Controller;

use Drupal\Component\Utility\Tags;
use Drupal\Core\Controller\ControllerBase;
use Drupal\lc_subject_field\LCLookupServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AutocompleteController.
 */
class AutocompleteController extends ControllerBase {

  /**
   * Service to do LC Subject lookups.
   *
   * @var Drupal\lc_subject_field\LCLookupServiceInterface
   */
  protected $lcLookup;

  /**
   * AutocompleteController constructor.
   *
   * @param Drupal\lc_subject_field\LCLookupServiceInterface $lc_lookup_service
   *   The lookup service to query against.
   */
  public function __construct(LCLookupServiceInterface $lc_lookup_service) {
    $this->lcLookup = $lc_lookup_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('lc_subject_field.lc_lookup')
    );
  }

  /**
   * Hello.
   *
   * @param Request $request
   *   The page request object
   * @param candidate
   * @return string
   *   Return Hello string.
   */
  public function handleAutocomplete(Request $request, $candidate) {
    $results = [];

    if ($input = $request->query->get('q')) {
      $typed_string = Tags::explode($input);
      $typed_string = mb_strtolower(array_pop($typed_string));
      $service_results = $this->lcLookup->getSuggestions($typed_string);
      foreach ($service_results as $subject => $url) {
        $results[] = ['value' => $url, 'label' => $subject];
      }
    }
      return new JsonResponse($results);

  }

}
