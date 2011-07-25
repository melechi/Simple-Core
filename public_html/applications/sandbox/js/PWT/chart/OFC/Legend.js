$PWT.Class.create
(
	{
		$namespace:		'$PWT.chart.OFC',
		$name:			'Legend',
		$traits:		[$PWT.trait.Configurable,$PWT.trait.Observable]
	}
)
(
	{
		config:
		{
			position:		'right',
			border:			true,
			stroke:			1,
			shadow:			true,
			border_colour:	'#808080',
			bg_colour:		'#F8F8F8',
			alpha:			1,
			margin:			10,
			padding:		6,
			disabled:		false
		},
		events:
		{
			onChange:	true
		},
		positions:	['top','right'],
		setPosition: function(position)
		{
			if (this.position.inArray(position))
			{
				this.config.position=position;
				this.fireEvent('onChange',this,position);
			}
			else
			{
				throw new Error('Invalid legend position "'+position+'". Valid positions are: '+this.positions.join(', ')+'.');
			}
			return this;
		},
		getPosition: function()
		{
			return this.config.position;
		},
		setBorder: function(border)
		{
			this.config.border=Boolean(border);
			this.fireEvent('onChange',this,border);
			return this;
		},
		getBorder: function()
		{
			return this.config.border;
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
		setShadow: function(shadow)
		{
			this.config.shadow=Boolean(shadow);
			this.fireEvent('onChange',this,shadow);
			return this;
		},
		getShadow: function()
		{
			return this.config.shadow;
		},
		setBorderColor: function(color)
		{
			this.config.border_colour=color;
			this.fireEvent('onChange',this,color);
			return this;
		},
		getBorderColor: function()
		{
			return this.config.border_colour;
		},
		setBorderColour: function()
		{
			return this.setBorderColor.apply(this,arguments);
		},
		getBorderColour: function()
		{
			return this.getBorderColor.apply(this,arguments);
		},
		setBackgroundColor: function(color)
		{
			this.config.bg_colour=color;
			this.fireEvent('onChange',this,color);
			return this;
		},
		getBackgroundColor: function()
		{
			return this.config.bg_colour;
		},
		setBackgroundColour: function()
		{
			return this.setBackgroundColor.apply(this,arguments);
		},
		getBackgroundColour: function()
		{
			return this.getBackgroundColor.apply(this,arguments);
		},
		setAlpha: function(alpha)
		{
			this.config.alpha=alpha;
			this.fireEvent('onChange',this,alpha);
			return this;
		},
		getAlpha: function()
		{
			return this.config.alpha;
		},
		setMargin: function(margin)
		{
			this.config.margin=margin;
			return this;
		},
		getMargin: function()
		{
			return this.config.margin;
		},
		setPadding: function(padding)
		{
			this.config.padding=padding;
			this.fireEvent('onChange',this,padding);
			return this;
		},
		getPadding: function()
		{
			return this.config.padding;
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
		getLegend: function()
		{
			return (this.config.disabled)?null:this.config;
		}
	}
);