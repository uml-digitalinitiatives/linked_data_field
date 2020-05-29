Drupal.behaviors.ld_run_query = {
  attach: function (context, settings) {

    // Attach a click listener to the run query button.
    var runQueryButton = document.getElementsByName('runquery_button')[0];
    runQueryButton.addEventListener('click', function() {

      // Do something!
      console.log('Run query button clicked!');
      console.log(drupalSettings.linkedDataField);
      jQuery('#edit-results').load('/linked-data-lookup/' + drupalSettings.linkedDataField.runQuery.endPointName + '?q='
      + jQuery('#edit-candidate').val());
    }, false);

  }
};
