$PWT.Class.create
(
	{
		$namespace:		'$PWT.chart.OFC.Axis',
		$name:			'X',
		$extends:		$PWT.chart.OFC.Axis
	}
)
(
	{
		config:
		{
			colour:			'#000000',
			'grid-colour':	'#CCCCCC',
			'tick-height':	10,
			rotate:			null,
			stroke:			1,
			steps:			1,
			min:			0,
			max:			null,
			labels:			null
		},
		setTickHeight: function(height)
		{
			this.config['tick-height']=height;
			this.fireEvent('onChange',this,height);
			return this;
		},
		getTickHeight: function()
		{
			return this.config['tick-height'];
		},
		setRotation: function(rotation)
		{
			this.config.rotate=Number(rotation);
			return this;
		},
		getRotation: function()
		{
			return this.config.rotate;
		}
	}
);