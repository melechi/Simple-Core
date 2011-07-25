<?php
class application_sandbox extends application
{
	public $hasSettings		=true;
	public $useBreadcrumbs	=true;
	public $useSmarty		=true;
	/**
	 * @var core_logger $logger
	 * @property core_logger $logger BOTH The Simple Core Logger.
	 * @property-read core_logger $logger READ  The Simple Core Logger.
	 * @property-write core_logger $logger WRITE The Simple Core Logger.
	 */
	
	public function initiate()
	{
//		print_r($this->config);
//		var_dump($this->my);
		$this->logger->log('test');
		$this->logger->info('Sandbox Initiated');
		$this->logger->warn('OMG! THIS IS A WARNING!');
		$this->logger->error('ERRORZoR!','Some Category');
		$this->logger->debug(print_r($this->parent,true));
		
//		$file=simplexml_load_file($this->my->dir.'test.xml');
//		print_r($file);
//		$config=new config($this->my->dir.'test.xml');
//		print_r($config);
//		print_r($config->loop);
//		foreach ($config as $key=>$item)
//		{
////			var_dump($key);
//			print_r($item);
//		}
//		foreach ($config->loop->item as $key=>$item)
//		{
////			var_dump($key);
//			var_dump($item);
//		}
//		print '1-----1';
//		var_dump($config);
//		print '2-----2';
//		var_dump($config->single);
//		print '3-----3';
//		var_dump($config->single->foo);
//		print '4-----4';
//		var_dump($config->single->foo['bar']);
//		print '5-----5';
//		var_dump($config->double->foo);
//		print '6-----6';
//		var_dump($config->double->foo[0]);
//		print '7-----7';
//		var_dump($config->double->foo[1]);
//		print '8-----8';
//		var_dump($config->double->foo[1]['bar']);
//		print '9-----9';
//		var_dump($config->double->foo[1]['abc']);
//		print '000-----000';
		
//		$config=new config($this->my->dir.'directServer.xml');
//		print_r($config->providers->provider[3]);
//		
//		
//		exit();
		
		return true;
		
		
		
//		var_dump($this->node());
		
		
//		print_r($this->my);
//		print 'Hello Sandbox :)';
//		$this->branch('test','now');
//		$this->exception('Test exception!');
		
		
		
//		$this->registerActiveRecords();
		
//		print $this->component->orm->category->length;
		
//		if ($this->component->orm->category->length)
//		{
//			foreach ($this->component->orm->category as $record)
//			{
//				print_r($record->getValues());
//			}
//		}
		
//		$records=$this->component->orm->category->id(array(1,2,3));
//		foreach ($records as $record)
//		{
//			print_r($record->getValues());
//		}
//		foreach ($records as $record)
//		{
//			if ($record->id===1)
//			{
//				$records->seek(3);
//				continue;
//			}
//			print_r($record->getValues());
//		}
//		print_r($records->seek(1)->getValues());
		
//		for ($i=0,$j=count($records); $i<$j; $i++)
//		{
//			print_r($records[$i]->getValues());
//		}
//		$records[0]->category_description='I am Foo!';
//		$records[0]->commit();
//		
//		$record=$this->component->orm->category->newRecord();
//		$record->parentid	=0;
//		$record->namespace	='global';
//		$record->name		='Baz';
//		$record->safename	='baz';
//		$record->status		=1;
//		$record->commit();
		
//		$record=$this->component->orm->category->newRecord
//		(
//			array
//			(
//				'parentid'	=>0,
//				'namespace'	=>'global',
//				'name'		=>'FooBarBaz',
//				'safename'	=>'foobarbaz',
//				'status'	=>1
//			)
//		)->commit();
		
//		$records=$this->component->orm->category->id(array(6,7));
//		if($records[0])$records[0]->delete();
//		foreach ($this->component->orm->category->getCachedRecords() as $record)
//		{
//			print_r($record->getValues());
//		}
		
//		$this->component->orm->category->id($_POST['category_id'])->category_status=1;
//		$this->component->orm->commitChanges();
		
//		$ar=$this->component->dbtools->activeRecord;
//		$ar->category->join('account','account_id',$_POST['account_id'])->select('*','account_id',1);
//		$ar->category->join('account','account_id',$_POST['account_id'])->select
//		(
//			'*',
//			array
//			(
//				'account_id',1,
//				'account_status',1
//			)
//		);
		
//		$records=$this->component->orm->account->quickSelect('account_id',1);
//		foreach ($records as $record)
//		{
//			print_r($record->getValues());
//		}
//		
//		$records=$this->component->orm->account->quickSelect
//		(
//			array('profiles_id',ACTIVERECORD_OP_EQUALS,1),
//			array('account_status',ACTIVERECORD_OP_EQUALS,1)
//		);
//		foreach ($records as $record)
//		{
//			print_r($record->getValues());
//		}
//		
//		$records	=$this->component->orm->profiles->newSelection()
//					->join('account','profiles_accountid',ACTIVERECORD_OP_EQUALS,'account_id')
//					->filter
//					(
//						array('profiles_id',ACTIVERECORD_OP_EQUALS,1),
//						array('account_status',ACTIVERECORD_OP_EQUALS,1)
//					)->execute();
//		foreach ($records as $record)
//		{
//			print_r($record->getValues());
//		}
		
		
		
		
		
		
		
		
		//LOCALIZATION
//		$this->component->i18n->init();
//		$this->component->i18n	->bindTextDomain($this,'messages','locale')
//								->setDomain($this,'messages');
		
//		setlocale(LC_ALL,'fr_FR');
//		T_setlocale(5,'fr_FR');
//		$domain = 'messages';
//		T_bindtextdomain($domain,$this->my->dir.'locale'._);
//		T_bind_textdomain_codeset($domain, 'iso-8859-1');
//		T_textdomain($domain);
//		print _('Hello World!<br />');
//		print _('Fooo!<br />');
//		print _('Bar!<br />');
//		print _('Bazzza!<br />');
//		print _('blah blargh brag!');
		
//		$n=3;
//		printf($this->component->i18n->nGetText('%d Comment','%d Comments',$n),$n);
		
//		$n=3;
//		printf(ngettext('%d Comment','%d Comments',$n),$n);
//		printf(T_ngettext('%d Comment','%d Comments',$n),$n);
//		print gettext('Hello World!');
		
		
		
		
		return true;
	}
	
	public function eventmap()
	{
		$this->eventmap->event('direct','API','?',array('folder'=>'direct','object'=>'API'));
		return true;
	}
	
	public function sitemap()
	{
		if ($this->node(0)=='foo')
		{
			print 'at foo node';
			if ($this->node(1)=='bar')
			{
				print '<br />at bar node';
			}
		}
//		$this->sitemap->page('|home');
//		$this->sitemap->page('direct');
		return true;
	}
}
?>