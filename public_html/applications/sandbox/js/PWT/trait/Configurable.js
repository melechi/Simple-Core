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
		$name:			'Configurable',
		$implements:	$PWT.iface.Configurable
	}
)
(
	{
		init: function(config)
		{
			Object.extend(this.config,config);
		}
	}
);