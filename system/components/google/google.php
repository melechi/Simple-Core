<?php
class component_google extends component
{
	private $services=array
	(
		'cl',		//Calendar Data API
		'gbase',	//Google Base Data API
		'blogger',	//Blogger Data API
		'cp',		//Contacts Data API
		'writely',	//Documents List Data API
		'lh2',		//Picasa Web Albums Data API
		'apps',		//Google Apps Provisioning API
		'wise',		//Spreadsheets Data API
		'youtube'	//YouTube Data API
	);
	private $authenticated=false;
	
	public function initiate()
	{
		$this	->branch('blogger')
				->branch('apps');
	}
	
	public function authenticate($email,$password,$serviceName)
	{
		if (!$this->isValidService($serviceName))
		{
			$this->exception('Invalid google service.');
		}
		else
		{
			file_get_contents
			(
				'https://www.google.com/accounts/ClientLogin',
				null,
				stream_context_create
				(
					array
					(
						'http'=>array
						(
							'method'	=>'POST',
							'header'	=>'Content-Type:application/x-www-form-urlencoded',
							'content'	=>array
							(
								'Email'			=>$email,
								'Passwd'		=>$password,
								'service'		=>$serviceName,
								'accountType'	=>'GOOGLE',
								'source'		=>'petim-simplecore-'.$this->parent->version
							)
						)
					)
				)
			);
		}
	}
	
	public function isValidService($serviceName)
	{
		return in_array($serviceName,$this->services);
	}
	
	public function isAuthenticated()
	{
		return $this->authenticated;
	}
}
?>