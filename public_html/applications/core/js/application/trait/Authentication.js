$PWT.Trait.create
(
	{
		$namespace:	'app.trait',
		$name:		'Authentication'
	}
)
(
	{
		setAuthenticated: function(authenticated)
		{
			this.authenticated=Boolean(authenticated);
			if (this.authenticated)
			{
				this.fireEvent('onAutenticated');
			}
			else
			{
				this.fireEvent('onUnautenticated');
			}
			return this;
		},
		isAutenticated: function()
		{
			return Boolean(this.authenticated);
		}
	}
);