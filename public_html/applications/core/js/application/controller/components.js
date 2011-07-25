$PWT.Class.create
(
	{
		$namespace:	'app.controller',
		$name:		'components',
		$extends:	app.controller.Container
	}
)
(
	{
		initController: function()
		{
			var store=this.getView().getStore();
			store.on('load',this.onStoreLoad.bind(this));
			store.load();
		},
		onStoreLoad: function()
		{
			(function()
			{
				Ext.select('.component.exists').on
				(
					'click',
					function(event,element)
					{
						var rowID=this.getView().getStore().findExact('reference',element.innerHTML);
						this.getView().getItem('grid').getSelectionModel().selectRow(rowID);
					}.bind(this)
				);
				Ext.select('.component.nonexists').on
				(
					'click',
					function(event,element)
					{
						Ext.Msg.confirm
						(
							'Install Module',
							'Would you like to install the "'+element.innerHTML+'" component?',
							function(answer)
							{
								if (answer=='yes')
								{
									this.getModel().showMask('Requesting Module... Please wait...');
									this.getModel().API.component.main.install(element.innerHTML);
								}
							}.bind(this)
						);
					}.bind(this)
				);
			}.bind(this)).defer(1000);
		}
	}
);