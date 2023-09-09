<?php
class icdEdit extends SimakConf
{

	public function onLoad($param)
	{
		parent::onLoad($param);
		if(!$this->IsPostBack)  // if the page is initially requested
		{
		// Retrieves the existing user data. This is equivalent to:
		// $userRecord=$this->getUserRecord();
		$icdRecord=$this->IcdRecord;
		$this->icd->Text=$icdRecord->kode;
		$this->kat->Text=$icdRecord->kat;
		$this->dtd->Text=$icdRecord->dtd;
		$this->nmIndo->Text=$icdRecord->indonesia;
		$this->nmEng->Text=$icdRecord->inggris;
		}
	} 	 		
	
	public function simpanClicked($sender,$param)
	{			
		//$dateTmp = $this->tgl_lahir->Text;
		//$mysqlDate = $this->ConvertDate($dateTmp,'2');
		if($this->IsValid)  // when all validations succeed
        {
            // populates a UserRecord object with user inputs
            $icdRecord=$this->IcdRecord;
			//$this->icd->Text=$icdRecord->kode;
			$icdRecord->kat = $this->kat->Text;
			$icdRecord->dtd = $this->dtd->Text;
			$icdRecord->indonesia = $this->nmIndo->Text;
			$icdRecord->inggris = $this->nmEng->Text;
           
		    $icdRecord->save(); 		
			//Return to the origin page	
			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl($this->originPath()));
        }			
	}
	
	protected function originPath ()
	{
		$mode=$this->Request['mode'];
		if($mode == '01'){
			$balik='Pendaftaran.IcdRM';
		}
		return $balik;
	}
	
	protected function getIcdRecord()
	{
		// use Active Record to look for the specified username
		$kode=$this->Request['kode'];
		$icdRecord=IcdRecord::finder()->findByPk($kode);		
		if(!($icdRecord instanceof IcdRecord))
			throw new THttpException(500,'id tidak benar.');
		return $icdRecord;
	}	
	
    public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl($this->originPath()));		
	}
}
?>