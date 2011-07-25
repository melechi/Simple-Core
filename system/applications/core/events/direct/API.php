<?php
class core_event_API extends event
{
	public $server=null;
	
	public function initiate()
	{
		$this->server=$this->component->ext->direct->initServer($this->parent,$this,$this->parent->my->dir.'directServer.xml','application.API');
		if (!$this->node(2))
		{
			$this->server->setJSTemplate
			(
				<<<JS
\$PWT.Class.create
(
	{
		\$namespace:	'{NAMESPACE}',
		\$name:		'Direct'
	}
)
(
	{
		API: {JSON}
	}
);
JS
			);
			$this->server->generateJavaScriptDefinition();
		}
		elseif ($this->server->providerExists($this->node(2)))
		{
			$this->server->processRequest();
		}
		exit();
	}
}
?>