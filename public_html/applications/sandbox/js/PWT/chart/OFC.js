$PWT.Class.create
(
	{
		$namespace:		'$PWT.chart',
		$name:			'OFC',
		$extends:		$PWT.chart.Chart
	}
)
(
	{
		config:
		{
			renderTo:		null,
			autoRender:		true,
			swf:			null,
			type:			null,
			width:			300,
			height:			300,
			title:			'',
			titleStyle:		null,
			legend:			false,
			yLegendText:	null,
			yLegendStyle:	null,
			tooltip:		null,
			x_axis:			null,
			y_axis:			null,
			y_axis_right:	false
		},
		events:
		{
			onChange: true
		},
		types:
		[
		//Line
			'line','line_dot','line_hollow',
		//Bar
			'bar','bar_filled','bar_glass',
			'bar_3d','bar_sketch','hbar',
			'bar_stack','hbar_stack','bar_round_glass',
			'bar_cylinder','bar_cylinder_outline','bar_dome',
			'bar_round','bar_round3d',
			'bar_plastic','bar_plastic_flat',
			//'bar_fade',
		//Area
			'area_line','area_hollow',
		//Pie
			'pie',
		//Scatter
			'scatter','scatter_line',
		//Candle
			'candle'
		//Misc
//			'shape','tags'
		],
		swf:			'open-flash-chart.swf',
		_disableRefresh:	false,
		init:	function()
		{
			this.disableRefresh();
			this.chart=Object.extend
			(
				this.chart,
				{
					titleStyle:					'color: #736AFF; font-size: 12px;',
					yLegendText:				'',
					yLegendStyle:				'color: #736AFF; font-size: 12px;',
					bgColor:					"#FFFFFF",
					legend:						null,
					tooltip:					null,
					xAxis:						null,
					yAxisLeft:					null,
					yAxisRight:					null,
					numDecimals:				null,
					fixedNumDecimalsForced:		null,
					decimalSeparatorComma:		null,
					thousandSeparatorDisabled:	null
				}
			);
			if (Object.isAssocArray(this.config.title) && Object.isDefined(this.config.title.text))
			{
				var tmp=Object.clone(this.config.title);
				this.config.title=tmp.text;
				if (Object.isDefined(tmp.style))
				{
					this.config.titleStyle	=tmp.style;
					this.chart.titleStyle	=tmp.style;
				}
				delete tmp;
			}
			else if (!Object.isNull(this.config.titleStyle))
			{
				this.chart.titleStyle=this.config.titleStyle;
			}
			if (Object.isAssocArray(this.config.y_legend) && Object.isDefined(this.config.y_legend.text))
			{
				var tmp=Object.clone(this.config.y_legend);
				this.config.yLegendText	=tmp.text;
				this.chart.yLegendText	=tmp.text;
				if (Object.isDefined(tmp.style))
				{
					this.config.yLegendStyle	=tmp.style;
					this.chart.yLegendStyle		=tmp.style;
				}
				delete tmp;
			}
			else if (!Object.isNull(this.config.yLegendText))
			{
				this.chart.yLegendText=this.config.yLegendText;
				if (!Object.isNull(this.config.yLegendStyle))
				{
					this.chart.yLegendStyle=this.config.yLegendStyle;
				}
			}
			this.setTooltip(this.config.tooltip);
			this.setLegend(this.config.legend);
			this.setXAxis(this.config.x_axis);
			this.setLeftYAxis(this.config.y_axis);
			this.setRightYAxis(this.config.y_axis_right);
			this.enableRefresh();
			this.init.$parent();
		},
		onChange: function()
		{
			this.refresh();
			var args=$A(arguments);
			args.unshift('onChange');
			this.fireEvent.apply(this,args);
		},
		render: function(renderTo)
		{
			if (!this.rendered)
			{
				if (!renderTo && this.config.renderTo)
				{
					renderTo=this.config.renderTo;
				}
				if (renderTo)
				{
					swfobject.embedSWF
					(
						this.config.swf || this.swf,
						renderTo,
						this.chart.width,
						this.chart.height,
						"9.0.0",
						null,
						{loading:'Loading Chart...'}
					);
					this.chartObject=$(renderTo);
					this.refresh();
				}
				else
				{
					throw new Error('Unable to render. Nowhere to render to!');
				}
				this.render.$parent();
			}
		},
		load: function(JSON)
		{
			$PWT.when(this.chartObject,'load').isFunction
			(
				function()
				{
					this.chartObject.load(Object.toJSON(JSON));
					this.fireEvent('onLoad',this,JSON);
					for (var i=0,j=this.data.length; i<j; i++)
					{
						this.data[i].setAnimations(false);
					}
				}.bind(this)
			);
		},
		reload: function()
		{
			$PWT.when(this.chartObject,'reload').isFunction
			(
				function()
				{
					this.chartObject.reload();
				}.bind(this)
			);
		},
		refresh: function()
		{
			if (!this._disableRefresh)this.load(this.applyTemplate());
		},
		applyTemplate: function(values)
		{
			values=Object.extend(values,this.chart);
			
			var data=[];
			for (var i=0,j=this.data.length; i<j; i++)
			{
				data.push(this.data[i].getRecordSet());
			}
			return {
				"bg_colour":						this.chart.bgColor,
				"title":
				{
					"text":							this.chart.title,
					"style":						"{"+this.chart.titleStyle+"}"
				},
				"y_legend":
				{
					"text":							this.chart.yLegendText,
					"style":						"{"+this.chart.yLegendStyle+"}"
				},
				"elements":							data,
				"x_axis":							this.chart.xAxis.getAxis(),
				"y_axis":							this.chart.yAxisLeft.getAxis(),
				"y_axis_right":						this.chart.yAxisRight.getAxis(),
				"legend":							this.chart.legend.getLegend(),
				"tooltip":							this.chart.tooltip.getTooltip(),
				"num_decimals":						this.chart.numDecimals,
				"is_fixed_num_decimals_forced":		this.chart.fixedNumDecimalsForced,
				"is_decimal_separator_comma":		this.chart.decimalSeparatorComma,
				"is_thousand_separator_disabled":	this.chart.thousandSeparatorDisabled
			};
		},
		loadData: function(data,append)
		{
			if (!append)
			{
				//Hard reset of the data array to avoid memory leaks.
				$PWT.GC.object(this.data);
				delete this.data;
				this.data=[];
			}
			if (Object.isArray(data))
			{
				for (var i=0,j=data.length; i<j; i++)
				{
					if (Object.isDefined(data[i].className) && data[i].className=='RecordSet')
					{
						data[i].observe('onChange',this.onChange.bind(this));
						this.data.push(data[i]);
					}
					else
					{
						if (Object.isUndefined(data[i].type) && !Object.isNull(this.config.type))
						{
							data[i].type=this.config.type;
						}
						this.data.push(new $PWT.chart.OFC.RecordSet(data[i]));
						this.data.last().observe('onChange',this.onChange.bind(this));
					}
				}
			}
			else
			{
				if (Object.isDefined(data.className) && data.className=='RecordSet')
				{
					data.observe('onChange',this.onChange.bind(this));
					this.data.push(data);
				}
				else
				{
					if (Object.isUndefined(data.type) && !Object.isNull(this.config.type))
					{
						data.type=this.config.type;
					}
					this.data.push(new $PWT.chart.OFC.RecordSet(data));
					this.data.last().observe('onChange',this.onChange.bind(this));
				}
			}
			this.onChange(data,append);
		},
		getData: function()
		{
			return this.data;
		},
		getDataItem: function(index)
		{
			return this.data[index];
		},
		setTitle: function(title,style)
		{
			this.setTitle.$parent(title);
			if (style)this.setTitleStyle(style);
			this.onChange(style);
			return this;
		},
		setTitleStyle: function(style)
		{
			this.chart.titleStyle=style;
			this.onChange(style);
			return this;
		},
		setBGColor: function(bgColor)
		{
			this.chart.bgColor=bgColor;
			this.onChange(bgColor);
			return this;
		},
		setTooltip: function(tooltip)
		{
			$PWT.GC.object(this.chart.tooltip);
			delete this.chart.tooltip;
			if (Object.isAssocArray(tooltip))
			{
				if (Object.isDefined(tooltip.className) && tooltip.className=='Tooltip')
				{
					this.chart.tooltip=tooltip;
				}
				else
				{
					this.chart.tooltip=new $PWT.chart.OFC.Tooltip(tooltip);
				}
			}
			else if (Object.isString(tooltip))
			{
				this.chart.tooltip=new $PWT.chart.OFC.Tooltip({tip:tooltip});
			}
			else
			{
				this.chart.tooltip=new $PWT.chart.OFC.Tooltip({disabled:!Boolean(tooltip)});
			}
			this.chart.tooltip.observe('onChange',this.onChange.bind(this));
		},
		getTooltip: function()
		{
			return this.chart.tooltip;
		},
		setLegend: function(legend)
		{
			$PWT.GC.object(this.chart.legend);
			delete this.chart.legend;
			if (Object.isAssocArray(legend))
			{
				if (Object.isDefined(legend.className) && legend.className=='Legend')
				{
					this.chart.legend=legend;
				}
				else
				{
					this.chart.legend=new $PWT.chart.OFC.Legend(legend);
				}
			}
			else
			{
				this.chart.legend=new $PWT.chart.OFC.Legend({disabled:!Boolean(legend)});
			}
			this.chart.legend.observe('onChange',this.onChange.bind(this));
		},
		getLegend: function()
		{
			return this.chart.legend;
		},
		setXAxis: function(axis)
		{
			$PWT.GC.object(this.chart.xAxis);
			delete this.chart.xAxis;
			if (Object.isAssocArray(axis))
			{
				if (Object.isDefined(axis.className) && axis.className=='X')
				{
					this.chart.xAxis=axis;
				}
				else
				{
					this.chart.xAxis=new $PWT.chart.OFC.Axis.X(axis);
				}
			}
			else
			{
				this.chart.xAxis=new $PWT.chart.OFC.Axis.X();
			}
			this.chart.xAxis.observe('onChange',this.onChange.bind(this));
		},
		getXAxis: function()
		{
			return this.chart.xAxis;
		},
		setLeftYAxis: function(axis)
		{
			$PWT.GC.object(this.chart.yAxisLeft);
			delete this.chart.yAxisLeft;
			if (Object.isAssocArray(axis))
			{
				if (Object.isDefined(axis.className) && axis.className=='Y')
				{
					this.chart.yAxisLeft=axis;
				}
				else
				{
					this.chart.yAxisLeft=new $PWT.chart.OFC.Axis.Y(axis);
				}
			}
			else
			{
				this.chart.yAxisLeft=new $PWT.chart.OFC.Axis.Y();
			}
			this.chart.yAxisLeft.observe('onChange',this.onChange.bind(this));
		},
		getLeftYAxis: function()
		{
			return this.chart.yAxisLeft;
		},
		setRightYAxis: function(axis)
		{
			$PWT.GC.object(this.chart.yAxisRight);
			delete this.chart.yAxisRight;
			if (Object.isAssocArray(axis))
			{
				if (Object.isDefined(axis.className) && axis.className=='Y')
				{
					this.chart.yAxisRight=axis;
				}
				else
				{
					this.chart.yAxisRight=new $PWT.chart.OFC.Axis.Y(axis);
				}
			}
			else
			{
				this.chart.yAxisRight=new $PWT.chart.OFC.Axis.Y({disabled:!Boolean(axis)});
			}
			this.chart.yAxisRight.observe('onChange',this.onChange.bind(this));
		},
		getRightYAxis: function()
		{
			return this.chart.yAxisRight;
		},
		setYLegendText: function(text)
		{
			this.chart.yLegendText=text;
			this.onChange(text);
			return this;
		},
		getYLegendText: function()
		{
			return this.chart.yLegendText;
		},
		setYLegendStyle: function(style)
		{
			this.chart.yLegendStyle=style;
			this.onChange(style);
			return this;
		},
		getYLegendStyle: function()
		{
			return this.chart.yLegendStyle;
		},
		setNumDecimals: function(num)
		{
			this.chart.numDecimals=num;
			this.onChange(num);
			return this;
		},
		getNumDecimals: function()
		{
			return this.chart.numDecimals;
		},
		setFixedNumDecimalsForced: function(is)
		{
			this.chart.fixedNumDecimalsForced=Boolean(is);
			this.onChange(is);
			return this;
		},
		getFixedNumDecimalsForced: function()
		{
			return this.chart.fixedNumDecimalsForced;
		},
		setDecimalSeparatorComma: function(is)
		{
			this.chart.decimalSeparatorComma=Boolean(is);
			this.onChange(is);
			return this;
		},
		getDecimalSeparatorComma: function()
		{
			return this.chart.decimalSeparatorComma;
		},
		setThousandSeparatorDisabled: function(is)
		{
			this.chart.thousandSeparatorDisabled=Boolean(is);
			this.onChange(is);
			return this;
		},
		getThousandSeparatorDisabled: function()
		{
			return this.chart.thousandSeparatorDisabled;
		},
		disableRefresh: function()
		{
			this._disableRefresh=true;
		},
		enableRefresh: function()
		{
			this._disableRefresh=false;
		},
		isRefreshEnabled: function()
		{
			return !this._disableRefresh;
		}
	}
);