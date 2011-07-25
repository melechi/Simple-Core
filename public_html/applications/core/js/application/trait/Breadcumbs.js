$PWT.Trait.create
(
	{
		$namespace:	'app.trait',
		$name:		'Breadcrumbs'
	}
)
(
	{
		addBreadcrumb: function(crumb,cardLink)
		{
			this.breadcrumbs.push({text:crumb,link:cardLink});
			this.updateBreadcrumbs();
			return this;
		},
		removeBreadcrumb: function(cardLink)
		{
			var newBreadcrumbs=[];
			for (var i=0,j=this.breadcrumbs.length; i<j; i++)
			{
				if (this.breadcrumbs[i].link==cardLink)break;
				newBreadcrumbs.push(this.breadcrumbs[i]);
			}
			this.breadcrumbs=newBreadcrumbs;
			this.updateBreadcrumbs();
			return this;
		},
		changeBreadcrumb: function(cardLink,crumb,newLink)
		{
			for (var i=0,j=this.breadcrumbs.length; i<j; i++)
			{
				if (this.breadcrumbs[i].link==cardLink)
				{
					$PWT.GC.object(this.breadcrumbs[i]);
					this.breadcrumbs[i]={text:crumb,link:newLink};
					break;
				}
			}
			this.updateBreadcrumbs();
			return this;
		},
		updateBreadcrumbs: function()
		{
			var tbar=this.getView().getContainer().getTopToolbar();
			tbar.removeAll();
			tbar.add(['&nbsp;','<b>Location:</b>&nbsp;']);
			for (var i=0,j=1,k=this.breadcrumbs.length; i<k; i++,j++)
			{
				if (j!=k)//Not last Breadcrumb
				{
					tbar.add
					(
						{
							text:		this.breadcrumbs[i].text,
							handler:	function(link,nextCrumb)
							{
								this.setActiveItem(link);
								this.removeBreadcrumb(nextCrumb);
							}.bind(this,this.breadcrumbs[i].link,this.breadcrumbs[j].link)
						},
						'&raquo;'
					);
				}
				else
				{
					tbar.add(this.breadcrumbs[i].text);
				}
			}
			this.getModel().redraw();
			return this;
		}
	}
);