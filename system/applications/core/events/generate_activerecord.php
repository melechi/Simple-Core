<?php
class core_event_generate_activerecord extends event
{
	public function initiate()
	{
		if($this->global->get('database'))
		{
			$database = $this->global->get('database');
			$sql = "SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA ='$database';";
			if($this->component->database->query($sql))
			{
				$results = $this->component->database->result('TABLE_NAME', 'TABLE_TYPE');
				foreach($results as $result)
				{
					$type = ($result['TABLE_TYPE'] == 'BASE TABLE') ? 'table' : 'view';
					$output = $this->generate($this->global->get('database'), $result['TABLE_NAME'], $type);
					
					$file = $this->parent->my->dir.'data'._.$result['TABLE_NAME'].'.xml';
					echo "<b>SAVING TO $file</b><pre>";
					file_put_contents($file, $output);
					echo htmlentities($output).'</pre><hr />';
				}
			}
		}
	}
	
	public function generate($database, $table, $type)
	{
		$sql = <<<SQL
SELECT * FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = '$database'
AND TABLE_NAME = '$table';
SQL;
		if($this->component->database->query($sql))
		{
			$prefix = $this->getPrefix($this->component->database->result('COLUMN_NAME'));
			$top = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<activeRecord version="1.0">
	<name>$table</name>
	<prefix>$prefix</prefix>
	<connection>$database</connection>
	<type>$type</type>
	<schema>

XML;
			$middle = '';
			$results = $this->component->database->result('COLUMN_NAME', 'DATA_TYPE', 'COLUMN_KEY');
			foreach($results as $result)
			{
				$middle .= "\t\t".'<column name="'.$result['COLUMN_NAME'].'"';
				$middle .= "\t\t".'type="'.$result['DATA_TYPE'].'"';
				if($result['COLUMN_KEY']=='PRI') $middle .= "\t".'primarykey="true" id="true"'; 
				$middle .= ' />'."\n";
			}
			$bottom = <<<XML
	</schema>
</activeRecord>
XML;
			return $top.$middle.$bottom;
		}
	}
	
	public function getPrefix($results)
	{
		$array = explode('_', $results[0]);
		if(!isset($array[1])) return '';
		$prefix = $array[0];
		
		foreach($results as $result)
		{
			$array = explode('_', $result);
			if(!isset($array[1])) return '';
			if($array[0] != $prefix) return '';
		}
		
		return $prefix;
	}
}
?>