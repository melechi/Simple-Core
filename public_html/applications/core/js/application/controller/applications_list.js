$PWT.Class.create
(
	{
		$namespace:	'app.controller',
		$name:		'applications_list',
		$extends:	app.controller.Card
	}
)
(
	{
		initController: function()
		{
			this.initController.$parent();
			this.getView().getItem('grid').on('rowdblclick',this.onRowDblClick.bind(this));
		},
		onRowDblClick: function(grid,index)
		{
			this.activateSibling('manageApplication');
			(function()
			{
				this.getSibling('manageApplication').getController().setActiveApplication(this.getView().getStore().getAt(index).data.name);
			}.bind(this)).defer(200);
		}
	}
);