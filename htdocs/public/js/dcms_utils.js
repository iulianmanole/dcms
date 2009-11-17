
/**
 * Will modify an element based on an event. 
 * 
 * @param actionedElement is the element that will cause the action to execute. 
 * 					Typically, this will be a button, but is not restricted only to this. 
 *
 * @param eventName is the event on witch our actiondElement will start the action execution.
 * 					Typically, it will be an DOM Level 2 Events (http://www.w3.org/TR/DOM-Level-2-Events/events.html#Events-eventgroupings)
 * 					Important events are : load, click, select, change, submit, reset, focus, etc. 
 * 
 * @param handlerUrl is the url to witch the XHR will be sent
 * 
 * @param handlerTarget is the element that will be replaced based on the XHR Response.
 * 
 * @param handlerParams is the list of parameters, and their values that will be sent to the handlerUrl, using XHR
 * 					 
 */


var dcmsUtilsJS = { };
	
/**
 * modifies an element (Target element), based on the appearance of an event (eventName) on the 
 * the actionedElement.
 *  
 * 
 */	 
dcmsUtilsJS.modifyElement = function(actionedElement, eventName, handlerUrl, 
								handlerMethod, target, handlerParams ) {
		
		
	var handlerObj = {
					actionedElement	: actionedElement,
					url				: handlerUrl, 
					method			: handlerMethod, 
					params			: eval('(' +handlerParams+ ')' ), 
					target			: target };
								
	/**
	 * We bind the handlerObj to the handler function. 
	 * That means that in our handler function we will reference handlerObj as 'this'
	 */			
	Event.observe(actionedElement,eventName,this.handler.bindAsEventListener(handlerObj));
}
	
/** 
 * Prepares the parameters to be sent to Server with XHR
 * Performs the Ajax.Request 
 */	
dcmsUtilsJS.handler = function(event) {
	
	//alert('in handler function!'+this.target);
	
	/* retrieve params values that will be sent to the server */
	for (property in this.params)
	{
		this.params[property] = Form.Element.getValue(property);
		
	}
	
	//alert(Object.toJSON(this.params));		
	var myAjax = new Ajax.Request(this.url, {		
							method : this.method,
							//parameters : Object.toJSON(this.params),
							parameters : this.params,							
    					  	onSuccess  : dcmsUtilsJS.replace.bind(this)
				});

}
/** 
 * performs the replacement of the target Element with the value provided by the 
 * response to XHR.
 * It is runned as onSuccess callback
 */	
dcmsUtilsJS.replace = function(transport) {
	//alert ('in replace');
	//alert ('Transport:'+transport.responseText);
	//alert ('this:' + Object.toJSON(this));
	//alert ('exit replace');
	
	var response = transport.responseText || false;
	
	if ((this.target != 'null') && (response != false)) {
		$(this.target).replace(response);
	}
	else {
		alert ('Unable to perform update!');
	}
}
