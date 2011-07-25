$PWT.Class.create
(
	{
		$namespace:	'app.controller',
		$name:		'Card',
		$extends:	app.controller.Container
	}
)
(
	{
		$parent:		null,
		name:			null,
		initController: function()
		{
			this.getView().$parent.getContainer().add(this.getView().getContainer());
			this.getModel().redraw();
		},
		activate:	function()
		{
			this.$parent.setActiveItem(this.name);
			return this;
		},
		activateSibling: function(itemName)
		{
			this.$parent.setActiveItem(itemName);
			return this;
		},
		getSibling: function(itemName)
		{
			return this.$parent.getCard(itemName);
		},
		siblingExists: function(itemName)
		{
			return this.$parent.cardExists(itemName);
		}
	}
);