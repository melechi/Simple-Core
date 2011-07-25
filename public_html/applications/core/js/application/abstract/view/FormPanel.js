$PWT.Class.create
(
	{
		$namespace:	'app.view',
		$name:		'FormPanel',
		$extends:	$PWT.mvc.View
	}
)
(
	{
		formPanel:			null,
		formPanelConfig:
		{
			bodyStyle:		'padding: 5px;',
			border:			false,
			buttonAlign:	'right',
		},
		field:			{},
		button:			{},
		fieldOrder:		[],
		buttonOrder:	[],
		createForm: function()
		{
			this.formConfig.items	=this.fieldOrder;
			this.formConfig.buttons	=this.buttonOrder;
			this.formPanel			=new Ext.form.FormPanel(this.formConfig);
			return this.formPanel;
		},
		getFormPanel: function()
		{
			return this.form;
		},
		getForm: function()
		{ 
			return this.form.getForm();
		},
		getField: function(field)
		{
			return (this.field[field]);
		},
		getButton: function(button)
		{
			return this.button[button];
		},
		addField: function(name,xtype,field)
		{
			this.field[name]=field;
			this.fieldOrder.push(this.field[name]);
			return this;
		},
		addButton: function(name,button)
		{
			this.button[name]=button;
			this.buttonOrder.push(this.button[name]);
			return this;
		}
	}
);