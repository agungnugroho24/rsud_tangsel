<?php
class tarifFisioBaru extends SimakConf
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
			$this->DDRadKel->DataSource=FisioKelRecord::finder()->findAll();
			$this->DDRadKel->dataBind();
			
			$this->DDRadKateg->DataSource=FisioKategRecord::finder()->findAll();
			$this->DDRadKateg->dataBind();
			
			$this->DDJenisFoto->DataSource=FisioTdkRecord::finder()->findAll();
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
			$this->DDJenisFoto->DataSource=FisioKategRecord::finder()->findAll();
			$this->DDJenisFoto->dataBind();
			$this->DDJenisFoto->Enabled=false;
			$this->DDRadKateg->focus();
		}else
		{
			$kel=$this->DDRadKel->SelectedValue;
			$kateg=$this->DDRadKateg->SelectedValue;
			if  ($kateg=='0')
			{
				$sql="select kode,nama from tbm_fisio_tindakan where kode like 'U%'";
			}else{
				$sql="select kode,nama from tbm_fisio_tindakan where kelompok = '$kel' AND kategori = '$kateg'";
			}
			$this->DDJenisFoto->DataSource=$this->queryAction($sql,'S');
			$this->DDJenisFoto->dataBind();
			$this->DDJenisFoto->Enabled=true;
			$this->DDJenisFoto->focus();	
			//$this->test->text=$kel.'+'.$kateg;
		}
	}
	
	public function DDJenisFotoChanged($sender,$param)
	{
		if  ($this-> DDJenisFoto->SelectedValue=='')
		{
			$this->Tarif1->Enabled=false;
			$this->Tarif2->Enabled=false;
			$this->Tarif3->Enabled=false;
			$this->Tarif4->Enabled=false;
			$this->Tarif5->Enabled=false;
			$this->Tarif6->Enabled=false;
			
			$this-> DDJenisFoto->focus();
		}else
		{
			$this->Tarif1->ReadOnly=false;
			$this->Tarif2->ReadOnly=false;
			$this->Tarif3->ReadOnly=false;
			$this->Tarif4->ReadOnly=false;
			$this->Tarif5->ReadOnly=false;
			$this->Tarif6->ReadOnly=false;
			
			$this->Tarif1->Enabled=true;
			$this->Tarif2->Enabled=true;
			$this->Tarif3->Enabled=true;
			$this->Tarif4->Enabled=true;
			$this->Tarif5->Enabled=true;
			$this->Tarif6->Enabled=true;
			
			$this->Tarif1->focus();		
		}
		//$this->test->text=$this->DDJenisFoto->SelectedValue;
	}
	
	public function simpanClicked($sender,$param)
	{			
		if($this->IsValid)  // when all validations succeed
        {
		    // populates a UserRecord object with user inputs
			$newFisioTarifRecord=new FisioTarifRecord;
			$newFisioTarifRecord->id=TPropertyValue::ensureString($this->DDJenisFoto->SelectedValue);
			$newFisioTarifRecord->tarif=$this->Tarif1->Text;	
			$newFisioTarifRecord->tarif1=$this->Tarif2->Text;	
			$newFisioTarifRecord->tarif2=$this->Tarif3->Text;	
			$newFisioTarifRecord->tarif3=$this->Tarif4->Text;	
			$newFisioTarifRecord->tarif4=$this->Tarif5->Text;	
			$newFisioTarifRecord->tarif5=$this->Tarif6->Text;	
			
			//$newFisioTarifRecord->tarif2=$this->Tarif2->Text;
			//$newFisioTarifRecord->tarif3=$this->Tarif3->Text;
						
			// saves to the database via Active Record mechanism
            $newFisioTarifRecord->save(); 			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Fisio.tarifFisio'));
        }			
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Fisio.tarifFisio'));		
	}
}
?>