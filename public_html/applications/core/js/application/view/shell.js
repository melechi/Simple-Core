$PWT.Class.create
(
	{
		$namespace:	'app.view',
		$name:		'shell',
		$extends:	app.view.View
	}
)
(
	{
//		controller:	'shell',
		layout:		null,
		initView: function()
		{
//			this.tabPanelConfig=Object.extend
//			(
//				this.tabPanelConfig,
//				{
//					height:	this.getViewport().getBox().height-50,
//					bbar:
//					[
//						'Copyright &copy; Phantom RPG',
//						'->',
//						'Powered by Power Web Tools'
//					]
//				}
//			);
//			this.getViewport().add(this.createTabPanel());
//			this.getViewport().doLayout();
//			this.getModel().newView('login');
			
			this.getViewport().add
			(
				this.layout=new Ext.Panel
				(
					{
						layout:			'fit',
//						headerAsText:	true,
//						title:			'<div id="prpglogo" style="height:104px;text-align:center;"><img src="/images/banner2.jpg" /></div>',
						title:			'Simple Core Control Panel',
						bbar:
						[
							'Copyright &copy; Simple Site Solutions',
							'->',
							'Powered by Power Web Tools'
						]
					}
				)
			);
			this.getModel().redraw
			(
				function()
				{
					this.getModel().newView('navigation','navigation');
				}.bind(this)
			);
		},
		getLayout: function()
		{
			return this.layout;
		}
	}
);