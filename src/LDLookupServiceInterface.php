<?php

namespace Drupal\linked_data_field;

/**
 * Interface LDLookupServiceInterface.
 */
interface LDLookupServiceInterface {

  /**
   * Return suggestions from the lookup service API based on given input.
   *
   * @param string $candidate
   *   The input string to get suggestions based on.
   *
   * @return mixed
   *   Array of suggestions from the API.
   */
  public function getSuggestions($candidate);

}
