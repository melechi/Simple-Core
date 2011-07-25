$PWT.Class.create
(
	{
		$namespace:	'app.view',
		$name:		'applications_create',
		$extends:	app.view.Container
	}
)
(
	{
		__defaultController:	'app.controller.Card',
		controller:	true,//'applications_create',
		initView:	function()
		{
			
			this.addItem
			(
				'test',
				{
					title:	'Foo',
					html:	'<b>Bar</b>'
				}
			);
//			this.setActiveItem('test');
		},
		getStore: function()
		{
			return this.store;
		}
	}
);