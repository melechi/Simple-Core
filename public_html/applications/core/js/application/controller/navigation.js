$PWT.Class.create
(
	{
		$namespace:	'app.controller',
		$name:		'navigation',
		$extends:	app.controller.Container
	}
)
(
	{
		initController: function()
		{
			this.getView().getItem('dashboard')		.on('render',this.connectView.bind(this,'dashboard'));
			this.getView().getItem('applications')	.on('render',this.connectView.bind(this,'applications'));
			this.getView().getItem('components')	.on('render',this.connectView.bind(this,'components'));
			this.getView().setActiveItem('applications');
//			this.getView().setActiveItem('components');
		}
	}
);