/*
 * Petim Web Tools v1.0
 * Copyright(c) 2008, Petim Pty. Ltd.
 * licensing@petim.com.au
 * 
 * See packaged license.txt
 * OR URL:
 * http://www.petim.com.au/products/pwt/license/view/
 * for full license terms.
 */

/**
 * @class $PWT.com.Request 
 * //Description...
 * 
 * 
 * @namespace $PWT.com
 * @name Request
 * @interfaces $PWT.iface.Configurable,$PWT.iface.Observable
 * @traits $PWT.trait.Configurable,$PWT.trait.Timeable
 * 
 * @cfg {String} url The address which the request will be performed on.
 * @cfg {String} method The method in which this request will be performed.
 * Valid options are POST, GET, PUT, DELETE, HEAD, OPTIONS. (defaults to POST)
 * @cfg {String, Object} [parameters] Parameters of the request. Either a valid
 * URL Encoded string or an object with matching key value pairs.
 * @cfg {Boolean} async Runs the request in ... (defaults to true)
 * @cfg {Boolean} xDomain Currently not supported. (defaults to false)
 * @cfg {Number} timeout The number of seconds to run the request before the
 * onTimeout event is fired. (defaults to 300 seconds)
 * @cfg {Boolean} evalScript True to eval scripts from the result of a request. (defaults to false)
 * @cfg {Boolean} evalJSON True to eval the result of a request as JSON. (defaults to true)
 * @cfg {Boolean} evalQueryString True to eval the result of a request as a
 * URL encoded query string. (defaults to false)
 * @cfg {Object} headers A set of valid key value pairs representing headers that will be
 * sent with the request.
 * 
 * @author Timothy Chandler tim@petim.com.au
 */
