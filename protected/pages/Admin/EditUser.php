<?php
class EditUser extends SimakConf
{
    /**
     * Initializes the inputs with existing user data.
     * This method is invoked by the framework when the page is being initialized.
     * @param mixed event parameter
     */
    public function onInit($param)
    {
        parent::onInit($param);
        if(!$this->IsPostBack)  // if the page is initially requested
        {
            // Retrieves the existing user data. This is equivalent to:
            // $userRecord=$this->getUserRecord();
            $userRecord=$this->userRecord;
 
            // Populates the input controls with the existing user data            
			$this->Username->Text=$userRecord->username;            
			$this->Realname->Text=$userRecord->real_name;
            $this->NoHP->Text=$userRecord->no_hp;
			$this->DDRole->SelectedValue=$userRecord->role;
			$this->DDAktif->SelectedValue=$userRecord->status;		
			
			$this->Nip1->Text=$userRecord->nip;
			
			/*$this->Nip1->Text=substr($userRecord->nip,0,3);
			$this->Nip2->Text=substr($userRecord->nip,3,3);
			$this->Nip3->Text=substr($userRecord->nip,6,3);	*/
			
			$this->Catatan->Text=$userRecord->catatan;		
			//here to show multiple selection result from database to TListBox
			$dump = explode(',', $userRecord->allow);				
			$this->TLBAllow->SelectedValues=$dump;						
        }
    } 	
	
	public function onLoad($param)
	{
		parent::onLoad($param);			
		if(!$this->IsPostBack){	
			$this->Username->focus();
			$this->DDRole->DataSource=RoleRecord::finder()->findAll();
			$this->DDRole->dataBind();
			$this->TLBAllow->DataSource=AllowRecord::finder()->findAll();
			$this->TLBAllow->dataBind(); 										
		}		
	}
	
	public function checkUsername($sender,$param)
    {
        // valid if the username is not found in the database
        $param->IsValid=UserRecord::finder()->findByPk($this->Username->Text)===null;
    }	
	  
 	public function simpanClicked($sender,$param)
	{					
		if($this->IsValid)  // when all validations succeed
        {
            $userRecord=$this->UserRecord;
			// populates a UserRecord object with user inputs		
            
            $userRecord->username=$this->Username->Text;				
			$userRecord->real_name=$this->Realname->Text;
            $userRecord->no_hp=$this->NoHP->Text;
			$userRecord->catatan=$this->Catatan->Text;
			//$Nip = $this->Nip1->Text . $this->Nip2->Text . $this->Nip3->Text;
			$Nip = trim($this->Nip1->Text);
			$userRecord->nip=$Nip;
			if($this->User->IsAdmin){
  				$userRecord->role=(string)$this->DDRole->SelectedValue;
				$userRecord->status=(string)$this->DDAktif->SelectedValue;
				$userRecord->allow=(string)$this->collectSelectionResult($this->TLBAllow);         
			}
			if($this->Password->Text <> '')
			{
				$passVal = md5($this->Password->Text);
			 	$userRecord->password=$passVal;
			}	
            // saves to the database via Active Record mechanism
            $userRecord->save();			
 
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Admin.AdminUser'));
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
        if(($this->User->IsAdmin || $this->User->IsPower) && $this->Request['username']!==null)
            $username=$this->Request['username'];
 
        // use Active Record to look for the specified username
        $userRecord=UserRecord::finder()->findByPk($username);
		$roleTmp = $userRecord->role;//Mendapatkan nilai role dalam tabel User!
		if($this->User->IsPower && $roleTmp == "2")//Bila level dibawahnya akan mengakses level diatasnya!
			throw new THttpException(500,'Anda tidak punya Hak Akses terhadap Super User.');
        if(!($userRecord instanceof UserRecord))
            throw new THttpException(500,'Username tersebut tidak ditemukan.');
        return $userRecord;
    }
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.AdminUser'));		
	}
}
?>
