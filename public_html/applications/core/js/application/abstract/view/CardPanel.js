$PWT.Class.create
(
	{
		$namespace:	'app.view',
		$name:		'CardPanel',
		$extends:	app.view.Container
	}
)
(
	{
		children:	[],
		containerConfig:
		{
			layout:				'card',
			layoutConfig:
			{
				deferredRender:		true,
				layoutOnCardChange:	true
			},
			layoutOnCardChange:	true,
			bodyStyle:			'padding: 5px;',
			border:				false,
			buttonAlign:		'right',
			activeItem:			0,
			height:				200
		}
	}
);