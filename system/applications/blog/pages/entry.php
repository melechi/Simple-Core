<?php
class blog_page_entry extends page
{
	private $entries=array();
	
	public function initiate()
	{
		$this->parent->setBoundTemplateVar('BLOGENTRIES',$this->entries);
		//Year
		if (!$this->node(1))
		{
			$this->prepareEntries($this->component->orm->entry->newSelection()
			->filter
			(
				array
				(
					'blog_entry_timestamp',
					ORM_OP_BETWEEN,
					$this->node(0).'-01-01',
					$this->node(0).'-12-31'
				)
			)->execute());
		}
		//Year & Month
		else if (!$this->node(2))
		{
			$this->prepareEntries($this->component->orm->entry->newSelection()
			->filter
			(
				array
				(
					'blog_entry_timestamp',
					ORM_OP_BETWEEN,
					$this->node(0).'-'.$this->node(1).'-01',
					$this->node(0).'-'.$this->node(1).'-31'
				)
			)->execute());
		}
		//Year, Month & Day
		else if (!$this->node(3))
		{
			$this->prepareEntries($this->component->orm->entry->newSelection()
			->filter
			(
				array
				(
					'blog_entry_timestamp',
					ORM_OP_BETWEEN,
					$this->node(0).'-'.$this->node(1).'-'.$this->node(2).' 00:00:00',
					$this->node(0).'-'.$this->node(1).'-'.$this->node(2).' 23:59:59'
				)
			)->execute());
		}
		//Year, Month, Day & Article Name
		else if (!$this->node(4))
		{
			if (is_numeric($this->node(3)))
			{
				$this->prepareEntries($this->component->orm->entry->id($this->node(3)));
			}
			else if (is_string($this->node(3)))
			{
				//TODO: Article name
				
			}
		}
		//Year, Month, Day, Article Name & 'comments'
		else if ($this->node(4)=='comments')
		{
			die('456');
		}
	}
	
	private function prepareEntries($recordSet)
	{
		foreach ($recordSet as $entry)
		{
			$values=$entry->getValues();
			$values['blog_entry_timestamp']		=date('d/m/Y \a\t g:ia',strtotime($values['blog_entry_timestamp']));
			$values['blog_entry_labels']		='label1, label2, label3';
			$values['blog_entry_numComments']	=$this->component->orm->comment->newSelection()
			->filter(array('blog_comment_entry_id',ORM_OP_EQUALS,$values['blog_entry_id']))
			->execute()->length;
			array_push($this->entries,$values);
		}
	}
}
?>