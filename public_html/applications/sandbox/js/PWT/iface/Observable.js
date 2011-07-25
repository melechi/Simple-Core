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

$PWT.Interface.create
(
	{
		$namespace: '$PWT.iface',
		$name:		'Observable'
	}
)
(
	{
		events:		[],
		observe:	$PWT.Interface.METHOD,
		unobserve:	$PWT.Interface.METHOD,
		fireEvent:	$PWT.Interface.METHOD
	}
);