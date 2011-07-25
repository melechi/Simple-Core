$PWT.Class.create
(
	{
		$namespace:		'$PWT.chart.OFC',
		$name:			'Record',
		$traits:		[$PWT.trait.Configurable,$PWT.trait.Observable]
	}
)
(
	{
		config:
		{
			top:		null,
			bottom:		null,
			colour:		null,
			tip:		null,
			label:		null,
			value:		null
		},
		events:
		{
			onChange:	true
		},
		init:	function()
		{
			if (Object.isNull(this.config.top) && Object.isDefined(this.config.value))
			{
				if (Object.isArray(this.config.value))
				{
					this.config.top		=this.config.value[0];
					this.config.bottom	=this.config.value[1];
				}
				else
				{
					this.config.top=this.config.value;
				}
			}
			this.setTip(this.config.tip);
		},
		onChange: function()
		{
			var args=$A(arguments);
			args.unshift('onChange');
			this.fireEvent.apply(this,args);
		},
		setValue: function(value)
		{
			if (Object.isArray(value))
			{
				this.config.top		=value[0];
				this.config.bottom	=value[1];
			}
			else
			{
				this.config.top=value;
			}
			this.fireEvent('onChange',this,value);
			return this;
		},
		getValue: function()
		{
			if (!Object.isNull(this.config.bottom))
			{
				return [this.config.top,this.config.bottom];
			}
			else
			{
				return this.config.top;
			}
		},
		setLabel: function(label)
		{
			this.config.label=label;
			this.fireEvent('onChange',this,label);
			return this;
		},
		getLabel: function()
		{
			return this.config.label;
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
		setTip: function(tip)
		{
			this.destroyTip();
			if (Object.isAssocArray(tip))
			{
				if (Object.isDefined(tip.className) && tip.className=='Tooltip')
				{
					this.config.tip=tip;
				}
				else
				{
					this.config.tip=new $PWT.chart.OFC.Tooltip(tip);
				}
			}
			else
			{
				this.config.tip=new $PWT.chart.OFC.Tooltip({tip:tip});
			}
			this.config.tip.observe('onChange',this.onChange.bind(this));
			return this;
		},
		getTip: function()
		{
			return this.config.tip;
		},
		destroyTip: function()
		{
			$PWT.GC.object(this.config.tip);
			delete this.config.tip;
			this.config.tip=null;
		},
		getRecord: function()
		{
			if (Object.isNull(this.config.colour)
			&& Object.isNull(this.config.bottom)
			&& Object.isNull(this.config.label)
			&& this.config.tip.getTemplate()=='#val#')
			{
				return this.config.top;
			}
			else
			{
				var ret=Object.clone(this.config);
				if (!Object.isNull(ret.tip))
				{
					ret.tip=ret.tip.getTemplate();
				}
				return ret;
			}
		}
	}
);