$PWT.namespace('app.ext').SelectBox=Ext.extend
(
	Ext.form.ComboBox,
	{
		mode:			'local',
		triggerAction:	'all',
		editable:		false,
		store:			null,
		data:			null,
		initComponent:	function()
		{
			app.ext.SelectBox.superclass.initComponent.call(this);
			if (!this.store)
			{
				this.store=new Ext.data.SimpleStore
				(
					{
						fields:	['value','label'],
						data:	this.data || []
					}
				);
				this.displayField	='label';
				this.valueField		='value';
			}
		}
	}
);
Ext.reg('select',app.ext.SelectBox);