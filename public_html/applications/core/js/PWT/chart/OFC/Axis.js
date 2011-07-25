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
			this.fireEvent('onChange',this,color);
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
			this.fireEvent('onChange',this,color);
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
			this.fireEvent('onChange',this,stroke);
			return this;
		},
		getStroke: function()
		{
			return this.config.stroke;
		},
		setSteps: function(steps)
		{
			this.config.steps=steps;
			this.fireEvent('onChange',this,steps);
			return this;
		},
		getSteps: function()
		{
			return this.config.steps;
		},
		setMin: function(min)
		{
			this.config.min=min;
			this.fireEvent('onChange',this,min);
			return this;
		},
		getMin: function()
		{
			return this.config.min;
		},
		setMax: function(max)
		{
			this.config.max=max;
			this.fireEvent('onChange',this,max);
			return this;
		},
		getMax: function()
		{
			return this.config.max;
		},
		setLabels: function(labels)
		{
			this.config.labels={labels:labels};
			this.fireEvent('onChange',this,labels);
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