$PWT.Class.create
(
	{
		$namespace:	'app.view',
		$name:		'Wizard',
		$extends:	$PWT.mvc.View
	}
)
(
	{
		wizardConfig:
		{
			title:	'Wizard',
			headerConfig:
			{
				title:	'Wizard'
			},
			cardPanelConfig:
			{
				defaults:
				{
					baseCls:	'x-small-editor',
					bodyStyle:	'padding:40px 15px 5px 120px;background-color:#F6F6F6;',
					border:		false
				}
			},
		},
		wizard:			null,
		card:			null,
		cardOrder:		[],
		createWizard: function()
		{
			this.wizardConfig.cards		=this.cardOrder;
			this.wizard					=new Ext.form.FormPanel(this.formConfig);
			return this.wizard;
		},
		getWizard: function()
		{
			return this.wizard;
		},
		addCard: function(name,card)
		{
			this.card[name]=new Ext.ux.Wiz.Card(card);
			this.cardOrder.push(this.card[name]);
			return this;
		},
		show: function()
		{
			this.wizard.show();
			return this;
		},
		hide: function()
		{
			this.wizard.hide();
			return this;
		}
	}
);