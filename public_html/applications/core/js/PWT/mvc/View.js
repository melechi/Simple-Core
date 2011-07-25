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

$PWT.Class.create
(
	{
		$namespace:		'$PWT.mvc',
		$name:			'View',
		$traits:		$PWT.trait.Observable
	}
)
(
	{
		events:
		{
			onBeforeInit:			true,
			onAfterInit:			true,
			onAttachController:		true,
			onAfterAttachController:true
		},
		model:		null,
		controller:	null,
		init: function(model)
		{
			this.model=model;
			if (Object.isFunction(this.initView))
			{
				this.fireEvent('onBeforeInit',this);
				this.initView();
			}
			if (Object.isString(this.controller)
			|| Object.isBoolean(this.controller)
			|| Object.isAssocArray(this.controller))
			{
				if (Object.isString(this.controller))
				{
					this.attachController(this.controller,this.controller);
				}
				else if (Object.isBoolean(this.controller))
				{
					if (this.controller)
					{
						this.attachController(this.className+'_default','default');
					}
				}
				else
				{
					if (Object.isUndefined(this.controller.id))
					{
						throw 'Undefined controller ID!';
					}
					else
					{
						this.attachController
						(
							this.controller.id,
							(!Object.isUndefined(this.controller.name))?this.controller.name:this.controller.id,
							(!Object.isUndefined(this.controller.listeners))?this.controller.listeners:{}
						);
					}
				}
			}
			this.fireEvent('onAfterInit',this);
		},
		getModel: function()
		{
			return this.model;
		},
		getController: function()
		{
			return this.controller;
		},
		attachController: function(controllerID,controllerName,listeners)
		{
			listeners=Object.clone(listeners);
			this.fireEvent('onAttachController',this,controllerID,controllerName,listeners);
			listeners=Object.extend
			(
				listeners,
				{
					onAfterInit: function(controller)
					{
						this.controller=controller;
						this.fireEvent('onAfterAttachController',this,this.controller);
					}.bind(this)
				}
			);
			if (controllerName!='default')
			{
				this.model.newController(controllerID,controllerName,this,listeners);
			}
			else
			{
				this.model.newDefaultController(controllerID,controllerName,this,listeners);
			}
		},
		//TODO: applyTemplate
//		applyTemplate: function(template,fNr)
//		{
//			//return new Ext.XTemplate(this.getModel().loadTemplate(template)).apply(fNr);
//			//return new Ext.Template(this.getModel().loadTemplate(template)).apply(fNr);
//			return petim.air.Template.parse(this.getModel().loadTemplate(template),fNr);
//		}
	}
);