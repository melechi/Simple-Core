$PWT.Class.create
(
	{
		$namespace:	'app',
		$name:		'Application',
		$extends:	$PWT.mvc.Model,
		$traits:
		[
			app.trait.Authentication,
			app.trait.Message,
			app.trait.Mask
		]
	}
)
(
	{
		API:			null,
		db:				null,
		authenticated:	false,
//		stage:			null,
		viewport:		null,
		dir:
		{
			storage:	null,
			cache:		null
		},
		init:			function()
		{
			this.init.$parent();
			
			if (!this.config.initMaskEl)
			{
				this.mask=new Ext.LoadMask(Ext.getBody(),{msg:this.config.initMessage});
				this.mask.show();
				this.maskEl=Ext.query('.x-mask-loading').first();
			}
			else
			{
				this.mask	=$(this.config.initMaskEl+'-back');
				this.maskEl	=$(this.config.initMaskEl+'-msg');
			}
			this.viewport=new Ext.Viewport({layout:'fit'});
			this.viewport.doLayout();
			
			
			
			//Initiate the Ext Direct server.
			$PWT.util.include
			(
				$COREROOT+'direct/API/?event=true',
				'js',
				function()
				{
					Ext.Direct.onProviderData=Ext.Direct.onProviderData.createInterceptor
					(
						function(provider,e)
						{
							if (Object.isDefined(e.xhr) && /(Fatal error|Parse error)/.test(e.xhr.responseText))
							{
								Ext.Msg.show
								(
									{
										title:	'Server Error',
										msg:	e.xhr.responseText,
										buttons:Ext.Msg.OK,
										icon:	Ext.Msg.ERROR
									}
								);
								throw new Error(e.xhr.responseText);
								return false;
							}
						},
						Ext.Direct
					);
					Ext.Direct.on
					(
						'console',
						function(event)
						{
							if ((Object.isArray(event.data.data) && event.data.data.length>20)
							|| (Object.isArray(event.data.data[0]) && event.data.data[0].length>20))
							{
								console[event.data.type].call(console,{data:event.data.data});
							}
							else
							{
								console[event.data.type].call(console,event.data.data);
							}
						}
					);
					
					Ext.Direct.on
					(
						'exception',
						function(exception)
						{
							Ext.Msg.show
							(
								{
									title:	'Server Exception',
									msg:	exception.message,
									buttons:Ext.Msg.OK,
									icon:	Ext.Msg.ERROR
								}
							);
							var message	=exception.message.match(/Message:\<\/b\> (.*)\<\/p\>/)[1],
								file	=exception.message.match(/File:\<\/b\> (.*)\<\/p\>/)[1],
								line	=exception.message.match(/Line:\<\/b\> (.*)\<\/p\>/)[1];
							throw new Error(message+' in File '+file+' on Line '+line);
						}
					);
					Ext.Direct.addProvider.apply(Ext.Direct,new this.API.Direct().API);
					this.hideMask();
				}.bind(this)
			);
			
			
			
			
			
//			this.newView('createCharacter','createCharacter');
			
			
			
//			var initialNetCheck=function(status)
//			{
//				this.netMonitor.unobserve('onChange',initialNetCheck);
//				delete initialNetCheck;
////				if (!$PAW.application.ApplicationVar.get('doneFirstRun'))
////				{
////					this.mainWindow.hideMask();
////					this.runScript('firstRunSetup');
////				}
////				else
////				{
////					this.mainWindow.hideMask();
////					this.newView('login','login');
////				}
////				this.mainWindow.hideMask();
//				this.newView('login','login');
//			}.bind(this)
////			this.mainWindow.showMask('Checking internet connectivity...');
//			this.netMonitor.observe('onChange',initialNetCheck);
			
			
		},
		exception: function()
		{
			
		},
		getViewport: function()
		{
			return this.viewport;
		},
		redraw: function(callback)
		{
			(function()
			{
				this.viewport.doLayout();
				if (Object.isFunction(callback))callback();
			}.bind(this)).defer(100);
			return this;
		},
	}
);
$PWT.onReady
(
	function()
	{
		window.application=new app.Application
		(
			{
				debug:			app.config.debug,
				root:			'app',
				title:			'Simple Core Control Panel',
				initMessage:	'Initializing the Simple Core Control Panel...',
				shellView:		'shell',
				shellListeners:	{},
				initMaskEl:		'pwt-mask',
				path:
				{
					view:		app.config.path.view,
					controller:	app.config.path.controller,
					script:		app.config.path.script,
					template:	app.config.path.template
				}
			}
		);
	}
);