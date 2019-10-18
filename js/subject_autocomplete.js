Drupal.autocomplete.options.select =  function selectHandler(event, ui) {
  var subjectURLField = event.target.parentNode.parentNode.getElementsByClassName('subject-url-input')[0];
  if (subjectURLField !== undefined) {
    subjectURLField.value = ui.item.value;
    event.target.value = ui.item.label;
    // Hack. Without this the form elements added via AJAX don't get saved.
    event.target.setAttribute('value', ui.item.label);
    jQuery(event.target).trigger('autocomplete-select');

    // Return false to tell jQuery UI that we've filled in the value already.
    return false;
  }
}