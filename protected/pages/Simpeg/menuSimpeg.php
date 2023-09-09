<?php
class menuSimpeg extends SimakConf
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		$mode=$this->Request['logout'];
		
		if($mode == "1"){
			$this->Application->getModule('auth')->logout();
			$url=$this->Service->constructUrl($this->Service->DefaultPage);
			$this->Response->redirect($url);
		}
	
	}	
}
?>