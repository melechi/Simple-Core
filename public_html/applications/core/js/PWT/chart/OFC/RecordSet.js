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
			'start-angle':	35,
			'gradient-fill':null,
			animate:		true,
			overlap:		false,
			colours:		null,
			barwidth:		0.9,
			tip:			null,
			values:			[]
		},
		events:
		{
			onChange:	true
		},
		values:	[],
		init:	function()
		{
			this.types=Object.clone($PWT.chart.OFC.prototype.types);
			if (Object.isDefined(this.config.values))
			{
				this.setValues(this.config.values);
				delete this.config.values;
			}
//			this.setTip(this.config.tip);
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
				this.fireEvent('onChange',this,type);
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
							if (this.config.type=='pie' && !Object.isNull(this.config.tip) && (Object.isDefined(values[i].tip) && !Object.isNull(values[i].tip)))
							{
								values[i].destroyTip();
							}
							this.values.push(values[i]);
							values[i].observe('onChange',this.onChange.bind(this));
						}
						else
						{
							this.values.push(new $PWT.chart.OFC.Record(values[i]));
							if (this.config.type=='pie' && !Object.isNull(this.config.tip) && (!Object.isDefined(values[i].tip) && !Object.isNull(values[i].tip)))
							{
								this.values.last().destroyTip();
							}
							this.values.last().observe('onChange',this.onChange.bind(this));
						}
					}
					else
					{
						if (Object.isArray(values[i]))
						{
							if (this.config.type=='pie' && !Object.isNull(this.config.tip))
							{
								this.values.push(new $PWT.chart.OFC.Record({top:values[i][0],bottom:values[i][1],tip:this.config.tip}));
							}
							else
							{
								this.values.push(new $PWT.chart.OFC.Record({top:values[i][0],bottom:values[i][1]}));
							}
						}
						else
						{
							if (this.config.type=='pie' && !Object.isNull(this.config.tip))
							{
								this.values.push(new $PWT.chart.OFC.Record({top:values[i],tip:this.config.tip}));
							}
							else
							{
								this.values.push(new $PWT.chart.OFC.Record({top:values[i]}));
							}
						}
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
		},
		setStartAngle: function(startAngle)
		{
			this.config['start-angle']=startAngle;
			this.fireEvent('onChange',this,startAngle);
			return this;
		},
		getStartAngle: function()
		{
			return this.config['start-angle'];
		},
		setGradientFill: function(gradientFill)
		{
			this.config['gradient-fill']=gradientFill;
			this.fireEvent('onChange',this,gradientFill);
			return this;
		},
		getGradientFill: function()
		{
			return this.config['gradient-fill'];
		},
		setColors: function(colors)
		{
			this.config.colours=colors;
			this.fireEvent('onChange',this,colors);
			return this;
		},
		getColors: function()
		{
			return this.config.colours;
		},
		addColor: function(color)
		{
			if (!this.hasColor(color))
			{
				this.config.colours.push(color);
				this.fireEvent('onChange',this,this.config.colours);
			}
			return this;
		},
		removeColor: function(color)
		{
			if (this.hasColor(this.config.colours))
			{
				var newColors=[];
				for (var i=0,j=this.config.colours.length; i<j; i++)
				{
					if (this.config.colours[i]!=color)
					{
						newColors.push(this.config.colours[i]);
					}
				}
				if (newColors.length)
				{
					this.config.colours=newColors;
				}
				else
				{
					this.config.colours=null;
				}
				this.fireEvent('onChange',this,this.config.colours);
			}
			return this;
		},
		hasColor: function(color)
		{
			if (Object.isArray(this.config.colours))
			{
				return this.config.colours.inArray(color);
			}
			return false;
		},
		setColours: function()
		{
			return this.setColors.apply(this,arguments);
		},
		getColours: function()
		{
			return this.getColors.apply(this,arguments);
		},
		addColour: function(colour)
		{
			return this.addColor.apply(this,arguments);
		},
		removeColour: function()
		{
			return this.removeColor.apply(this,arguments);
		},
		hasColour: function()
		{
			return this.hasColor.apply(this,arguments);
		},
//		setTip: function(tip)
//		{
//			$PWT.GC.object(this.config.tip);
//			delete this.config.tip;
//			if (Object.isAssocArray(tip))
//			{
//				if (Object.isDefined(tip.className) && tip.className=='Tooltip')
//				{
//					this.config.tip=tip;
//				}
//				else
//				{
//					this.config.tip=new $PWT.chart.OFC.Tooltip(tip);
//				}
//			}
//			else
//			{
//				this.config.tip=new $PWT.chart.OFC.Tooltip({tip:tip});
//			}
//			this.config.tip.observe('onChange',this.onChange.bind(this));
//			return this;
//		},
		setTip: function(tip)
		{
			this.config.tip=tip;
			this.fireEvent('onChange',this,tip);
			return this;
		},
		getTip: function()
		{
			return this.config.tip;
		}
	}
);