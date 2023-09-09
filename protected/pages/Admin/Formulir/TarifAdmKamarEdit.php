<?php
class TarifAdmKamarEdit extends SimakConf
{

	public function onPreLoad($param)
	{
		parent::onPreLoad($param);
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('Simak'));
			
		if(!$this->IsPostBack)  // if the page is initially requested
		{
			$this->DDKamar->DataSource=KamarKelasRecord::finder()->findAll();
			$this->DDKamar->dataBind();
			$dataRuangPasien=$this->TarifAdmKamarRecord;
			
			// Populates the input controls with the existing user data			
			
			$this->DDKamar->SelectedValue=$dataRuangPasien->id_kls;
			$this->adm->Text=$dataRuangPasien->adm;
			$this->jsp->Text=$dataRuangPasien->jsp;
			$this->utk->Text=$dataRuangPasien->utk;
		}
	} 	 
	
	public function simpanClicked($sender,$param)
	{	
		if($this->IsValid)  // when all validations succeed
        {
			$dataRuangPasien=$this->TarifAdmKamarRecord;
			
            $dataRuangPasien->id_kls=$this->DDKamar->SelectedValue;
			$dataRuangPasien->adm=$this->adm->Text;
			$dataRuangPasien->jsp=$this->jsp->Text;
			$dataRuangPasien->utk=$this->utk->Text;
			
			// saves to the database via Active Record mechanism
            $dataRuangPasien->save(); 		
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifAdmKamar'));	
        }			
	}
		
	protected function getTarifAdmKamarRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$TarifAdmKamarRecord=TarifAdmKamarRecord::finder()->findByPk($ID);		
		if(!($TarifAdmKamarRecord instanceof TarifAdmKamarRecord))
			throw new THttpException(500,'id tidak benar.');
		return $TarifAdmKamarRecord;
	}	
	
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifAdmKamar'));			
	}
}
?>