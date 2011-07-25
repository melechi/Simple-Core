$PWT.Trait.create
(
	{
		$namespace:	'app.trait',
		$name:		'Body'
	}
)
(
	{
		setTitle: function(title)
		{
			this.getModel().getView('structure').getItem('body').setTitle(title);
		}
	}
);