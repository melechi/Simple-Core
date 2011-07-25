$PWT.Class.create
(
	{
		$namespace:	'app.view',
		$name:		'applications',
		$extends:	app.view.CardPanel
	}
)
(
	{
		controller:	'applications',
//		__containerConfig:
//		{
////			title:	'&nbsp;',
//			tbar:		['&nbsp;'],
//			bodyStyle:	'padding: 20px;',
//		},
		containerConfig:
		{
			layout:				'card',
			layoutConfig:		{deferredRender:true},
			layoutOnCardChange:	true,
			bodyStyle:			'padding: 0px;',
			border:				true,
			buttonAlign:		'right',
			activeItem:			0,
			height:				400,
			tbar:				['&nbsp;'],
		},
//		initView:	function()
//		{
//			
//		}
	}
);