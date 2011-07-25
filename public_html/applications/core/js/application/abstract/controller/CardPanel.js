$PWT.Class.create
(
	{
		$namespace:	'app.controller',
		$name:		'CardPanel',
		$extends:	app.controller.Container
	}
)
(
	{
		viewFolder:		null,
		connectedViews:	0,
		connectView:	function(view,callback)
		{
			if (!Object.isArray(view))
			{
				view=[view];
			}
			var prefix=(Object.isString(this.viewFolder))?this.viewFolder+'_':''
			for (var i=0,j=view.length; i<j; i++)
			{
				this.connectView.$parent
				(
					prefix+view[i],
					this.viewFolder,
					{
						onBeforeInit: function(name,theView)
						{
							theView.$parent					=this.getView();
							this.getView().children[name]	=theView;
							theView.name					=name;
						}.bind(this,view[i]),
						onAfterInit: function(name,theView)
						{
							this.getView().item[name]	=theView.getContainer();
						}.bind(this,view[i]),
						onAfterAttachController: function(name,theView,theController)
						{
							theController.$parent	=this;
							theController.name		=name;
							this.connectedViews++;
						}.bind(this,view[i])
					}
				);
			}
			$PWT.when(this,{object:'connectedViews',value:view.length}).isEqualTo
			(
				function()
				{
					if (Object.isFunction(callback))callback(this);
				}.bind(this)
			);
			return this;
		},
		setActiveItem: function(itemName)
		{
			var view=this.getView();
			$PWT.when(view.getContainer(),'rendered').isTrue
			(
				function()
				{
					if (this.cardExists(itemName))
					{
						view.getContainer().getLayout().setActiveItem(view.getItem(itemName).getId());
						this.getCard(itemName).fireEvent('onActivate');
					}
					else
					{
						throw new Error('Unable to display card "'+itemName+'".');
					}
				}.bind(this)
			);
			return this;
		},
		cardExists: function(cardName)
		{
			return Object.isDefined(this.getView().children[cardName]);
		},
		getCard: function(cardName)
		{
			return this.getView().children[cardName];
		}
	}
);