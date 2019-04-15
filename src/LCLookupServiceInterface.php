<?php

namespace Drupal\lc_subject_field;

/**
 * Interface LCLookupServiceInterface.
 */
interface LCLookupServiceInterface {
  public function getSuggestions($candidate);
}
