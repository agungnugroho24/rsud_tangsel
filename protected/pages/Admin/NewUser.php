<?php
class NewUser extends SimakConf
{
 public function onLoad($param)
	{
		parent::onLoad($param);
		
		if($this->IsPostBack){
			
		}
			
		if(!$this->IsPostBack){	
			$this->Username->focus();
			$this->DDRole->DataSource=RoleRecord::finder()->findAll();
			$this->DDRole->dataBind();
			$this->TLBAllow->DataSource=AllowRecord::finder()->findAll();
			$this->TLBAllow->dataBind(); 			
			
			//$nip = $this->nipcounter();
			//$this->Username->Text = '001'.$nip;
			//$this->Nip1->Text = '001';
			//$this->Nip2->Text = substr($nip,0,3);
			//$this->Nip3->Text = substr($nip,3,3);
		}		
	} 
	
	public function checkUsername($sender,$param)
    {
        // valid if the username is not found in the database
        $param->IsValid=UserRecord::finder()->findByPk($this->Username->Text)===null;
    }
	
	
	public function batalClicked()
	{
		$this->Username->Text = '';
		$this->Password->Text = '';
		$this->Password2->Text = '';
		$this->Realname->Text = '';
		$this->NoHP->Text = '';
		$this->Catatan->Text = '';
		
		$this->Username->Focus();
	}
	
	public function simpanClicked($sender,$param)
	{			
		$dateTmp = date('Y-m-d');
		$timeTmp = date('G:i:s');
		$NipTmp = trim($this->Nip1->Text);
		//$NipTmp = $this->Nip1->Text . $this->Nip2->Text . $this->Nip3->Text;
		$mysqlDate = $this->ConvertDate($dateTmp,'0');
		$nama = ucwords(strtolower($this->Realname->Text));
		if($this->IsValid)  // when all validations succeed
        {
            // populates a UserRecord object with user inputs
            $userRecord=new UserRecord;
            $userRecord->username=$this->Username->Text;
			$passTmp = md5($this->Password->Text);
            $userRecord->password=$passTmp;
			$userRecord->real_name=$nama;
            $userRecord->nip=$NipTmp;
			$userRecord->catatan=$this->Catatan->Text;		
			$userRecord->no_hp=$this->NoHP->Text;		
  			$userRecord->role=(int)$this->DDRole->SelectedValue;
            $userRecord->status=(int)$this->DDAktif->SelectedValue;		
            $userRecord->allow=(string)$this->collectSelectionResult($this->TLBAllow);
 			$userRecord->tgl_create=$dateTmp;
            $userRecord->wkt_create=$timeTmp;
			$userRecord->tgl_log=$dateTmp;
            $userRecord->wkt_log=$timeTmp;
 			
            // saves to the database via Active Record mechanism
            $userRecord->save();
 
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Admin.AdminUser'));
        }	
		else
		{
		
		}		
	}
	
   public function keluarClicked($sender,$param)
   {		
	$this->Response->redirect($this->Service->constructUrl('Admin.AdminUser'));		
   }
}
?>