$PWT.Class.create
(
	{
		$namespace:		'$PWT.chart',
		$name:			'Chart',
		$traits:		[$PWT.trait.Configurable,$PWT.trait.Observable]
	}
)
(
	{
		config:
		{
			renderTo:	null,
			autoRender:	true,
			swf:		null,
			type:		null,
			width:		300,
			height:		300,
			title:		'',
		},
		events:
		{
			onReady:	true,
			onLoad:		true,
			onChange:	true
		},
		types:			[],
		swf:			'',
		data:			[],
		rendered:		false,
		chartObject:	null,
		chart:
		{
			type:	null,
			width:	null,
			height:	null,
			title:	''
		},
		init:			function()
		{
			if (!this.types.inArray(this.config.type))
			{
				throw 'Invalid type "'+this.config.type+'". This type is not supported.';
			}
			else
			{
				this.chart=Object.extend
				(
					this.chart,
					{
						type:		this.config.type,
						width:		this.config.width,
						height:		this.config.height,
						title:		this.config.title,
					}
				);
				this.fireEvent('onReady',this);
				if (this.config.autoRender)
				{
					this.render();
				}
			}
		},
		render: function()
		{
			//TODO: manage width and height.
			this.rendered=true;
			this.fireEvent.defer(500,this,'onRender',this);
		},
		setTitle: function(title)
		{
			this.chart.title=title;
			this.fireEvent('onTitleChange',this,title);
			return this;
		},
		setWidth: function(width)
		{
			this.chart.width=width;
			if (this.rendered)
			{
				this.chartObject.width=width;
			}
			this.fireEvent('onWidthChange',this,width);
			return this;
		},
		setHeight: function(height)
		{
			this.chart.height=height;
			if (this.rendered)
			{
				this.chartObject.height=height;
			}
			this.fireEvent('onHeightChange',this,height);
			return this;
		},
		setSize: function(width,height)
		{
			if (Object.isAssocArray(width))
			{
				this.chart.width=width.width;
				this.chart.height=width.height;
			}
			else
			{
				this.chart.width=width;
				this.chart.height=height;
			}
			if (this.rendered)
			{
				this.chartObject.width=this.chart.width;
				this.chartObject.height=this.chart.height;
			}
			this.fireEvent('onWidthChange',this,this.chart.width);
			this.fireEvent('onHeightChange',this,this.chart.height);
			return this;
		},
		getChart: function()
		{
			return this.chartObject;
		}
	}
);