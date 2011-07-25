$PWT.Class.create
(
	{
		$namespace:		'$PWT.chart.OFC',
		$name:			'Axis',
		$traits:		[$PWT.trait.Configurable,$PWT.trait.Observable]
	}
)
(
	{
		config:
		{
			colour:			'#d000d0',
			'grid-colour':	'#00ff00',
			stroke:			1,
			steps:			1,
			min:			0,
			max:			null,
			labels:			null
		},
		events:
		{
			onChange:	true
		},
		init: function()
		{
			this.config.labels={labels:this.config.labels};
		},
		setColor: function(color)
		{
			this.config.colour=color;
			return this;
		},
		getColor: function()
		{
			return this.colour;
		},
		setColour: function()
		{
			return this.setColor.apply(this,arguments);
		},
		getColour: function()
		{
			return this.getColor.apply(this,arguments);
		},
		setGridColor: function(color)
		{
			this.config['grid-colour']=color;
			return this;
		},
		getGridColor: function()
		{
			return this.config['grid-colour'];
		},
		setGridColour: function()
		{
			return this.setGridColor.apply(this,arguments);
		},
		getGridColour: function()
		{
			return this.getGridColour.apply(this,arguments);
		},
		setStroke: function(stroke)
		{
			this.config.stroke=stroke;
			return this;
		},
		getStroke: function()
		{
			return this.config.stroke;
		},
		setMin: function(min)
		{
			this.config.min=min;
			return this;
		},
		getMin: function()
		{
			return this.config.min;
		},
		setMax: function(max)
		{
			this.config.max=max;
			return this;
		},
		getMax: function()
		{
			return this.config.max;
		},
		setLabels: function(labels)
		{
			this.config.labels={labels:labels};
			return this;
		},
		getLabels: function()
		{
			return this.config.labels;
		},
		getAxis: function()
		{
			return this.config;
		}
	}
);