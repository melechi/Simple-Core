<?php
class core_event_API_application_main extends ext_direct_server_provider
{
	public function read()
	{
		$data=array();
		foreach (new DirectoryIterator((string)$this->config->path->applications) as $iteration)
		{
			if ($iteration->isDir())
			{
				$file=$iteration->getPath()._.$iteration->getFilename()._.'info.php';
				if (is_file($file))
				{
					include_once($file);
					$className	='application_'.$iteration->getFilename().'_info';
					$info		=new $className();
					$data[]=array
					(
						'reference'						=>$iteration->getFilename(),
						'name'							=>$info->name,
						'description'					=>$info->description,
						'version'						=>$info->version,
						'version_core_min'				=>$info->coreMin,
						'dependencies_applications'		=>$info->dependencies['applications'],
						'dependencies_components'		=>$info->dependencies['components']
					);
				}
			}
		}
		$this->respond
		(
			array
			(
				'success'	=>true,
				'data'		=>$data
			)
		);
	}
	
	
	public function initManagement($application)
	{
		$admin=$this->application->{$application}->activateAdministration();
//		$admin->generateMenu();
		
		$this->respond
		(
			array
			(
				'success'	=>true,
				'data'		=>array
				(
					'modules'=>$admin->getModules()
				)
			)
		);
	}
	
	public function getView($view)
	{
		
	}
	
	public function getController($controller)
	{
		
	}
}
?>