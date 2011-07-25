$PWT.Class.create
(
	{
		$namespace:		'$PWT.chart.OFC',
		$name:			'Tooltip',
		$traits:		[$PWT.trait.Configurable,$PWT.trait.Observable]
	}
)
(
	{
		config:
		{
			shadow:		true,
			mouse:		2,
			stroke:		1,
			colour:		'#000000',
			background:	'#FFFFFF',
			title:		'font-size: 14px; color: #0A0A2A;',
			body:		'font-size: 10px; font-weight: bold; color: #000000;',
			tip:		'#val#',
			disabled:	false
		},
		events:
		{
			onChange:	true,
		},
		init:	function()
		{
			if (this.config.tip=='#val#' && Object.isDefined(this.config.template))
			{
				this.config.tip=this.config.template;
			}
			else if (this.config.tip==null)
			{
				this.config.tip='#val#';
			}
		},
		setShadow: function(shadow)
		{
			this.config.shadow=shadow;
			this.fireEvent('onChange',this,shadow);
			return this;
		},
		getShadow: function()
		{
			return this.config.shadow;
		},
		setMouse: function(mouse)
		{
			this.config.mouse=mouse;
			this.fireEvent('onChange',this,mouse);
			return this;
		},
		getMouse: function()
		{
			return this.config.mouse;
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
		setTextColor: function(color)
		{
			this.config.colour=color;
			this.fireEvent('onChange',this,color);
			return this;
		},
		getTextColor: function()
		{
			return this.config.colour;
		},
		setTextColour: function()
		{
			return this.setTextColor.apply(this,arguments);
		},
		getTextColour: function()
		{
			return this.getTextColor.apply(this,arguments);
		},
		setBackgroundColor: function(color)
		{
			this.config.background=color;
			this.fireEvent('onChange',this,color);
			return this;
		},
		getBackgroundColor: function()
		{
			return this.config.background;
		},
		setBackgroundColour: function()
		{
			return this.setBackgroundColor.apply(this,arguments);
		},
		getBackgroundColour: function()
		{
			return this.getBackgroundColor.apply(this,arguments);
		},
		setTitleStyle: function(style)
		{
			this.config.title='{'+style+'}';
			this.fireEvent('onChange',this,style);
			return this;
		},
		getTitleStyle: function()
		{
			return this.config.title;
		},
		setBodyStyle: function(style)
		{
			this.config.body='{'+style+'}';
			this.fireEvent('onChange',this,style);
			return this;
		},
		getBodyStyle: function()
		{
			return this.config.body;
		},
		setTemplate: function(template)
		{
			this.config.tip=template;
			this.fireEvent('onChange',this,template);
		},
		getTemplate: function()
		{
			return this.config.tip;
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
		getTooltip: function()
		{
			if (!this.config.disabled)
			{
				var ret=Object.clone(this.config);
				ret.title	='{'+ret.title+'}';
				ret.body	='{'+ret.body+'}';
				return ret;
			}
			return null;
		}
	}
);