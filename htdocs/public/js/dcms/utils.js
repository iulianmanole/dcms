
dojo.provide('dcms.utils');

//connections array that will keep all created connections
var connections = [];
var connectionsHandlers = [];


dcms.utils.test = function()
{
 //alert("In TestAction");
 //dojo.place("<div id='aaa'>new node # </div>", "rack_add"); // before/after
}

dcms.utils.xhrReq = function(opt)
{
	//alert(dojo.toJson(this.opt));
	//opt = this.opt;
	//alert("Now,");
	//alert(dojo.toJson(opt)); 
	 
	 //XHR request 
	dojo.xhrPost({
		url: 	opt.url, 
		handleAs:"text", 
		form:	opt.form,  
		load: 	function(response, ioArgs) {
				
					//trims whitespaces from both sides of the string
					//response html must start with '<' or will fail the replace.
					response = dojo.trim(response);
					
					//replace toModify element with the response received from server
					dojo.place(response,opt.toModify,'replace');

					//because dojo.place replaces the opt.toModify element, 
					//All the events observers created previously for this DOM element or childs are destroyed.
					//All observers  must be reregistered.  
					
					//iterate over created connections and reregister the events observers
					//@todo: optimize the registration to only occur for 
					for (key in connections) {
						//alert("Recreate the following binding.");
						//alert("OPT"+dojo.toJson(opt));
						//alert(dojo.toJson(connections[key]));

						//we disconnect the observer first.
						//alert("Disconnect Key:"+key);
						dojo.disconnect(connectionsHandlers[key]);
						//recreate the observer;
						//key is set because we 
						//noConnectionUpdate is true because it already exists in connections array
						//alert("Recreate Handler:"+key);
						dcms.utils.modifyElem(connections[key],true, key);
					
					}
					
					return response;
				},
		error: 	function(response, ioArgs) {
					alert("Unable to perform XHR.HTTP status code is"+ioArgs.xhr.status);
					
					
					
				}
							
		});		 
}
/*
 * This function registers some events observers.  
 * When "opt.action" is observed to actionElement an XHR will be fired to 
 * 	update the "toModify" Element.
 * The XHR response(that is a html block) will be used to replace the current content.
 *   
 *  @param opt 				- list of options for element modification tracking. 
 *  	opt.actionElem 		- the elem that is observed
 *  	opt.action 			- the action observed for the elem
 *  	opt.toModify 		- the elem that will be modified	
 *  	opt.url 			- the new elem value will be obtain from  
 *  @param noConnectionsUpdate - if true, the "OPT" will not be saved in connections array
*/

dcms.utils.modifyElem = function(opt, noConnectionsUpdate, connectionsKey)  
{	
	//alert("OPT:"+dojo.toJson(opt));
	//alert(noConnectionsUpdate);
	//alert(dojo.version);
	var pushHandler = false;
	var replaceHandler = false;	
	if (typeof(connectionsKey) == 'undefined' || connectionsKey === null) {
		//connectionsKey is not set.
		pushHandler = true;
	}
	else {
		//connectionsKey is set
		replaceHandler = true;
		
	}
	/* Connect an watcher to the actionElem, that will see when opt.action happens.
	 * When the action will happens, the function 'msg' will be run.
	 * !Immportant: by using dojo.hitch we will run the 'msg' function in current(this) scope. 
	 *				xhrReq will have access to opt variable.
	 *
	 *  The Handler will be push / replaced in connectionsHandlers and will be used to 
	 *  disconnect eventlisteners. 
	 */	
	handler = dojo.connect(dojo.byId(opt.actionElem), opt.action, dojo.hitch(this,'xhrReq',opt));
	
	//We keep all the opt objects in the connections array 
	if (noConnectionsUpdate != true) {
		//add a new observer options in the connections array 
		connections.push(opt);
	}
	
	if(pushHandler == true) {
		//we push to connectionsHandlers
		connectionsHandlers.push(handler);
	}
	if (replaceHandler == true) {
		//we will replace the connectionsHandlers[connectionsKey] with the new handler
		connectionsHandlers[connectionsKey] = handler;
	}
	
	//alert(dojo.toJson(connections));
	//alert(dojo.toJson(connectionsHandlers));
	//console.log(connectionsHandlers);
}





