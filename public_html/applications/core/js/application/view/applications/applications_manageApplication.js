$PWT.Class.create
(
	{
		$namespace:	'app.view',
		$name:		'applications_manageApplication',
		$extends:	app.view.BorderLayoutPanel
	}
)
(
	{
		controller:	'applications_manageApplication',
//		__defaultController:	'app.controller.Card',
//		controller:	true,
		containerConfig:
		{
			title:	'Manage Application'
		},
		initView:	function()
		{
			this.observe
			(
				'onActivate',
				function()
				{
					this.$parent.getController().addBreadcrumb('Manage Application','manageApplication');
				}.bind(this)
			);
			this.addItem
			(
				'navigation',
				{
					title:		'Navigation',
					region:		'west',
					layout:		'accordion',
					split:		true,
					width:		200,
					minSize:	100,
					maxSize:	300,
					collapsible:true
				},
				'page',
				{
					title:	'&nbsp;',
					region:	'center'
				}
			);
		}
	}
);