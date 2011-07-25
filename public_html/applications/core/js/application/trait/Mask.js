$PWT.Trait.create
(
	{
		$namespace:	'app.trait',
		$name:		'Mask'
	}
)
(
	{
		hideMask: function()
		{
			this.mask.hide();
			if (this.config.initMaskEl)
			{
				this.maskEl.hide();
			}
		},
		showMask: function(message)
		{
			if (!this.config.initMaskEl)
			{
				this.mask.msg=message;
				this.maskEl.show();
			}
			else
			{
				new Ext.Element(this.maskEl).select('div').update(message);
				this.maskEl.show();
			}
			this.mask.show();
		}
	}
);