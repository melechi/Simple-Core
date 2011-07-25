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
			colour:		null,
			tip:		null
		},
		events:
		{
			onChange:	true
		},
		init:	function()
		{
			if (Object.isNull(this.config.top) && Object.isDefined(this.config.value))
			{
				this.config.top=this.config.value;
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
			this.config.top=value;
			this.fireEvent('onChange',this,value);
			return this;
		},
		getValue: function()
		{
			return this.config.top;
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
			$PWT.GC.object(this.config.tip);
			delete this.config.tip;
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
			return this.tip;
		},
		getRecord: function()
		{
			if (Object.isNull(this.config.colour) && this.config.tip.getTemplate()=='#val#')
			{
				return this.config.top;
			}
			else
			{
				var ret=Object.clone(this.config);
				ret.tip=ret.tip.getTemplate();
				return ret;
			}
		}
	}
);