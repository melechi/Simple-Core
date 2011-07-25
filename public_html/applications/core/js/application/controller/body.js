$PWT.Class.create
(
	{
		$namespace:	'app.controller',
		$name:		'body',
		$extends:	app.controller.Container
	}
)
(
	{
		initController: function()
		{
			var container=this.getView().getContainer();
			container.on
			(
				'render',
				function()
				{
					this.connectView('home');
					this.connectView('world');
				}.bind(this)
			);
			this.getModel().getView('structure').getItem('body').add(container);
			this.getModel().redraw();
		}
	}
);