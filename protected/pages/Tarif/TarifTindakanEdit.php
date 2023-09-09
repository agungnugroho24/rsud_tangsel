<?php
class TarifTindakanEdit extends SimakConf
{

	public function onPreLoad($param)
	{
		parent::onPreLoad($param);
		/*
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('Simak'));
		*/	
		$ID=$this->Request['ID'];		
		if(!$this->IsPostBack)  // if the page is initially requested
		{
			$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDKlinik->dataBind();
			$sql="SELECT id_klinik FROM tbm_nama_tindakan WHERE id='$ID' ";
			$klinik=NamaTindakanRecord::finder()->findBySql($sql)->id_klinik;
			$this->DDKlinik->SelectedValue=$klinik;			
			$this->DDNmTdk->DataSource=NamaTindakanRecord::finder()->findAll();
			$this->DDNmTdk->dataBind();
			$nmTdkRecord=$this->TarifTindakanRecord;
			$this->DDNmTdk->SelectedValue=$nmTdkRecord->id;
			$this->biaya1->text=$nmTdkRecord->biaya1;
			$this->biaya2->text=$nmTdkRecord->biaya2;
			$this->biaya3->text=$nmTdkRecord->biaya3;
			$this->biaya4->text=$nmTdkRecord->biaya4;
		}
	} 	 					
	
	public function cekKlinik($sender,$param)
    {		
        //$this->DDNmTdk->DataSource=NamaTindakanRecord::finder()->findAll('id_klinik= ?',$this->DDKlinik->SelectedValue);
		$this->DDNmTdk->dataBind();        
		//$this->biaya4->text=$this->DDKlinik->SelectedValue;
    }
	
	protected function collectSelectionListResult($input)
	{
		$indices=$input->SelectedIndices;		
		foreach($indices as $index)
		{
			$item=$input->Items[$index];
			return $index;
		}		
	}		
	
	public function simpanClicked($sender,$param)	{			
		
		if($this->IsValid)  // when all validations succeed
        {
            // populates a UserRecord object with user inputs
            $TarifTindakanRecord=$this->TarifTindakanRecord;           
            //$nmTdkRecord->nama=ucwords($this->nama->Text);				
  			//$nmTdkRecord->id_klinik=(string)$this->DDKlinik->SelectedValue;            
			// saves to the database via Active Record mechanism            
			$TarifTindakanRecord->biaya1=$this->biaya1->Text;
			$TarifTindakanRecord->biaya2=$this->biaya2->Text;
			$TarifTindakanRecord->biaya3=$this->biaya3->Text;
			$TarifTindakanRecord->biaya4=$this->biaya4->Text;
			
			$TarifTindakanRecord->save(); 		
			//Return to the origin page	
			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl($this->originPath()));
        }			
	}
	
	protected function originPath ()
	{
		$mode=$this->Request['mode'];
		if($mode == '01'){
			$balik='Tarif.TarifTindakan';
		}else if($mode == '02'){
			$balik='Pendaftaran.DaftarCariRwtJln';
		}else if($mode == '03'){
			$balik='Pendaftaran.DaftarCariRwtInap';
		}else if($mode == '04'){
			$balik='Pendaftaran.DaftarCariRwtIgd';
		}else if($mode == '05'){
			$balik='Pendaftaran.kunjPas';
		}else if($mode == '06'){
			$balik='Pendaftaran.kunjPasIgd';
		}
		return $balik;
	}
	
	protected function getTarifTindakanRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$TarifTindakanRecord=TarifTindakanRecord::finder()->findByPk($ID);		
		if(!($TarifTindakanRecord instanceof TarifTindakanRecord))
			throw new THttpException(500,'id tidak benar.');
		return $TarifTindakanRecord;
	}	
	
    public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl($this->originPath(),array('ID'=>$this->getViewState('ID'))));		
	}
}
?>