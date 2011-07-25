$PWT.Class.create
(
	{
		$namespace:	'app.view',
		$name:		'applications_manageApplication_navigation',
		$extends:	app.view.AccordionPanel
	}
)
(
	{
//		controller:	'applications_manageApplication',
//		__defaultController:	'app.controller.Card',
		initView:	function()
		{
//			this.addItem
//			(
//				'navigation',
//				{
//					title:	'Manage Application',
//					html:	'<b>Bar</b>'
//				}
//			);
			this.getModel().getShell().getLayout().add(this.getContainer());
			this.getModel().redraw();
		}
	}
);