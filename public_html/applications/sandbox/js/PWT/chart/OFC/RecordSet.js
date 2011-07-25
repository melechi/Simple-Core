$PWT.Class.create
(
	{
		$namespace:		'$PWT.chart.OFC',
		$name:			'RecordSet',
		$traits:		[$PWT.trait.Configurable,$PWT.trait.Observable]
	}
)
(
	{
		types:				[],
		config:
		{
			type:			'line',
			alpha:			0.5,
			colour:			'#000000',
			text:			'',
			'font-size':	10,
			animate:		true,
			overlap:		false,
			barwidth:		0.9
			//TODO: tip
		},
		events:
		{
			onChange:	true
		},
		values:	[],
		init:	function()
		{
			this.types=$PWT.chart.OFC.prototype.types;
			if (Object.isDefined(this.config.values))
			{
				this.setValues(this.config.values);
				delete this.config.values;
			}
		},
		onChange: function()
		{
			var args=$A(arguments);
			args.unshift('onChange');
			this.fireEvent.apply(this,args);
		},
		setType: function(type)
		{
			if (!this.types.inArray(type))
			{
				throw new Error('Invalid chart type "'+type+'".');
			}
			else
			{
				this.config.type=type;
			}
			return this;
		},
		getType: function()
		{
			return this.config.type;
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
		setText: function(text)
		{
			this.config.text=text;
			this.fireEvent('onChange',this,text);
			return this;
		},
		getText: function()
		{
			return this.text;
		},
		setFontSize: function(fontSize)
		{
			this.config['font-size']=fontSize;
			this.fireEvent('onChange',this,fontSize);
			return this;
		},
		getFontSize: function()
		{
			return this.config['font-size'];
		},
		setValues: function(values,append)
		{
			if (!append)//Hard reset.
			{
				$PWT.GC.object(this.values);
				delete this.values;
				this.values=[];
			}
			if (Object.isArray(values))
			{
				for (var i=0,j=values.length; i<j; i++)
				{
					if (Object.isAssocArray(values[i]))
					{
						if (Object.isDefined(values[i].className) && values[i].className=='Record')
						{
							this.values.push(values[i]);
							values[i].observe('onChange',this.onChange.bind(this));
						}
						else
						{
							this.values.push(new $PWT.chart.OFC.Record(values[i]));
							this.values.last().observe('onChange',this.onChange.bind(this));
						}
					}
					else
					{
						this.values.push(new $PWT.chart.OFC.Record({top:values[i]}));
						this.values.last().observe('onChange',this.onChange.bind(this));
					}
				}
			}
			else if (Object.isAssocArray(values) && Object.isDefined(values.className) && values.className=='Record')
			{
				this.values.push(values);
				values.observe('onChange',this.onChange.bind(this));
			}
			else
			{
				this.values.push(new $PWT.chart.OFC.Record({top:values}));
				this.values.last().observe('onChange',this.onChange.bind(this));
			}
			this.onChange();
			return this;
		},
		getValues: function()
		{
			return this.values;
		},
		getRecordSet: function()
		{
			var ret=Object.clone(this.config);
			ret.values=[];
			for (var i=0,j=this.values.length; i<j; i++)
			{
				ret.values.push(this.values[i].getRecord());
			}
			return ret;
		},
		setAnimations: function(animate)
		{
			this.config.animate=Boolean(animate);
			return this;
		},
		getAnimations: function()
		{
			return this.config.animate;
		},
		setOverlap: function(overlap)
		{
			this.config.overlap=overlap;
			this.fireEvent('onChange',this,overlap);
			return this;
		},
		getOverlap: function()
		{
			return this.config.overlap;
		},
		setBarwidth: function(barwidth)
		{
			this.config.barwidth=barwidth;
			this.fireEvent('onChange',this,barwidth);
			return this;
		},
		getBarwidth: function()
		{
			return this.config.barwidth;
		}
	}
);