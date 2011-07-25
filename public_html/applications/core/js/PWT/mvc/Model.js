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

$PWT.Class.create
(
	{
		$namespace:		'$PWT.mvc',
		$name:			'Model',
		$traits:		[$PWT.trait.Configurable,$PWT.trait.Observable]
	}
)
(
	{
		config:
		{
			root:			null,
			shellView:		'shell',
			shellListeners:	{},
			path:
			{
				view:		null,
				controller:	null,
				script:		null,
				template:	null,
			}
		},
		events:
		{
			onBeforeInit:		true,
			onAfterInit:		true,
			onNewView:			true,
			onNewController:	true,
			onRunScript:		true
		},
		views:					{},
		controllers:			{},
		shell:					null,
		__defaultController:	'$PWT.mvc.Controller',
		init:			function(model)
		{
			this.fireEvent('onBeforeInit',this);
			if (this.config.shellView!=null)
			{
				$PWT.util.include
				(
					this.config.path.view+this.config.shellView+'.js',
					'js',
					function()
					{
						this.shell=new window[this.config.root].view[this.config.shellView](this.config.shellListeners,this);
						this.fireEvent('onAfterInit',this);
					}.bind(this)
				);
			}
			else
			{
				throw 'shellView not defined in system init call. Unable to display initial screen!';
			}
			return this;
		},
		newView: function(viewID,viewName,listeners)
		{
			var viewDir=this.config.path.view;
			if (!Object.isString(viewName))
			{
				viewDir+=viewName.viewDir+_;
				viewName=viewName.view;
			}
			var run=function()
			{
				this.views[viewID]=new window[this.config.root].view[viewName](listeners,this);
				this.fireEvent('onNewView',this,this.views[viewID]);
			}.bind(this)
			if (Object.isUndefined(window[this.config.root].view)
			|| Object.isUndefined(window[this.config.root].view[viewName]))
			{
				$PWT.util.include
				(
					viewDir+viewName+'.js',
					'js',
					run
				);
			}
			else
			{
				run();
			}
		},
		getView: function(viewID)
		{
			return this.views[viewID];
		},
		newController: function(controllerID,controllerName,view,listeners)
		{
			var run=function()
			{
				this.controllers[controllerID]=new window[this.config.root].controller[controllerName](listeners,this,view);
				this.fireEvent('onNewController',this,this.controllers[controllerID]);
			}.bind(this)
			if (Object.isUndefined(window[this.config.root].controller)
			|| Object.isUndefined(window[this.config.root].controller[controllerName]))
			{
				$PWT.util.include
				(
					this.config.path.controller+controllerName+'.js',
					'js',
					run
				);
			}
			else
			{
				run();
			}
		},
		newDefaultController: function(controllerID,controllerName,view,listeners)
		{
			var defaultController=this.__defaultController;
			if (Object.isDefined(view.__defaultController) && Object.isString(view.__defaultController))
			{
				defaultController=view.__defaultController;
			}
			var path		=defaultController.split('.'),
				controller	=window;
			while (path.length)
			{
				controller=controller[path.shift()];
				if (Object.isUndefined(controller))
				{
					throw new Error('Unable to initiate default controller "'+defaultController+'".');
					break;
				}
			}
			this.controllers[controllerID]=new controller(listeners,this,view);
			this.fireEvent('onNewController',this,this.controllers[controllerID]);
		},
		getController: function(controllerID)
		{
			return this.controllers[controllerID];
		},
		runScript: function(scriptName,callback)
		{
			var run=function()
			{
				var script=new window[this.config.root].script[scriptName]();
				this.fireEvent('onRunScript',this,script);
				if (Object.isFunction(callback))callback(script);
			}.bind(this)
			if (Object.isUndefined(window[this.config.root].script)
			|| Object.isUndefined(window[this.config.root].script[scriptName]))
			{
				$PWT.util.include
				(
					this.config.path.script+scriptName+'.js',
					'js',
					run
				);
			}
			else
			{
				run();
			}
		},
		getShell: function()
		{
			return this.shell;
		}
		//TODO: loadTemplate()
//		loadTemplate: function(template)
//		{
//			
//			var file=air.File.applicationDirectory.resolvePath(this.config.templatePath+template+'.tpl');
//			var stream=new air.FileStream();
//			stream.open(file,air.FileMode.READ);
//			var data=stream.readUTFBytes(stream.bytesAvailable);
//			stream.close();
//			return data;
//		}
	}
);