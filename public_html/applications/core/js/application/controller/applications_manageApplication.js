$PWT.Class.create
(
	{
		$namespace:	'app.controller',
		$name:		'applications_manageApplication',
		$extends:	app.controller.Card
	}
)
(
	{
		activeApplication:	null,
		rawApplicationJSON:	null,
		modules:			{},
		sections:			{},
		rawMenus:			{},//{floating:false,items:[]},
//		initController:	function()
//		{
//			
//		},
		setActiveApplication: function(application)
		{window.am=this;
			this.activeApplication=application;
			this.$parent.changeBreadcrumb
			(
				'manageApplication',
				'Manage Application - <b>'+application+'</b>',
				'manageApplication'
			);
			window.$container=this.getView().getContainer();
			this.getView().getContainer().setTitle('Managing the "'+application+'" Application');
			this.getModel().redraw();
			this.getModel().API.application.main.initManagement
			(
				application,
				function(result)
				{
					this.rawApplicationJSON	=result.data;
					var modules				=this.rawApplicationJSON.modules;
					if ((Object.isArray(modules) && modules.length) && Object.isDefined(modules[0].title))
					{
						this.addModules(modules)
							.renderMenu()
							.getModel().redraw();
					}
					else
					{
						throw new Error('No modules were returned from server.');
					}
				}.bind(this)
			);
			
		},
		addModules: function(modules)
		{
			var thisModule=null;
			for (var i=0,j=modules.length; i<j; i++)
			{
				this.modules[modules[i].id]=
				{
					title:		modules[i].title,
					sections:	[],
					item:		null
				}
				thisModule=this.modules[modules[i].id];
				this.getView().getItem('navigation').add(thisModule.item=new Ext.Panel({title:thisModule.title}));
				if (modules[i].sections.length)
				{
					this.addSections(modules[i].id,modules[i].sections);
				}
			}
			return this;
		},
		addSections: function(moduleID,sections,menuItem)
		{
			if (Object.isUndefined(menuItem))
			{
				this.rawMenus[moduleID]	={floating:false,items:[]};
				menuItem				=this.rawMenus[moduleID].items;
			}
			else if (Object.isUndefined(menuItem.menu))
			{
				menuItem.menu	=[];
				menuItem		=menuItem.menu;
			}
			if (Object.isDefined(this.modules[moduleID]))
			{
				var	thisSection	=null;
				for (var i=0,j=sections.length; i<j; i++)
				{
					this.sections[sections[i].id]	=sections[i];
					thisSection						=this.sections[sections[i].id];
					menuItem.push({text:thisSection.title});
					if (sections[i].children.length)
					{
						this.addSections(moduleID,sections[i].children,menuItem.last());
					}
				}
			}
			else
			{
				throw new Error('Unable to add section. Module "'+moduleID+'" is not defined.');
			}
			return this;
		},
		renderMenu: function(moduleID)
		{
			for (moduleID in this.rawMenus)
			{
				this.modules[moduleID].item.add(new Ext.menu.Menu(this.rawMenus[moduleID]));
			}
			return this;
		}
	}
);