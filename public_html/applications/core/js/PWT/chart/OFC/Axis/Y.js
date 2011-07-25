$PWT.Class.create
(
	{
		$namespace:		'$PWT.chart.OFC.Axis',
		$name:			'Y',
		$extends:		$PWT.chart.OFC.Axis
	}
)
(
	{
		config:
		{
			rotate:			'horizontal',
			'tick-length':	3,
			offset:			0,
			'log-scale':	0,
			colour:			'#000000',
			'grid-colour':	'#CCCCCC',
			stroke:			1,
			steps:			1,
			min:			0,
			max:			10,
			labels:			null,
			disabled:		false
		},
		rotations:	['horizontal','vertical'],
		setRotation: function(rotation)
		{
			if (this.rotations.inArray(rotation))
			{
				this.config.rotation=rotation;
				this.fireEvent('onChange',this,rotation);
			}
			else
			{
				throw new Error('Invalid Y Axis text rotation "'+rotation+'". Valid rotations are: '+this.rotations.join(', ')+'.');
			}
			return this;
		},
		getRotation: function()
		{
			return this.config.rotation;
		},
		setTickLength: function(length)
		{
			this.config['tick-length']=length;
			this.fireEvent('onChange',this,length);
			return this;
		},
		getTickLength: function()
		{
			return this.config['tick-length'];
		},
		setOffset: function(offset)
		{
			this.config.offset=offset;
			this.fireEvent('onChange',this,offset);
			return this;
		},
		getOffset: function()
		{
			return this.config.offset;
		},
		setLogScale: function(scale)
		{
			this.config['log-scale']=Boolean(scale);
			this.fireEvent('onChange',this,scale);
			return this;
		},
		getLogScale: function()
		{
			return this.config['log-scale'];
		},
		setDisabled: function(disabled)
		{
			this.config.disabled=Boolean(disabled);
			this.fireEvent('onChange',this,disabled);
			return this;
		},
		getDisabled: function()
		{
			return this.config.disabled;
		},
		getAxis: function()
		{
			if (this.config.disabled)
			{
				return null;
			}
			else
			{
				return this.getAxis.$parent();
			}
		}
	}
);