$PWT.Class.create
(
	{
		$namespace:		'$PWT.com',
		$name:			'Request',
		$traits:		[$PWT.trait.Configurable,$PWT.trait.Timeable]
	}
)
(
	{
		events:
		{
			/**
			 * Callback fires if STATUS==200.
			 * @member $PWT.com.Request
			 * @return {Mixed}
			 */
			onSuccess:			true,
			/**
			 * Callback fires if STATUS!=200.
			 * @member $PWT.com.Request
			 * @return {Mixed}
			 */
			onFailure:			true,
			/**
			 * Callback fired whenever data is received or the request progresses.
			 * @member $PWT.com.Request
			 * @return {Mixed}
			 */
			onProgress:			true,
			/**
			 * Callback fired when request completes.
			 * @member $PWT.com.Request
			 * @return {Mixed}
			 */
			onComplete:			true,
			/**
			 * Callback fired when a request fails due to a fatal or io error.
			 * @member $PWT.com.Request
			 * @return {Mixed}
			 */
			onError:			true,
			/**
			 * Callback fired when a request is aborted by the user.
			 * @member $PWT.com.Request
			 * @return {Mixed}
			 */
			onAbort:			true,
			/**
			 * Callback fired when request times out.
			 * @member $PWT.com.Request
			 * @return {Mixed}
			 */
			onTimeout:			true,
			/**
			 * Callback fired when a successful request
			 * attempts to evaluate the response and fails.
			 * @member $PWT.com.Request
			 * @return {Mixed}
			 */
			onEvalError:		true
		},
		/**
		 * @property {Object}
		 * @member $PWT.com.Request
		 */
		config:
		{
			url:				'',
			method:				'POST',
			parameters:			null,
			async:				true,
			xDomain:			false,//TODO: xDomain
			timeout:			300,
			evalScript:			false,
			evalJSON:			true,
			evalQueryString:	false,
			headers:
			{
				'X-Requested-With':		'XMLHttpRequest',
				'X-PWT-Version':		$PWT.version,
				'Accept':				'application/json, text/javascript, text/html, application/xml, text/xml, */*',
				'Content-Type':			'application/x-www-form-urlencoded; charset=UTF-8',
				'Cache-Control':		'no-cache'
			}
		},
		/**
		 * @private
		 * @property {XMLHttpRequest}
		 * @member $PWT.com.Request
		 */
		xhr:					null,
		/**
		 * @private
		 * @property {Object}
		 * @member $PWT.com.Request
		 */
		response:
		{
			responseText:			null,
			responseXML:			null,
			responseJSON:			null,
			responseQueryString:	null
		},
		/**
		 * @member $PWT.com.Request
		 * @return {$PWT.com.Request}
		 * @constructor
		 */
		init: function()
		{
			this.observe
			(
				'onTimeout',
				function()
				{
					this.stop();
				}.bind(this)
			);
			this.xhr=new XMLHttpRequest();
			if (Object.isAssocArray(this.config.parameters))
			{
				this.config.parameters=Object.toQueryString(this.config.parameters);
			}
			else
			{
				this.config.parameters='';
			}
			if (this.config.method==$PWT.com.Request.METHOD.GET)
			{
				if (this.config.parameters!='')
				{
					if (!/\?/.test(this.config.url))this.config.url+='?';
					this.config.url+=this.config.parameters;
				}
				this.xhr.open(this.config.method,this.config.url,this.config.async);
			}
			else
			{
				this.xhr.open(this.config.method,this.config.url,this.config.async);
			}
			for (header in this.config.headers)
			{
				this.xhr.setRequestHeader(header,this.config.headers[header]);
			}
			this.xhr.onreadystatechange=this.onReadyStateChange.bind(this);
			this.xhr.onabort=function(event)
			{
				this.stopTimer();
				this.fireEvent('onAbort',event,this);
			}.bind(this);
			this.xhr.onerror=function(event)
			{
				this.stopTimer();
				this.fireEvent('onFailure',this.xhr);
				this.fireEvent('onError',event,this);
			}.bind(this);
			this.startTimer();
			this.xhr.send(this.config.parameters);
		},
		/**
		 * @private
		 * @method
		 * @member $PWT.com.Request
		 */
		onReadyStateChange: function(xhrEvent)
		{
			switch (this.xhr.readyState)
			{
				case $PWT.com.Request.READYSTATE.UNINITIALIZED:
				case $PWT.com.Request.READYSTATE.LOADING:
				case $PWT.com.Request.READYSTATE.LOADED:
				case $PWT.com.Request.READYSTATE.INTERACTIVE:
				{
					this.fireEvent('onProgress',this,xhrEvent);
					break;
				}
				case $PWT.com.Request.READYSTATE.COMPLETE:
				{
					this.stopTimer();
					this.fireEvent('onComplete',this,xhrEvent);
					if (this.xhr.status==$PWT.com.Request.STATUS.OK)
					{
						this.response=
						{
							responseText:	this.xhr.responseText,
							responseXML:	this.xhr.responseXML
						}
						if (this.xhr.getResponseHeader('Content-Type')=='application/json' && this.config.evalJSON)
						{
							//TODO: Safer JSON Evaling.
							eval('this.response.responseJSON='+this.xhr.responseText+';');
						}
						if (this.config.evalQueryString)
						{
							this.response.responseQueryString=this.xhr.responseText.toQueryParams();
						}
						this.fireEvent('onSuccess',this.response,xhrEvent);
					}
					else
					{
						this.fireEvent('onFailure',this.xhr);
					}
					break;
				}
			}
			return;
		},
		/**
		 * Stops the request.
		 * 
		 * @method
		 * @member $PWT.com.Request
		 * @return {XMLHttpRequest} The XMLHttpRequest instance.
		 */
		stop: function()
		{
			this.xhr.abort();
			this.stopTimer();
			return this.xhr;
		}
	}
);
Object.extend
(
	$PWT.com.Request,
	{
		/**
		 * @const
		 * @member $PWT.com.Request
		 */
		READYSTATE:
		{
			UNINITIALIZED:		0,
			LOADING:			1,
			LOADED:				2,
			INTERACTIVE:		3,
			COMPLETE:			4
		},
		STATUS:
		{
			UNINITILIZED:		0,
			OK:					200,
			NOT_FOUND:			404,
			SERVER_ERROR:		500
		},
		METHOD:
		{
			POST:		'POST',
			GET:		'GET',
			PUT:		'PUT',
			DELETE:		'DELETE',
			HEAD:		'HEAD',
			OPTIONS:	'OPTIONS'
		}
	}
);