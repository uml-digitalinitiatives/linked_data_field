Drupal.autocomplete.options.select =  function selectHandler(event, ui) {
  console.log('autocomplete funciton reached');
  var terms = Drupal.autocomplete.splitValues(event.target.value);
  // Remove the current input.
  // Add the selected item.
  event.target.parentNode.parentNode.getElementsByClassName('subject-url-input')[0].value = ui.item.value;
  event.target.value = ui.item.label;
  jQuery(event.target).trigger('autocomplete-select');
  // Return false to tell jQuery UI that we've filled in the value already.
  return false;
}