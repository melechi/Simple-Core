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

/**
 * @class $PWT
 * //Description
 * 
 * 
 * 
 * @author Timothy Chandler tim@petim.com.au
 * @version 1.0.0
 */
/**
 * Shorthand for document.getElementById().
 * 
 * @param {String, Element} [element] The ID of the element to fetch.
 * @return {Element} element.
 */
$=function(element)
{
	if (Object.isElement(element))return element;
	return document.getElementById(element);
}
/**
 * Converts array-like collections into an Array object.
 * 
 * Accepts an array-like collection (anything with numeric indices) and returns its equivalent as an actual Array object
 * 
 * @param {Mixed} iterable Accepts virtually anything and converts it into an array.
 * @return {Array} results
 * 
 * @credit http://www.prototypejs.org/api/utility/dollar-a
 */
$A=function(iterable)
{
	if (!iterable)return [];
	if (!(typeof iterable=='function' && iterable=='[object NodeList]') && iterable.toArray)return iterable.toArray();
	var	length=iterable.length || 0,
		results=new Array(length);
	while (length--)results[length]=iterable[length];
	return results;
};
var $PWT=
{
	version:	'1.1.0',
//	ready:		false,
//	basePath:	'',
	emptyFunction:	function(){},
	init:			function()
	{
		delete this.init;
	},
	/**
	 * Use this function to create a namespace.
	 * 
	 * This function will safely create a namespace and return it for use.
	 * Use this function before adding anything to a namespaced object to assure
	 * it exists and you won't suffer data loss.
	 * 
	 * @member $PWT
	 * @param {String} namespace A string representation of the namespace to be created.
	 * @return {Object} namespace
	 */
	namespace:(function()
	{
		var validIdentifier=/^(?:[\$a-zA-Z_]\w*[.])*[\$a-zA-Z_]\w*$/;
		function inOrderDescend(t,initialContext)
		{
			var i,N;
			if (Object.isString(t))
			{
				var context,parts;
				if (!validIdentifier.test(t))
				{
					throw new Error('"'+t+'" is not a valid name for a package.');
				}
				context	=initialContext;
				parts	=t.split('.');
				for (var i=0,N=parts.length;i<N;i++)
				{
					t			=parts[i];
					context[t]	=context[t] || {};
					context		=context[t];
				}
				return context;
			}
			else
			{
				throw new TypeError();
			}
		}
		return function(spec,context)
		{
			return inOrderDescend(spec, context||window);	
		};
	})(),
	/**
	 * Imports packagesinto a localized scope.
	 * 
	 * This function simulates packages by allowing you to import a packages
	 * into a localized scope. This can also be thought of as importing
	 * a set of namespaces which can then be aliased (maintaining reference)
	 * in the localized scope.
	 * 
	 * @member $PWT
	 * @param {Mixed} arguments Accepts unlimited arguments to be imported.
	 * @return {Function} Function which accepts another function to act as the locaized scope.
	 */
	usingPackage: function()
	{
		var args=arguments;
		return function(inner){return inner.apply(args[0],args);};
	},
	/**
	 * 
	 * 
	 */
	Class:
	{
		/**
		 * Creates a new class.
		 * 
		 * 
		 * 
		 * @memberOf $PWT.Class
		 * @param {Object,String} [definition] Definition object which takes up to four parameters.
		 * $namespace - Defines the namespace which the class will be created in.
		 * $name - Defines the name of the class. This will be created within the specified namespace.
		 * $interfaces - Defines a single or an array of interfaces in which the class must conform to.
		 * $traits - Defines a single or an array of traits which the class will have.
		 * @return {Function} Returns a function which consumes the class body.
		 */
		create: function(definition)
		{
			if (typeof definition=='string')
			{
				var namespace=window;
				var className=definition;
			}
			else
			{
				if (Object.isUndefined(definition.$name))
				{
					throw Error('Class name must be defined.');
				}
				else
				{
					var className=definition.$name;
					if (Object.isUndefined(definition.$namespace))
					{
						var namespace=window;
					}
					else
					{
						var namespace=$PWT.namespace(definition.$namespace);
					}
				}
			}
			//When a new instance of the class is created, this is where it all begins.
			namespace[className]=function()
			{
				if (definition.$abstract)
				{
					throw '"'+className+'" is an abstract class and cannot be directly initiated.';
				}
				//The first and most important thing to do is to make a clone of the class prototype.
				var prototype=Object.clone(namespace[className].prototype);
				//Make arguments easier to work with.
				var args=$A(arguments);
				//Handle trait related initiation functionality.
				if (namespace[className].prototype.__behaviors.length)
				{
					for (var i=0,j=namespace[className].prototype.__behaviors.length; i<j; i++)
					{
						var thisArg=args.shift();
						namespace[className].prototype.__behaviors[i].call(this,thisArg);
					}
				}
				//Now restore the class prototype back to its original form.
				namespace[className].prototype=prototype;
				//Private Extension Functions
				var processExtensions=function(scope,definition)
				{
					var doNextExtend=function(scope,object,$parent,extension,args)
					{
						//Save the old $parent in a temp var.
						var tmp=scope[object].$parent;
						//Now override this method with a new one.
						//- This is done so that IF this method calls ITS parent method, it won't recusively call the same method.
						scope[object].$parent=(function(scope,$parent)
						{
							return function()
							{
								var ret=null;
								if (!Object.isUndefined(extension.definition.$extends))
								{
									var ext			=extension.definition.$extends,
										gotExtension=false;
									while (1)
									{
										if (Object.isFunction(ext.prototype[object])
										&& scope[object]!==ext.prototype[object])
										{
											gotExtension=true;
											ret=doNextExtend(scope,object,$parent,ext,arguments);
											break;
										}
										else if (!Object.isUndefined(ext.definition.$extends))
										{
											ext=ext.definition.$extends;
										}
										else
										{
											break;
										}
									}
								}
								if (!gotExtension)
								{
									ret=$parent.apply(scope,arguments);
								}
								return ret;
							}
						})(scope,extension.prototype[object]);
						var ret=$parent.apply(scope,args);
						//Restore the old $parent method.
						scope[object].$parent=tmp;
						return ret;
					}
					var doExtend=function(scope,object,extension)
					{
						var func=scope[object];
						scope[object]=function()
						{
							//Setup this method's parent.
							scope[object].$parent=(function(scope,$parent)//$parent=Parent method that needs to be called.
							{
								//Returns a function which returns that result of the $parent method after being called with the correct scope.
								return function()
								{
									var ret=null;
									//If this parent method has a parent, it too needs to be setup. - This should be recusive from here on out.
									if (!Object.isUndefined(extension.definition.$extends))
									{
										var ext			=extension.definition.$extends,
											gotExtension=false;
										while (1)
										{
											if (Object.isFunction(ext.prototype[object])
											&& scope[object]!==ext.prototype[object])
											{
												gotExtension=true;
												ret=doNextExtend(scope,object,$parent,ext,arguments);
												break;
											}
											else if (!Object.isUndefined(ext.definition.$extends))
											{
												ext=ext.definition.$extends;
											}
											else
											{
												break;
											}
										}
									}
									if (!gotExtension)
									{
										ret=$parent.apply(scope,arguments);
									}
									return ret;
								}
							})(scope,extension.prototype[object]);
							//Execute this method.
							return func.apply(scope,arguments);
						}
					}
					if (!Object.isUndefined(definition.$extends))
					{
						for (object in definition.$extends.prototype)
						{
							//Immidiate Parents.
							if (Object.isFunction(scope[object])
							&& Object.isFunction(definition.$extends.prototype[object])
							&& scope[object]!==definition.$extends.prototype[object])
							{
								doExtend(scope,object,definition.$extends);
							}
							//
							else if (Object.isFunction(scope[object]))
							{
								var extension=definition.$extends;
								while (1)
								{
									if (Object.isFunction(extension.prototype[object])
									&& scope[object]!==extension.prototype[object])
									{
										doExtend(scope,object,extension);
										break;
									}
									else if (!Object.isUndefined(extension.definition.$extends))
									{
										extension=extension.definition.$extends;
									}
									else
									{
										break;
									}
								}
							}
						}
					}
				}
				if (!Object.isUndefined(namespace[className].definition.$extends))
				{
					processExtensions(this,namespace[className].definition);
				}
				delete processExtensions;
				//Finally, initiate the class.
				if (Object.isFunction(this.init))
				{
					this.init.apply(this,args);
				}
			};
			return function(classBody)
			{
				//Do the extending first, because the new class body and traits will overwrite existing methods and properties.
				if (typeof definition.$extends!='undefined')
				{
					if (definition.$extends==undefined)
					{
						throw new Error('Unable to extend class. Class to extend from is undefined.');
					}
					else if (typeof definition.$extends.prototype.interfaceName!='undefined')
					{
						throw new Error('Unable to extend class from interface. Use $implements followed by interface.');
					}
					else if (typeof definition.$extends.prototype.traitName!='undefined')
					{
						throw new Error('Unable to extend class from trait. Use $traits followed by trat.');
					}
					else if (typeof definition.$extends.prototype.className=='undefined')
					{
						throw new Error('Unable to extend class. Class to extend from is not an instance of "$PWT.Class".');
					}
					else
					{
						namespace[className].prototype=Object.extend(namespace[className].prototype,definition.$extends.prototype);
					}
				}
				//Normalize the traits before adding them so that the normalized
				//trait can be used to validate any interfaces.
				var normalizedTrait=false;
				if (!Object.isArray(namespace[className].prototype.__behaviors))namespace[className].prototype.__behaviors=[];
				if (!Object.isUndefined(definition.$traits))
				{
					if (!Object.isArray(definition.$traits))definition.$traits=[definition.$traits];
					normalizedTrait=$PWT.Trait.normalize(definition.$traits);
					for (var i=0,j=definition.$traits.length; i<j; i++)
					{
						if (Object.isFunction(definition.$traits[i]) && Object.isFunction(definition.$traits[i].prototype.init))
						{
							namespace[className].prototype.__behaviors.push(definition.$traits[i].prototype.init);
						}
					}
					if (!Object.isArray(definition.$traits))definition.$traits=[definition.$traits];
				}
				//Handle implementation of interfaces.
				if (!Object.isUndefined(definition.$implements))
				{
					if (!Object.isArray(definition.$implements))definition.$implements=[definition.$implements];
					for (var i=0,j=definition.$implements.length; i<j; i++)
					{
						if ($PWT.Interface.validate(definition.$implements[i]))
						{
							$PWT.Interface.add(namespace[className],classBody,normalizedTrait,definition.$implements[i]);
						}
					}
				}
				//Handle adding traits.
				if (!Object.isUndefined(definition.$traits))
				{
					if ($PWT.Trait.validate(normalizedTrait))
					{
						$PWT.Trait.add(namespace[className],normalizedTrait);
					}
				}
				delete normalizedTrait;
				//Record the class name.
				namespace[className].prototype.className=className;
				//Now apply the new class items.
				for (var item in classBody)
				{
					if (Object.isFunction(classBody[item]))
					{
						if (classBody[item]===$PWT.Class.ABSTRACT_METHOD)
						{
							if (!definition.$abstract)
							{
								throw '"'+className+'" contains one or more abstract methods and must be declared as abstract.';
							}
							else
							{
								namespace[className].prototype[item]=Object.clone(classBody[item]);
							}
						}
						if (Object.isFunction(namespace[className].prototype[item]))
						{
							var tmp=namespace[className].prototype[item];
							namespace[className].prototype[item]=classBody[item];
							namespace[className].prototype[item].$parent=tmp;
						}
						//TODO: Clean up this duplacated code.
						else
						{
							if (Object.isAssocArray(classBody[item]))
							{
								namespace[className].prototype[item]=Object.clone(classBody[item]);
							}
							else
							{
								namespace[className].prototype[item]=classBody[item];
							}
						}
					}
					else
					{
						if (Object.isAssocArray(classBody[item]))
						{
							namespace[className].prototype[item]=Object.clone(classBody[item]);
						}
						else
						{
							namespace[className].prototype[item]=classBody[item];
						}
					}
				}
				for (var item in namespace[className].prototype)
				{
					if (Object.isFunction(namespace[className].prototype[item])
					&& namespace[className].prototype[item]===$PWT.Class.ABSTRACT_METHOD
					&& !definition.$abstract)
					{
						throw [
							'"'+item+'" is an abstract method and so must either be defined or the class ',
							' "'+className+'" must be declared as abstract by defining',
							' $abstract:true in the class definition.'
						].join('')
					}
				}
				namespace[className].definition=definition;
			}
		},
		ABSTRACT_METHOD: function(){}
	},
	/**
	 * 
	 */
	Interface:
	{
		/**
		 * 
		 * 
		 * 
		 * 
		 * 
		 */
		create: function(definition)
		{
			if (Object.isString(definition))
			{
				var namespace=window;
				var interfaceName=definition;
			}
			else
			{
				if (Object.isUndefined(definition.$name))
				{
					throw Error('Interface name must be defined.');
				}
				else
				{
					var interfaceName=definition.$name;
					if (Object.isUndefined(definition.$namespace))
					{
						var namespace=window;
					}
					else
					{
						var namespace=$PWT.namespace(definition.$namespace);
					}
				}
			}
			namespace[interfaceName]=function()
			{
				throw [
					'Interface "'+interfaceName+'" cannot be initiated because interfaces',
					' are abstract classes which cannot be directly initiated.'
				].join('');
			}
			//Record the interface name.
			namespace[interfaceName].prototype.interfaceName=interfaceName;
			return function(interfaceBody)
			{
				if (!Object.isUndefined(definition.$extends))
				{
					if (!Object.isUndefined(definition.$extends.prototype.traitName))
					{
						throw new Error('Interfaces cannot extend Traits.');
					}
					else if (!Object.isUndefined(definition.$extends.prototype.className))
					{
						throw new Error('Interfaces cannot extend Classes.');
					}
					else
					{
						namespace[interfaceName].prototype=Object.extend(namespace[interfaceName].prototype,definition.$extends.prototype);
					}
				}
				if (!Object.isUndefined(definition.$implements))
				{
					throw new Error('Interfaces cannot implement other Interfaces. Interfaces can only extend other Interfaces.');
				}
				for (var item in interfaceBody)
				{
					if (Object.isFunction(interfaceBody[item])
					&& interfaceBody[item]!=$PWT.Interface.METHOD)
					{
						throw new Error('Interfaces may only contain empty functions. Use $PWT.Interface.METHOD.');
					}
					else
					{
						namespace[interfaceName].prototype[item]=interfaceBody[item];
					}
				}
			}
		},
		PROPERTY:	function(){},
		METHOD: 	function(){},
		validate: function(thisInterface)
		{
			if (Object.isUndefined(thisInterface))
			{
				throw new Error('Unable to implement interface. Interface to implement is undefined.');
			}
			else if (!Object.isUndefined(thisInterface.prototype.className))
			{
				throw new Error
				(
					[
						'Unable to implement interface. Interface to implement is a "$PWT.Class".',
						' An interface must be a "$PWT.Interface".'
					]
				);
			}
			else if (!Object.isUndefined(thisInterface.prototype.traitName))
			{
				throw new Error
				(
					[
						'Unable to implement interface. Interface to implement is a "$PWT.Trait".',
						' An interface must be a "$PWT.Interface".'
					]
				);
			}
			else if (Object.isUndefined(thisInterface.prototype.interfaceName))
			{
				throw new Error('Unable to implement interface. Interface to implement is not an instance of "$PWT.Interface".');
			}
			return true;
		},
		add: function(Class,classBody,trait,thisInterface)
		{
			for (var item in thisInterface.prototype)
			{
				if (item!='interfaceName')
				{
					if (thisInterface.prototype[item]==$PWT.Interface.METHOD)
					{
						if (!Object.isFunction(classBody[item]) && !(trait && Object.isFunction(trait.prototype[item])))
						{
							throw new Error
							(
								[
									'The implementation of interface "'+thisInterface.prototype.interfaceName+'" requires',
									' the method "'+item+'" be implemented.'
								].join('')
							);
						}
					}
					if (Object.isAssocArray(thisInterface.prototype[item]))
					{
						Class.prototype[item]=Object.clone(thisInterface.prototype[item]);
					}
					else
					{
						Class.prototype[item]=thisInterface.prototype[item];
					}
				}
			}
		}
	},
	Trait:
	{
		create: function(definition)
		{
			if (typeof definition=='string')
			{
				var namespace=window;
				var traitName=definition;
			}
			else
			{
				if (typeof definition.$name=='undefined')
				{
					throw Error('Trait name must be defined.');
				}
				else
				{
					var traitName=definition.$name;
					if (typeof definition.$namespace=='undefined')
					{
						var namespace=window;
					}
					else
					{
						var namespace=$PWT.namespace(definition.$namespace);
					}
				}
			}
			namespace[traitName]=function()
			{
				throw [
					'Trait "'+traitName+'" cannot be initiated because traits',
					' are abstract classes which cannot be directly initiated.'
				].join('');
			}
			//Record the trait name.
			namespace[traitName].prototype.traitName=traitName;
			return function(traitBody)
			{
				//Normalize the traits before adding them so that the normalized 
				//trait can be used to validate any interfaces.
				var normalizedTrait=false;
				if (!Object.isUndefined(definition.$traits))
				{
					if (!Object.isArray(definition.$traits))definition.$traits=[definition.$traits];
					normalizedTrait=$PWT.Trait.normalize(definition.$traits,true);
				}
				//Handle implementation of interfaces.
				if (!Object.isUndefined(definition.$implements))
				{
					if (!Object.isArray(definition.$implements))definition.$implements=[definition.$implements];
					for (var i=0,j=definition.$implements.length; i<j; i++)
					{
						if ($PWT.Interface.validate(definition.$implements[i]))
						{
							$PWT.Interface.add(namespace[traitName],traitBody,normalizedTrait,definition.$implements[i]);
						}
					}
				}
				//Handle adding traits.
				if (!Object.isUndefined(definition.$traits))
				{
					if ($PWT.Trait.validate(normalizedTrait))
					{
						$PWT.Trait.add(namespace[traitName],normalizedTrait);
					}
				}
				delete normalizedTrait;
				
				
				
//				//Handle adding of other traits to this one.
//				if (typeof definition.$traits!='undefined')
//				{
//					//Only bother noramalizing the traits if we are dealing with an array of traits.
//					if (Object.isArray(definition.$traits))
//					{
//						var normalizedTrait=$PWT.Trait.normalize(definition.$traits);
//					}
//					else
//					{
//						var normalizedTrait=definition.$traits;
//					}
//					if ($PWT.Trait.validate(normalizedTrait))
//					{
//						$PWT.Trait.add(namespace[traitName],normalizedTrait);
//					}
//					delete normalizedTrait;
//				}
				for (var item in traitBody)
				{
					if (typeof traitBody[item]!='function')
					{
						throw new Error('A trait may only contain methods.');
					}
					else
					{
						namespace[traitName].prototype[item]=traitBody[item];
					}
				}
			}
		},
		normalize: function(traits,preserveInit)
		{
			//If traits is not an array, make it one so its easier to deal with.
			if (!Object.isArray(traits))traits=[traits];
			//Pluck out the conflict resultion object if present.
			var conflictResolutions={};
			if (!Object.isFunction(traits[traits.length-1]))
			{
				conflictResolutions=traits[traits.length-1];
				delete traits[traits.length-1];
			}
			//Now loop through each trait and see if there are any conflicts.
			var normalizedTrait					=function(){};
			normalizedTrait.prototype.traitName	='Normalized';
			var methods							=[];
			var resolved						=[];
			for (var i=0,j=traits.length; i<j; i++)
			{
				if (Object.isFunction(traits[i]))
				{
					//Loop through each method in this trait.
					for (var method in traits[i].prototype)
					{
						//We only want to deal with methods.
						if (!Object.isFunction(traits[i].prototype[method]))	continue;
						//Skip init if present and preserveInit=false.
						if (method=='init' && !preserveInit)					continue;
						//If the method is not in the metods array, all is well.
						if (!methods.inArray(method))
						{
							//Add the method name to the methods array.
							methods.push(method);
							//Add the method it to the normalized trait.
							normalizedTrait.prototype[method]=traits[i].prototype[method];
						}
						//However if a method IS in the methods array, then we need to check for a resolution.
						else
						{
							//This check assures that conflicts are only delt with once.
							if (!resolved.inArray(method))
							{
								//If there is no conflict resolution defined for this method,
								// delete the method from the normalized trait but NOT from the methods array.
								if (Object.isUndefined(conflictResolutions[method]))
								{
									delete normalizedTrait.prototype[method];
								}
								//Else we replace the method in the normalized trait with the resolved one.
								else
								{
									normalizedTrait.prototype[method]=conflictResolutions[method].prototype[method];
								}
								//Mark this conflict as resolved.
								resolved.push(method);
							}
						}
					}
				}
			}
			return normalizedTrait;
		},
		validate: function(thisTrait)
		{
			if (Object.isUndefined(thisTrait))
			{
				throw new Error('Unable to add trait. Trait to add is undefined.');
			}
			else if (!Object.isUndefined(thisTrait.prototype.className))
			{
				throw new Error
				(
					[
						'Unable to add trait. Trait to add is an instance of "$PWT.Class".',
						' A trait must be an instance of "$PWT.Trait".'
					]
				);
			}
			else if (!Object.isUndefined(thisTrait.prototype.instanceName))
			{
				throw new Error
				(
					[
						'Unable to add trait. Trait to add is an instance of "$PWT.Interface".',
						' A trait must be an instance of "$PWT.Trait".'
					]
				);
			}
			else if (Object.isUndefined(thisTrait.prototype.traitName))
			{
				throw new Error('Unable to add trait. Trait to add is not an instance of "$PWT.Trait".');
			}
			return true;
		},
		add: function(Class,thisTrait)
		{
			for (var item in thisTrait.prototype)
			{
				if (item!='traitName')
				{
					if (!Object.isFunction(thisTrait.prototype[item]))
					{
						throw new Error
						(
							
							[
								'Attempt to add trait to class instance failed because trait contained an entity that was not a method.',
								' Traits may only contain methods.'
							].join('')
						);
					}
					Class.prototype[item]=thisTrait.prototype[item];
				}
			}
		}
	},
	/**
	 * Executes a callback function when the DOM has been fully loaded.
	 * 
	 * @member $PWT
	 * @param {Function} callback The function to be executed when the DOM is ready.
	 * @return {Void}
	 */
	onReady:	function(callback)
	{
		var _timer=setInterval
		(
			function()
			{
				if (/loaded|complete/.test(document.readyState))
				{
					clearInterval(_timer);
					if (Object.isFunction(callback))callback();
				}
			},
			10
		);
	},
	util:
	{
		include: function(filePath,type,callback)
		{
			var alreadyIncluded=false;
			if (type=='js')
			{
				$A(document.getElementsByTagName('script')).each
				(
					function(script)
					{
						if (script.src.replace('app:/','')==filePath && /loaded|complete/.test(script.readyState))
						{
							alreadyIncluded=true;
							if (Object.isFunction(callback))callback();
							throw $break;
						}
					}
				);
				if (!alreadyIncluded)
				{
					var newScript		=document.createElement('script');
					newScript.type		='text/javascript';
					newScript.src		=filePath;
					newScript.onload	=function(){if (Object.isFunction(callback))callback();}
					document.getElementsByTagName('head')[0].appendChild(newScript);
				}
			}
			else if (type=='css')
			{
				$A(document.getElementsByTagName('link')).each
				(
					function(script)
					{
						if (script.href.replace('app:/','')==filePath)
						{
							alreadyIncluded=true;
							if (Object.isFunction(callback))callback();
							throw $break;
						}
					}
				);
				if (!alreadyIncluded)
				{
					var newCSS		=document.createElement('link');
					newCSS.rel		='stylesheet';
					newCSS.type		='text/css';
					newCSS.href		=filePath;
					newCSS.onload	=function(){if (Object.isFunction(callback))callback();}
					document.getElementsByTagName('head')[0].appendChild(newCSS);
				}
			}
		},
		random: function(inOptions)
		{
			var options=
			{
				min:	10,
				max:	20,
				chars:
				[
					0,1,2,3,4,5,6,7,8,9,
					'a','b','c','d','e','f','g',
					'h','i','j','k','l','m','n',
					'o','p','q','r','s','t','u',
					'v','w','x','y','z'
				]
			}
			Object.extend(options,inOptions);
			if (Object.isString(options.chars))
			{
				options.chars=$A(options.chars);
			}
			else if (Object.isNumber(options.chars))
			{
				eval('options.chars="'+options.chars+'"');
				options.chars=$A(options.chars);
			}
			var numChars=0;
			while (numChars<options.min)
			{
				numChars=Math.round(Math.random()*options.max);
			}
			var $return='';
			for (var i=0; i<numChars; i++)
			{
				$return+=options.chars[Math.round(Math.random()*options.chars.length)];
			}
			return $return;
		},
		__nextID:	0,
		id: function()
		{
			this.__nextID++;
			return 'PWT-'+this.__nextID;
		}
	}
}