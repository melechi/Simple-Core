$PWT.Class.create
(
	{
		$namespace:	'app.view',
		$name:		'components',
		$extends:	app.view.Container
	}
)
(
	{
		controller:	'components',
		store:		null,
		initView:	function()
		{
			this.store=new Ext.data.DirectStore
			(
				{
					root:		'data',
					fields:		['reference','name','description','version','version_core_min','dependencies_components'],
					directFn:	this.getModel().API.component.main.read
				}
			);
			window.$store=this.store;
			this.addItem
			(
				'grid',
				{
					xtype:				'grid',
					title:				'List of Components',
					store:				this.store,
					height:				490,
					border:				false,
					stripeRows:			true,
					sortCombo:			true,
					autoScroll:			true,
					columns:
					[
						{
							dataIndex:	'reference',
							header:		'Component Reference',
							width:		120,
							sortable:	true,
							hidden:		true
						},
						{
							dataIndex:	'name',
							header:		'Component Name',
							width:		120,
							sortable:	true
						},
						{
							dataIndex:	'description',
							header:		'Component Description',
							width:		150,
							sortable:	false
						},
						{
							dataIndex:	'version',
							header:		'Component Version',
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
							dataIndex:	'dependencies_components',
							header:		'Component Dependencies',
							width:		100,
							sortable:	false,
							renderer:	function(value)
							{
								var html='<ul>';
								for (var i=0,j=value.length; i<j; i++)
								{
									var	exists=(this.store.findExact('reference',value[i])!==-1)?true:false;
									html+='<li><a href="javascript:{};" class="component '+(exists?'exists':'nonexists')+'">'+value[i]+'</a></li>';
								}
								return html+'</ul>';
							}.bind(this)
						}
					],
					viewConfig:
					{
						forceFit:	true,
						emptyText:	'No component to display!'
					},
					tbar:
					[
						{
							text:		'Do Test',
							handler:	this.getModel().API.component.main.read
						}
					]
				}
			);
			this.getModel().getView('navigation').getItem('components').add(this.getContainer())
			this.getModel().redraw();
		},
		getStore: function()
		{
			return this.store;
		}
	}
);