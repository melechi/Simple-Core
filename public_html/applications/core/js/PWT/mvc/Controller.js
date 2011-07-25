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
		$name:			'Controller',
		$traits:		$PWT.trait.Observable
	}
)
(
	{
		events:
		{
			onBeforeInit:	true,
			onAfterInit:	true
		},
		model:	null,
		view:	null,
		init: function(model,view)
		{
			this.model	=model;
			this.view	=view;
			this.fireEvent('onBeforeInit',this);
			if (Object.isFunction(this.initController))
			{
				this.initController();
			}
			this.fireEvent('onAfterInit',this);
		},
		getModel: function()
		{
			return this.model;
		},
		getView: function()
		{
			return this.view;
		}
	}
);