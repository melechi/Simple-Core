<?php
class blog_page_home extends page
{
	public function initiate()
	{
		$entries=array();
		$this->parent->setBoundTemplateVar('BLOGENTRIES',$entries);
		foreach ($this->component->orm->entry->quickSelect() as $entry)
		{
			$values=$entry->getValues();
			$dateURL=date('Y/m/d',strtotime($values['blog_entry_timestamp']));
			$values['blog_entry_url']			=$this->parent->makeURL('/'.$dateURL.'/'.$values['blog_entry_id'].'/');
			$values['blog_entry_timestamp']		=date('d/m/Y \a\t g:ia',strtotime($values['blog_entry_timestamp']));
			$values['blog_entry_labels']		='label1, label2, label3';
			$values['blog_entry_numComments']	=$this->component->orm->comment->newSelection()
			->filter(array('blog_comment_entry_id',ORM_OP_EQUALS,$values['blog_entry_id']))
			->execute()->length;
			array_push($entries,$values);
		}
	}
}
?>