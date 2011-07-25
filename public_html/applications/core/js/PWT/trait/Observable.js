/*
 * Petim Web Tools v1.0
 * Copyright(c) 2008, Petim Pty. Ltd.
 * licensing@petim.com.au
 * 
 * See packaged license.txt
 * OR URL:
 * http://www.petim.com.au/products/pwt/license/view/
 * for full license terms.
 */

$PWT.Trait.create
(
	{
		$namespace:		'$PWT.trait',
		$name:			'Observable',
		$implements:	$PWT.iface.Observable
	}
)
(
	{
		init:		function(listeners)
		{
			for (var listener in listeners)
			{
				this.observe(listener,listeners[listener]);
			}
		},
		observe:	function(type,listener)
		{
			if (!Object.isArray(this.events[type]))this.events[type]=[];
//			listener.times=0;
			this.events[type].push(listener);
			this.events[type].last().times=0;
			return this;
		},
		observeOnce: function(type,listener)
		{
			return this.observeTimes.call(this,1,type,listener);
		},
		observeTimes: function(numTimes,type,listener)
		{
			if (!Object.isArray(this.events[type]))this.events[type]=[];
			listener.times=numTimes;
			this.events[type].push(listener);
			return this;
		},
		unobserve: function(type,listener)
		{
			if (!Object.isUndefined(this.events[type]))
			{
				var tmp=[];
				for (var i=0,j=this.events[type].length; i<j; i++)
				{
					if (this.events[type][i]==listener)
					{
						delete this.events[type][i];
					}
					else
					{
						tmp.push(this.events[type][i]);
					}
				}
				this.events[type]=tmp;
				if (!this.events[type].length)this.events[type]=true;
			}
			return this;
		},
		fireEvent: function()
		{
			var	args=$A(arguments),
				type=args.shift();
			if (!Object.isUndefined(this.events[type]) && Object.isArray(this.events[type]))
			{
				for (var i=0,j=this.events[type].length; i<j; i++)
				{
					if (Object.isFunction(this.events[type][i]))
					{
						if (this.events[type][i].apply(this,args)===false)
						{
							return false;
						}
						if (Object.isDefined(this.events[type][i]) && this.events[type][i].times!==0)
						{
							if (--this.events[type][i].times===0)
							{
								this.unobserve(type,this.events[type][i]);
							}
						}
					}
				}
			}
			return true;
		},
		clearEvent: function(type)
		{
			if (!Object.isUndefined(this.events[type]))
			{
				this.events[type]=true;
			}
			return this;
		}
	}
);