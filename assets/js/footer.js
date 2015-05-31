// Footer Scripts

$ = jQuery;

var thValues = [];

// Get the table heading values
$('#main table th').each(function(e) {
  thValues.push($(this).text());
});

// Loop through each row and add the data
// attribute to each cell
$('#main .odd, #main .even').each(function(e) {

  var cells = $(this).children("td");

  for (var i = cells.length - 1; i >= 0; i--) {
    $(cells[i]).attr("data-th-value", thValues[i]);
  };
});
