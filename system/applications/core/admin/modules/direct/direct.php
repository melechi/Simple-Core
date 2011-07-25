<?php
class core_admin_module_direct extends adminControlPanel_module
{
	public function initiate()
	{
		//SETUP SECTIONS
		$this->setRootSectionTitle('Manage Direct Server');
		$this->addSection
		(
			array
			(
				'id'		=>'dashboard',
				'title'		=>'Dashboard',
				'weight'	=>0,
				'view'		=>'dashboard',
				'controller'=>'dashboard',
				'default'	=>true
			)
		);
		$this->addSection
		(
			array
			(
				'id'		=>'providers',
				'title'		=>'Providers',
				'weight'	=>1,
				'view'		=>'providers',
				'controller'=>'providers'
			)
		)->addChildSection
		(
			array
			(
				'id'		=>'test1',
				'title'		=>'Child Test',
				'weight'	=>0,
				'view'		=>'test1',
				'controller'=>'test1'
			)
		);
		
//		
//		$this->addSection
//		(
//			array
//			(
//				'title'		=>'Dashboard',
//				'weight'	=>0,
//				'view'		=>'dashboard',
//				'controller'=>'dashboard'
//			),
//			array
//			(
//				'title'		=>'Providers',
//				'weight'	=>1,
//				'view'		=>'providers',
//				'controller'=>'providers'
//			),
//			array
//			(
//				'title'		=>'Modules',
//				'weight'	=>2,
//				'view'		=>'modules',
//				'controller'=>'modules'
//			),
//			array
//			(
//				'title'		=>'Methods',
//				'weight'	=>3,
//				'view'		=>'methods',
//				'controller'=>'methods'
//			),
//			array
//			(
//				'title'		=>'Test 1',
//				'weight'	=>0,
//				'view'		=>'test1',
//				'controller'=>'test1'
//			),
//			array
//			(
//				'title'		=>'Test 3',
//				'weight'	=>2,
//				'view'		=>'test3',
//				'controller'=>'test3'
//			),
//			array
//			(
//				'title'		=>'Test 2',
//				'weight'	=>1,
//				'view'		=>'test2',
//				'controller'=>'test2'
//			),
//			array
//			(
//				'title'		=>'Test 4',
//				'weight'	=>0,
//				'view'		=>'test4',
//				'controller'=>'test4'
//			),
//			array
//			(
//				'title'		=>'Test 5',
//				'weight'	=>0,
//				'view'		=>'test5',
//				'controller'=>'test5'
//			)
//		);
	}
//	
//	public function view_dashboard()
//	{
//		
//	}
}
?>