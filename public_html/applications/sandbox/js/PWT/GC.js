$PWT.Class.create
(
	{
		$namespace:	'$PWT',
		$name:		'GC'
	}
)
(
	{
		object: function(object)
		{
			if (Object.isDefined(object))
			{
				if (Object.isArray(object))
				{
					for (var i=0,j=object.length; i<j; i++)
					{
						if (Object.isArray(object[i]) || Object.isAssocArray(object[i]))
						{
							$PWT.GC.object(object[i]);
						}
						delete object[i];
					}
				}
				else if (Object.isAssocArray(object))
				{
					for (var item in object)
					{
						if (Object.isArray(object[item]) || Object.isAssocArray(object[item]))
						{
							$PWT.GC.object(object[item]);
						}
						delete object[item];
					}
				}
				delete object;
			}
		}
	}
);
//Singleton
$PWT.GC=new $PWT.GC();