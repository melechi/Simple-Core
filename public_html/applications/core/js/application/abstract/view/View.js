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
		$namespace:		'app.view',
		$name:			'View',
		$extends:		$PWT.mvc.View
	}
)
(
	{
		viewport:	null,
		
//		init:		function(model)
//		{
//			
//			this.init.$parent(model);
//		},
		getViewport:function()
		{
			return this.model.viewport;
		}
	}
);