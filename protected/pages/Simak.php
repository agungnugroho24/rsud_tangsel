<?php
class Simak extends SimakConf
{
    public function onInit($param)
	{		
	
	}	
	 
			
	public function onLoad($param)
	{				
		parent::onLoad($param);				
		if(!$this->IsPostBack && !$this->IsCallBack)
        {   
			if($this->User->IsGuest)
				$this->Response->redirect($this->Service->constructUrl('login'));
		}		
    }
	
}
?>
