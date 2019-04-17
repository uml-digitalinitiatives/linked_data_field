Drupal.autocomplete.options.select =  function selectHandler(event, ui) {
  event.target.parentNode.parentNode.getElementsByClassName('subject-url-input')[0].value = ui.item.value;
  event.target.value = ui.item.label;
  jQuery(event.target).trigger('autocomplete-select');
  // Return false to tell jQuery UI that we've filled in the value already.
  return false;
}