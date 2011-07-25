<?php
class core_event_API_component_main extends ext_direct_server_provider
{
	public function read()
	{
		$data=array();
		foreach (new DirectoryIterator((string)$this->config->path->components) as $iteration)
		{
			if ($iteration->isDir())
			{
				if ($iteration->isDot())continue;
				//if (substr($iteration->getFilename(),0,1)=='.')continue;
				if (strstr($iteration->getFilename(),'.'))continue;
				$file=$iteration->getPath()._.$iteration->getFilename()._.'info.php';
				if (!is_file($file))
				{
					$this->createInfoFile($iteration->getFilename(),$iteration->getPath()._.$iteration->getFilename()._);
				}
				include_once($file);
				$className='component_'.$iteration->getFilename().'_info';
				$info=new $className();
				$data[]=array
				(
					'reference'						=>$iteration->getFilename(),
					'name'							=>$info->name,
					'description'					=>$info->description,
					'version'						=>$info->version,
					'version_core_min'				=>$info->coreMin,
					'dependencies_components'		=>$info->dependencies['components']
				);
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
	
	
	
	
	private function createInfoFile($name,$dir)
	{
		$template=<<<TEMPLATE
<?php
class component_{CLASSNAME}_info
{
	public \$name		='{NAME}';
	public \$description	='{DESCRIPTION}';
	public \$version		='{VERSION}';
	public \$coreMin		='{VERSION_CORE_MIN}';
	public \$dependencies=array
	(
		'components'	=>array({DEPENDENCIES_COMPONENTS})
	);
}
?>
TEMPLATE;
		$dependencies=array();
		foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $iteration)
		{
			if (substr($iteration->getFilename(),0,1)=='.')continue;
			if (preg_match('/\.(svn|cvs|git)/',$iteration->getPath()))continue;
			$dependencies=array_merge($dependencies,$this->findDependencies($iteration->getPath()._.$iteration->getFilename()));
		}
		$dependencies=array_unique($dependencies);
		sort($dependencies);
		$fNr=array
		(
			'{CLASSNAME}'				=>$name,
			'{NAME}'					=>$name,
			'{DESCRIPTION}'				=>$name,
			'{VERSION}'					=>'1.0.0',
			'{VERSION_CORE_MIN}'		=>str_ireplace('-dev','',$this->version),
			'{DEPENDENCIES_COMPONENTS}'	=>count($dependencies)?'\''.implode('\',\'',$dependencies).'\'':''
		);
		file_put_contents
		(
			$dir.'info.php',
			str_replace(array_keys($fNr),array_values($fNr),$template)
		);
	}
	
	public function install($moduleName)
	{
		$this->console->log($moduleName);
		
		
	}
	
	private function findDependencies($file)
	{
		$tokens=token_get_all(file_get_contents($file));
		$return		=array();
		$inThis		=false;
		$inComponent=false;
		foreach ($tokens as &$token)
		{
			switch ($token[0])
			{
				case T_VARIABLE:
				{
					if ($token[1]=='$this')
					{
						$inThis=true;
					}
					break;
				}
				case T_STRING:
				{
					if ($inThis && $token[1]=='component')
					{
						$inComponent=true;
					}
					else if ($inThis && $inComponent)
					{
						$return[]	=$token[1];
						$inThis		=false;
						$inComponent=false;
					}
					else if ($inThis && $token[1]!='component')
					{
						$inThis		=false;
						$inComponent=false;
					}
					break;
				}
			}
		}
		return $return;
	}
}
?>