<?php
class tarifRadBaru extends SimakConf
{
 public function onLoad($param)
	{
		parent::onLoad($param);
		/*
		$tmpVar=$this->authApp('8');//ID aplikasi keKaban
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
		{
			$this->Response->redirect($this->Service->constructUrl('Home'));
		
		}
		*/
			
		if(!$this->IsPostBack){
			$this->DDRadKel->DataSource=RadKelRecord::finder()->findAll();
			$this->DDRadKel->dataBind();
			
			$this->DDRadKateg->DataSource=RadKategRecord::finder()->findAll();
			$this->DDRadKateg->dataBind();
			
			$this->DDJenisFoto->DataSource=RadTdkRecord::finder()->findAll();
			$this->DDJenisFoto->dataBind();
						
			$this->DDRadKel->focus();								
		}		
	}
	
	public function DDRadKelChanged($sender,$param)
	{/*
		if  ($this->DDRadKel->SelectedValue=='')
		{
			$this->DDRadKateg->DataSource=RadKategRecord::finder()->findAll();
			$this->DDRadKateg->dataBind();
			$this->DDRadKateg->Enabled=false;
			$this->DDRadKel->focus();
		}else
		{*/
			$this->DDRadKateg->Enabled=true;
			$this->DDRadKateg->focus();		
		//}
	}
	
	public function DDRadKategChanged($sender,$param)
	{
		if  ($this->DDRadKateg->SelectedValue=='')
		{
			$this->DDJenisFoto->DataSource=RadKategRecord::finder()->findAll();
			$this->DDJenisFoto->dataBind();
			$this->DDJenisFoto->Enabled=false;
			$this->DDRadKateg->focus();
		}else
		{
			$kel=$this->DDRadKel->SelectedValue;
			$kateg=$this->DDRadKateg->SelectedValue;
			$sql="select kode,nama from tbm_rad_tindakan where kelompok = '$kel' AND kategori = '$kateg'";
			$this->DDJenisFoto->DataSource=$this->queryAction($sql,'S');
			$this->DDJenisFoto->dataBind();
			$this->DDJenisFoto->Enabled=true;
			$this->DDJenisFoto->focus();	
			//$this->test->text=$kel.'+'.$kateg;
		}
	}
	
	public function checkRM($sender,$param)
    {       
		// valid if the id kabupaten is not found in the database
		//$param->IsValid=(LabTdkRecord::finder()->findByPk($this->ID->Text)===null);
		
		if(RadTarifRecord::finder()->findByPk($this->DDJenisFoto->SelectedValue))
		{
			$param->IsValid = false;
		}
		else
		{
			$param->IsValid = true;
		}
    }
	
	public function DDJenisFotoChanged($sender,$param)
	{
		if  ($this-> DDJenisFoto->SelectedValue=='')
		{
			$this->tarif->Enabled=false;
			$this->tarif->Enabled=false;
			$this->Tarif2->Enabled=false;
			$this->Tarif3->Enabled=false;
			$this->Tarif4->Enabled=false;
			$this->Tarif5->Enabled=false;
			$this->Tarif6->Enabled=false;
			$this-> DDJenisFoto->focus();
		}else
		{
			$this->tarif->ReadOnly=false;
			$this->tarif->ReadOnly=false;
			$this->Tarif2->ReadOnly=false;
			$this->Tarif3->ReadOnly=false;
			$this->Tarif4->ReadOnly=false;
			$this->Tarif5->ReadOnly=false;
			$this->Tarif6->ReadOnly=false;
			
			$this->tarif->Enabled=true;
			$this->tarif->Enabled=true;
			$this->Tarif2->Enabled=true;
			$this->Tarif3->Enabled=true;
			$this->Tarif4->Enabled=true;
			$this->Tarif5->Enabled=true;
			$this->Tarif6->Enabled=true;
			
			$this->tarif->focus();		
		}
		//$this->test->text=$this->DDJenisFoto->SelectedValue;
	}
	
	public function simpanClicked($sender,$param)
	{			
		if($this->IsValid)  // when all validations succeed
        {
		    // populates a UserRecord object with user inputs
			$newRadTarifRecord=new RadTarifRecord;
			$newRadTarifRecord->id=TPropertyValue::ensureString($this->DDJenisFoto->SelectedValue);
			$newRadTarifRecord->tarif=$this->tarif->Text;	
			$newRadTarifRecord->tarif1=$this->Tarif2->Text;	
			$newRadTarifRecord->tarif2=$this->Tarif3->Text;	
			$newRadTarifRecord->tarif3=$this->Tarif4->Text;	
			$newRadTarifRecord->tarif4=$this->Tarif5->Text;	
			$newRadTarifRecord->tarif5=$this->Tarif6->Text;				
						
			// saves to the database via Active Record mechanism
            $newRadTarifRecord->save(); 			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Rad.tarifRad'));
        }			
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Rad.tarifRad'));		
	}
}
?>