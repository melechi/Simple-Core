$PWT.Class.create
(
	{
		$namespace:	'app.view',
		$name:		'applications_create',
		$extends:	app.view.FormPanel
	}
)
(
	{
//		controller:	'applications_create',
		store:		null,
		initView:	function()
		{
			this.addItem
			(
				'name',
				{
					allowBlank:		false,
					width:			100,
					fieldLabel:		'Application Name',
					emptyText:		'Enter Application Name...'
				}
			);
			
			
			
			this.getModel().getView('navigation').getItem('applications').add(this.getContainer())
			this.getModel().redraw();
		},
		getStore: function()
		{
			return this.store;
		}
	}
);