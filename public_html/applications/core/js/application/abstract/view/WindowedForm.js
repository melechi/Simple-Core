$PWT.Class.create
(
	{
		$namespace:	'app.view',
		$name:		'WindowedForm',
		$extends:	app.view.FormView
	}
)
(
	{
		window:				null,
		width:				800,
		height:				600,
		createShowWindow:	function(callback)
		{
			//Hide the window if it already exists.
			this.hideWindow();
			//Create the fields.
			this.createFields();
			//Create the buttons.
			this.createButtons();
			//Create the form.
			this.createForm();
			//Create the containing window.
			this.createWindow();
			//Show the window.
			this.showWindow();
			//Redraw the screen.
			this.getModel().redraw(callback);
		},
		createWindow: function()
		{
			this.window=new Ext.Window
			(
				{
					title:			'',
					width:			this.width,
					height:			this.height,
					closable:		false,
					resizeable:		false,
					minimizable:	false,
					maximizable:	false,
					modal:			true,
					items:			this.form
				}
			);
		},
		showWindow: function()
		{
			if (this.window)
			{
				this.window.show();
			}
			return this;
		},
		hideWindow: function()
		{
			if (this.window)
			{
				this.window.hide();
			}
			return this;
		},
		getWindow: function()
		{
			return this.window;
		}
	}
);