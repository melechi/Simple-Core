$PWT.Class.create
(
	{
		$namespace:	'app.view',
		$name:		'AccordionPanel',
		$extends:	app.view.Container
	}
)
(
	{
		__containerConfig:
		{
			layout:		'accordion',
			layoutConfig:
			{
				animate:	true,
				sequence:	true
			}
		}
	}
);