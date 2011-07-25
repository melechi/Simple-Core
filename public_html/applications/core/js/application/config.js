var _='/';
$PWT.namespace('app').config=
{
	debug:			true,
	path:
	{
		libs:		'applications/core/js/',
		app:		'applications/core/js/application/',
		view:		'applications/core/js/application/view/',
		controller:	'applications/core/js/application/controller/',
		script:		'applications/core/js/application/script/',
		sql:		'applications/core/js/application/sql/',
		trait:		'applications/core/js/application/trait/'//,
//		template:	'templates'+_
	},
//	documentsFolder:	'PBPRPG',
//	databaseName:		'pbprpg.db',
	url:
	{
		login:				'http://localhost/prpg/login/',
		logout:				'http://localhost/prpg/logout/',
		register:			'http://localhost/prpg/register/',
		createCharacter:	'http://localhost/prpg/createCharacter/'
	}
}