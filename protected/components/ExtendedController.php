<?php

class ExtendedController extends CController
{
	
	public function beforeAction()
	{
		return TRUE;
	}
	
	public function afterAction()
	{
		return TRUE;
	}
}