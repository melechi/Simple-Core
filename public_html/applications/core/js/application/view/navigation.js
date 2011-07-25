$PWT.Class.create
(
	{
		$namespace:	'app.view',
		$name:		'navigation',
		$extends:	app.view.TabPanel
	}
)
(
	{
		controller:	'navigation',
		initView:	function()
		{
			this.addItem
			(
				'dashboard',
				{
					title:	'Dashboard'
				},
				'applications',
				{
					title:	'Applications'
				},
				'components',
				{
					title:	'Components'
				}
			);
			this.getModel().getShell().getLayout().add(this.getContainer());
			this.getModel().redraw();
		}
	}
);