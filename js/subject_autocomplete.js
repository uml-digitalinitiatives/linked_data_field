Drupal.autocomplete.options.select =  function selectHandler(event, ui) {
  var urlFieldName = event.target.name.replace("[value]", "[url]");
  var urlField = document.getElementsByName(urlFieldName)[0];
  if (urlField !== undefined) {
    urlField.value = ui.item.value;
    event.target.value = ui.item.label;
    // Hack. Without this the form elements added via AJAX don't get saved.
    event.target.setAttribute('value', ui.item.label);
    jQuery(event.target).trigger('autocomplete-select');

    // Return false to tell jQuery UI that we've filled in the value already.
    return false;
  }
}
