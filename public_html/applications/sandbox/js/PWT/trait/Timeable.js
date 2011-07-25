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
		$namespace:	'$PWT.trait',
		$name:		'Timeable',
		$traits:	$PWT.trait.Observable
	}
)
(
	{
		startTimer: function()
		{
			if (this.config.timeout)
			{
				this.timeout=this.config.timeout;
				window.clearInterval(this.timer);
				this.timer=window.setInterval
				(
					function()
					{
						this.timeout--;
						if (this.timeout<=0)
						{
							window.clearInterval(this.timer);
							this.fireEvent('onTimeout',this);
						}
					}.bind(this),
					1000
				);
			}
		},
		restartTimer: function()
		{
			this.startTimer();
		},
		pauseTimer: function()
		{
			window.clearInterval(this.timer);
		},
		resumeTimer: function()
		{
			this.startTimer();
		},
		stopTimer: function()
		{
			window.clearInterval(this.timer);
			this.timeout=this.config.timeout;
		},
		resetTimer: function()
		{
			this.timeout=this.config.timeout;
		}
	}
);