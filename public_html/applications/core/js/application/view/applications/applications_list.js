$PWT.Class.create
(
	{
		$namespace:	'app.view',
		$name:		'applications_list',
		$extends:	app.view.Container
	}
)
(
	{
//		__defaultController:	'app.controller.Card',
		controller:				'applications_list',
		store:					null,
		initView:				function()
		{
			this.store=new Ext.data.DirectStore
			(
				{
					root:		'data',
					fields:		['name','description','version','version_core_min','dependencies_applications','dependencies_components'],
					directFn:	this.getModel().API.application.main.read,
					autoLoad:	true
				}
			);
			this.addItem
			(
				'grid',
				{
					xtype:				'grid',
					title:				'List of Applications',
					store:				this.store,
					height:				490,
					border:				false,
					stripeRows:			true,
					sortCombo:			true,
					autoScroll:			true,
					columns:
					[
						{
							dataIndex:	'name',
							header:		'Application Name',
							width:		120,
							sortable:	true
						},
						{
							dataIndex:	'description',
							header:		'Application Description',
							width:		150,
							sortable:	false
						},
						{
							dataIndex:	'version',
							header:		'Application Version',
							width:		100,
							sortable:	true
						},
						{
							dataIndex:	'version_core_min',
							header:		'Minimum Core Version',
							width:		100,
							sortable:	true
						},
						{
							dataIndex:	'dependencies_applications',
							header:		'Application Dependencies',
							width:		100,
							sortable:	true
						},
						{
							dataIndex:	'dependencies_components',
							header:		'Component Dependencies',
							width:		100,
							sortable:	true,
							renderer:	function(value)
							{
								var html='<ul>';
								for (var i=0,j=value.length; i<j; i++)
								{
									html+='<li><a href="#" class="">'+value[i]+'</a></li>';
								}
								return html+'</ul>';
							}
						}
					],
					viewConfig:
					{
						forceFit:	true,
						emptyText:	'No application to display!'
					}
				}
			);
			
			
//			this.addItem
//			(
//				'test',
//				{
//					title:	'Foo',
//					html:	'<b>Bar</b>'
//				}
//			);
			
//			this.getModel().getView('navigation').getItem('applications').add(this.getContainer());
//			this.getModel().redraw();
//			this.setActiveItem('test');
		},
		getStore: function()
		{
			return this.store;
		}
	}
);