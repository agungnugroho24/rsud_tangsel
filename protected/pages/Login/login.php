<?php
class login extends TPage
{
    /**
     * Validates whether the username and password are correct.
     * This method responds to the TCustomValidator's OnServerValidate event.
     * @param mixed event sender
     * @param mixed event parameter
     */	
	public function onLoad($param)
	{
		parent::onLoad($param);		
		if(!$this->IsPostBack){	
			$this->Username->focus();			
		}				
	}
	
    public function validateUser($sender,$param)
    {
        $authManager=$this->Application->getModule('auth');
        if(!$authManager->login($this->Username->Text,$this->Password->Text))
            $param->IsValid=false;  // tell the validator that validation fails
			$this->Username->focus();
    }
 
    /**
     * Redirects the user's browser to appropriate URL if login succeeds.
     * This method responds to the login button's OnClick event.
     * @param mixed event sender
     * @param mixed event parameter
     */
    public function loginClicked($sender,$param)
    {        
		if($this->Page->IsValid)  // all validations succeed
		{   			
			$url=$this->Service->constructUrl('Pendaftaran.DaftarBaru');
			$this->Response->redirect($url);
		}		
    }   
}
?>