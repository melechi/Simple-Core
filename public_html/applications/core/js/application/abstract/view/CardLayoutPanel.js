$PWT.Class.create
(
	{
		$namespace:	'app.view',
		$name:		'CardLayoutPanel',
		$extends:	app.view.Container
	}
)
(
	{
		__containerConfig:
		{
			layout:				'card',
			activeItem:			0,
			layoutConfig:
			{
				layoutOnCardChange:	true,
				deferredRender:		false
			}
		},
		showItem: function(name)
		{
			//Note: Card layouts seem to be getting an item by default.
			//So we add one so that we can ignore the first item.
			var order=this.getItemOrderNumber(name)+1;
			if (!Object.isNull(order))
			{
				this.container.getLayout().setActiveItem(order);
			}
			else
			{
				throw new Error('Unable to show item "'+name+'". Item could not be found in item order list.');
			}
			return this;
		}
	}
);