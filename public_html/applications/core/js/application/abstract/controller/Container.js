$PWT.Class.create
(
	{
		$namespace:	'app.controller',
		$name:		'Container',
		$extends:	$PWT.mvc.Controller
	}
)
(
	{
		connectView: function(view,viewDir,listeners)
		{
			if (Object.isDefined(viewDir) && Object.isString(viewDir))
			{
				this.getModel().newView(view,{view:view,viewDir:viewDir},listeners);
			}
			else
			{
				this.getModel().newView(view,view,listeners);
			}
			return this;
		}
	}
);