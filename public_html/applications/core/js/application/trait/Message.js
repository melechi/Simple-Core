$PWT.Trait.create
(
	{
		$namespace:	'app.trait',
		$name:		'Message'
	}
)
(
	{
		message: function(message,handler,title)
		{
			Ext.MessageBox.show
			(
				{
					title:		title?title:'System Message',
					msg:		message,
					buttons:	Ext.MessageBox.OK,
					icon:		Ext.MessageBox.INFO,
					fn:			handler
				}
			);
		},
		warning: function(message,handler,title)
		{
			Ext.MessageBox.show
			(
				{
					title:		title?title:'Warning!',
					msg:		message,
					buttons:	Ext.MessageBox.OK,
					icon:		Ext.MessageBox.WARNING,
					fn:			handler
				}
			);
		},
		error: function(message,handler,title)
		{
			Ext.MessageBox.show
			(
				{
					title:		title?title:'Error!',
					msg:		message,
					buttons:	Ext.MessageBox.OK,
					icon:		Ext.MessageBox.ERROR,
					fn:			handler
				}
			);
		}
	}
);


