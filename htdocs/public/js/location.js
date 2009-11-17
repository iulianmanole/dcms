

Event.observe(window, 'load', function() {
 	Event.observe('addNewLocation', 'click', addNewLocation);
});


function addNewLocation(event) {
	
	/*Workflow:
		Varianta 1: 
			XHR pentru a updata baza de date. updatelocationlist returneaza true/false. 
			Daca updatelocationlist returneaza true, se adauga parametrul la select
		Varianta 2: 
			XHR pentru update baza de date. 
			updatelocationlist returneaza un nou select pentru Locatii, care il va inlocui pe 
			cel deja existent, folosing Element.replace	
	
	*/
	//Form.Element.disable('eqName');

	var url = '/inventory/equipment/updatelocationlist';
    var pars = 'newLocation'+escape($F('newLocation'));
    var target = 'locationList';
   	// var target = 'info';
    /** var myAjax = new Ajax.Updater(	target, 
    								url, 
    								{	method: 'post', 
    									parameters: pars 								
    								});
    */
    new Ajax.Request(url, 
    					 {		  method: 'post',  								
    							  onComplete : function(transport){
    							  		$('info').update('onSuccess function reached');
    							   		var response = transport.responseText || "no response text";
      									alert("Success! \n\n" + response);
      									$('locationList').replace(response);
    							  } 
    								
    							 });
    								
    								


}
