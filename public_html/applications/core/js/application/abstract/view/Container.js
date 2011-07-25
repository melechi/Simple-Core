$PWT.Class.create
(
	{
		$namespace:	'app.view',
		$name:		'Container',
		$extends:	$PWT.mvc.View
	}
)
(
	{
		__containerConfig:	{},
		container:			null,
		containerConfig:	{},
		xtype:				'panel',
		item:				{},
		__order:			[],
		order:				[],
		init:				function()
		{//console.debug(this.className,this.__containerConfig);if (this.className=='applications')return;
			this.__containerConfig		=Object.extend(this.__containerConfig,this.containerConfig);
			this.__containerConfig.xtype=this.xtype;
			this.init.$parent.apply(this,arguments);
		},
		createContainer: function()
		{
			if (Object.isNull(this.container))
			{
				this.__containerConfig.items	=this.order;
				this.container					=Ext.ComponentMgr.create(this.__containerConfig);
			}
			return this.container;
		},
		getContainer: function()
		{
			if (!Object.isNull(this.container))
			{
				return this.container;
			}
			else
			{
				return this.createContainer();
			}
		},
		addItem: function(name,component)
		{
			var args=$A(arguments),
				item=[];
			for (var i=0,j=args.length,k=false; i<j; i++,k=!k)
			{
				if (!k)
				{
					item[0]=args[i];
				}
				else
				{
					item[1]=args[i];
					if (Object.isFunction(item[1].add))
					{
						this.item[item[0]]=item[1];
					}
					else
					{
						if (Object.isUndefined(item[1].xtype))item[1].xtype='panel';
						this.item[item[0]]=Ext.ComponentMgr.create(item[1]);
					}
					this.order.push(this.item[item[0]]);
					this.__order.push(item[0]);
					if (!Object.isNull(this.container))
					{
						this.container.add(this.item[item[0]]);
					}
//					$PWT.GC.object(item);
					item=[];
				}
			}
			if (!Object.isNull(this.container))
			{
				this.getModel().redraw();
			}
			return this;
		},
		getItem: function(name)
		{
			return this.item[name];
		},
		getItemOrderNumber: function(name)
		{
			var ret=null;
			for (var i=0,j=this.__order.length; i<j; i++) 
			{
				if (this.__order[i]==name)
				{
					ret=i;
					break;
				}
			}
			return ret;
		}
	}
);