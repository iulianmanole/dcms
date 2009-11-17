

Event.observe(window, 'load', function() {
  Event.observe('form', 'submit', addLocation);
});

function addLocation(){
     var url = 'location.php';
     var pars = 'new-location='+escape($F('new-location'));
     var target = 'location-div';
     var myAjax = new Ajax.Updater(target, url, {method: 'get', parameters: pars});
}