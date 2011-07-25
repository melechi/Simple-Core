$PWT.Class.create
(
	{
		$namespace:	'app.controller',
		$name:		'applications',
		$extends:	app.controller.CardPanel,
		$traits:	app.trait.Breadcrumbs
	}
)
(
	{
		viewFolder:		'applications',
		breadcrumbs:	[{text:'Home',link:'list'}],
		initController: function()
		{
			this.getView().getContainer().on
			(
				'render',
				function()
				{
					this.connectView
					(
						['list','create','manageApplication'],
						function()
						{
							this.setActiveItem('list');
							this.updateBreadcrumbs();
						}.bind(this)
					);
				}.bind(this)
			);
			this.getModel().getView('navigation').getItem('applications').add(this.getView().getContainer());
			this.getModel().redraw();
		}
	}
);