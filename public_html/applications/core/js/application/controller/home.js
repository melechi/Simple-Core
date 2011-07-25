$PWT.Class.create
(
	{
		$namespace:	'app.controller',
		$name:		'home',
		$extends:	app.controller.Container,
		$traits:	app.trait.Body
	}
)
(
	{
		initController: function()
		{
			this.getView().getItem().on('show',this.setTitle.bind(this,'Home'));
			this.setTitle('Home');
			this.getModel().getView('body').showItem('home');
		}
	}
);