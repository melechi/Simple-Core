$PWT.Class.create
(
	{
		$namespace:	'app.controller',
		$name:		'shell',
		$extends:	$PWT.mvc.Controller
	}
)
(
	{
		initController: function()
		{
//			//Activations
//			this.getView().getContentsTab()	.on('activate',	this.activateTabHandler.bind(this));
//			this.getView().getIndexTab()	.on('activate',	this.activateTabHandler.bind(this));
//			this.getView().getDetailsTab()	.on('activate',	this.activateTabHandler.bind(this));
//			
//			//Renders
//			this.getView().getContentsTab()	.on('render',	this.renderViewHandler.bind(this,'contents'));
//			this.getView().getIndexTab()	.on('render',	this.renderViewHandler.bind(this,'index'));
//			this.getView().getDetailsTab()	.on('render',	this.renderViewHandler.bind(this,'details'));
//			
//			//Focus the contents tab.
//			this.getView().getTabPanel().activate(this.getView().getContentsTab());
		}
//		activateTabHandler: function(tab)
//		{
//			this.getView().setTabPanelTitle(tab.title);
//		},
//		renderViewHandler: function(view)
//		{
//			this.getModel().newView(view);
//		}
	}
);