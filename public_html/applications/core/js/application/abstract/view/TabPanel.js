$PWT.Class.create
(
	{
		$namespace:	'app.view',
		$name:		'TabPanel',
		$extends:	app.view.Container
	}
)
(
	{
		xtype:					'tabpanel',
		containerConfig:
		{
			layoutOnTabChange:	true,
			bodyStyle:			'padding: 5px;',
			border:				false,
			buttonAlign:		'right'
		},
		setActiveItem: function(itemName)
		{
			this.getContainer().setActiveTab(this.getItem(itemName));
		}
	}
);