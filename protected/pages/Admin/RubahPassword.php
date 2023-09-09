<?php
class RubahPassword extends SimakConf
{
    /**
     * Initializes the inputs with existing user data.
     * This method is invoked by the framework when the page is being initialized.
     * @param mixed event parameter
     */
	
    public function onInit($param)
    {
        parent::onInit($param);
       
	   	if($this->User->IsGuest)
			$this->Response->redirect($this->Service->constructUrl('Simak'));	
    } 	
	
	public function onLoad($param)
	{
		parent::onLoad($param);			
		if(!$this->IsPostBack){	
			$this->PasswdLama->focus();
			$dataUser=$this->userRecord;
			$this->Username->Text = $dataUser->username;	
		}		
	}
	
	public function checkUsername($sender,$param)
    {
        // valid if the username is not found in the database
        $param->IsValid=UserRecord::finder()->findByPk($this->Username->Text)===null;
    }	
	
	public function checkPassword($sender,$param)
    {
        // valid if the username is not found in the database
        $param->IsValid=!(UserRecord::finder()->find('username = ? AND password=?',$this->Username->Text, md5($this->PasswdLama->Text))===null);
    }
	 
	public function batalClicked()
	{
		$this->Response->reload();
	}
	 
 	public function simpanClicked($sender,$param)
	{					
		if($this->IsValid)  // when all validations succeed
        {
            $userRecord=$this->UserRecord;
			// populates a UserRecord object with user inputs
			$passVal = md5($this->Password->Text); 	
			
			$userRecord->password=$passVal;
				
            // saves to the database via Active Record mechanism
            $userRecord->save();			
 
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Simak'));
        }			
	}
    /**
     * Returns the user data to be editted.
     * @return UserRecord the user data to be editted.
     * @throws THttpException if the user data is not found.
     */
     protected function getUserRecord()
    {
        // the user to be editted is the currently logged-in user
        //$username=$this->User->Name;
        // if the 'username' GET var is not empty and the current user
        // is an administrator, we use the GET var value instead.
        //if(($this->User->IsAdmin || $this->User->IsPower) && $this->Request['username']!==null)
            $username=$this->User->Name;
 
        // use Active Record to look for the specified username
        $userRecord=UserRecord::finder()->findByPk($username);
		//$roleTmp = $userRecord->role;//Mendapatkan nilai role dalam tabel User!
		//if($this->User->IsPower && $roleTmp == "2")//Bila level dibawahnya akan mengakses level diatasnya!
		//	throw new THttpException(500,'Anda tidak punya Hak Akses terhadap Super User.');
        if(!($userRecord instanceof UserRecord))
            throw new THttpException(500,'Username tersebut tidak ditemukan.');
        return $userRecord;
    }
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
}
?>
