$PWT.Class.create
(
	{
		$namespace:	'$PWT',
		$name:		'When'
	}
)
(
	{
		timer:	null,
		items:
		{
			length:	0
		},
		init:	function()
		{
			this.startTimer();
		},
		startTimer: function()
		{
			if (Object.isNull(this.timer))
			{
				this.timer=window.setInterval
				(
					function()
					{
						for (var item in this.items)
						{
							if (item=='length')continue;
							//Try to execute the condition.
							try
							{
								this[this.items[item].condition](this.items[item]);
							}
							//Catch any exceptions, remove the item, then release the exception so that we don't have this infinately executing.
							catch(e)
							{
								this.removeItem(this.items[item].id);
								throw e;
							}
						}
					}.bind(this),
					100
				);
			}
		},
		stopTimer: function()
		{
			window.clearInterval(this.timer);
			this.timer=null;
		},
		captureCondition: function(condition,id,args)
		{
			var	args		=$A(args)
			if (args.length)
			{
				this.items[id].condition	=condition;
				this.items[id].callback		=args.shift();
				this.items[id].args			=args;
			}
			else
			{
				delete this.removeItem(id);
			}
		},
		addItem: function(scope,object)
		{
			var id=$PWT.util.random();
			this.items[id]=
			{
				id:			id,
				scope:		scope,
				object:		object,
				condition:	null,
				callback:	$PWT.emptyFunction,
				args:		null
			};
			this.items.length++;
			if (this.items.length)
			{
				this.startTimer();
			}
			return id;
		},
		removeItem: function(id)
		{
			delete this.items[id];
			this.items.length--;
			if (!this.items.length)
			{
				this.stopTimer();
			}
		},
		assert: function(result,item)
		{
			if (result)
			{
				item.callback.apply(item.callback,item.args);
				this.removeItem(item.id);
			}
		},
		isDefined: function(item)
		{
			this.assert(Object.isDefined(item.scope[item.object]),item);
		},
		isUndefined: function(item)
		{
			this.assert(Object.isUndefined(item.scope[item.object]),item);
		},
		isTrue: function(item)
		{
			this.assert((item.scope[item.object]===true),item);
		},
		isFalse: function(item)
		{
			this.assert((item.scope[item.object]===false),item);
		},
		isBoolean: function(item)
		{
			this.assert(Object.isBoolean(item.scope[item.object]),item);
		},
		isFunction: function(item)
		{
			this.assert(Object.isFunction(item.scope[item.object]),item);
		},
		isArray: function(item)
		{
			this.assert(Object.isArray(item.scope[item.object]),item);
		},
		isAssocArray: function(item)
		{
			this.assert(Object.isAssocArray(item.scope[item.object]),item);
		},
		isString: function(item)
		{
			this.assert(Object.isString(item.scope[item.object]),item);
		},
		isNumber: function(item)
		{
			this.assert(Object.isNumber(item.scope[item.object]),item);
		},
		isElement: function(item)
		{
			this.assert(Object.isElement(item.scope[item.object]),item);
		},
		isNull: function(item)
		{
			this.assert(Object.isNull(item.scope[item.object]),item);
		},
		isEqualTo: function(item)
		{
			this.assert((item.scope[item.object.object]==item.object.value),item)
		}
	}
);
$PWT.When=new $PWT.When();
$PWT.when=function(scope,object)
{
	var	id		=$PWT.When.addItem(scope,object),
		callee	=arguments.callee,
		chain	=
		{
			andWhen: function()
			{
				if (!arguments.length)
				{
					return callee(scope,object);
				}
				else
				{
					return callee(arguments[0],arguments[1]);
				}
			}
		};
	return {
		isDefined: function()
		{
			$PWT.When.captureCondition('isDefined',id,arguments);
			return chain;
		},
		isUndefined: function()
		{
			$PWT.When.captureCondition('isUndefined',id,arguments);
			return chain;
		},
		isTrue: function()
		{
			$PWT.When.captureCondition('isTrue',id,arguments);
			return chain;
		},
		isFalse: function()
		{
			$PWT.When.captureCondition('isFalse',id,arguments);
			return chain;
		},
		isBoolean: function()
		{
			$PWT.When.captureCondition('isBoolean',id,arguments);
			return chain;
		},
		isFunction: function()
		{
			$PWT.When.captureCondition('isFunction',id,arguments);
			return chain;
		},
		isArray: function()
		{
			$PWT.When.captureCondition('isArray',id,arguments);
			return chain;
		},
		isAssocArray: function()
		{
			$PWT.When.captureCondition('isAssocArray',id,arguments);
			return chain;
		},
		isString: function()
		{
			$PWT.When.captureCondition('isString',id,arguments);
			return chain;
		},
		isNumber: function()
		{
			$PWT.When.captureCondition('isNumber',id,arguments);
			return chain;
		},
		isElement: function()
		{
			$PWT.When.captureCondition('isElement',id,arguments);
			return chain;
		},
		isNull: function()
		{
			$PWT.When.captureCondition('isNull',id,arguments);
			return chain;
		},
		isEqualTo: function()
		{
			$PWT.When.captureCondition('isEqualTo',id,arguments);
			return chain;
		}
	};
}


/*** TESTING ***/


//window.foobar=false;
//$PWT.when(window,'foobar').isTrue
//(
//	function(a,b,c)
//	{
//		console.debug(a,b,c);
//	}.bind(this),
//	'arg1',
//	'arg2',
//	'arg3'
//);
//
//$PWT.Class.create
//(
//	{
//		$namespace:	'foo.bar.baz',
//		$name:		'Foo'
//	}
//)
//(
//	{
//		bar:	null,
//		init:	function()
//		{
//			$PWT.when(this,'bar').isTrue
//			(
//				function(a,b,c)
//				{
//					console.debug('bar has been set to true!');
//					console.debug(this,a,b,c);
//				}.bind(this),
//				'arg1',
//				'arg2',
//				'arg3'
//			);
//			$PWT.when(this,'bar').isFalse
//			(
//				function()
//				{
//					console.debug('bar has been set to false!');
//				}
//			);
//			$PWT.when(this,'baz').isDefined
//			(
//				function()
//				{
//					console.debug('baz has been defined!');
//					console.debug(this.baz);
//				}.bind(this)
//			).andWhen().isDefined
//			(
//				function()
//				{
//					console.debug('baz has been defined![second test(chained)]');
//				}.bind(this)
//			).andWhen().isTrue
//			(
//				function()
//				{
//					console.debug('baz is true!');
//				}.bind(this)
//			);
//			//Simulate adding and removing a variable.
//			window.setTimeout
//			(
//				function()
//				{
//					this.baz='foobarbaz';
//					$PWT.when(this,'baz').isUndefined
//					(
//						function()
//						{
//							console.debug('baz has been deleted!');
//							console.debug(this.baz);
//						}.bind(this)
//					);
//					window.setTimeout
//					(
//						function()
//						{
//							delete this.baz;
//						}.bind(this),
//						2500
//					);
//				}.bind(this),
//				2500
//			);
//		},
//		setBar: function(val)
//		{
//			this.bar=val;
//		},
//		setBaz: function(val)
//		{
//			this.baz=val;
//		}
//	}
//);
//window.foo=new foo.bar.baz.Foo();