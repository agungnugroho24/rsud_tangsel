<?php
class masterFisioEdit extends SimakConf
{
 	public function onInit($param)
	{		
		parent::onInit($param);
		$tmpVar=$this->authApp('6');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	}
	
	public function onPreLoad($param)
	{
		if(!$this->IsPostBack)  // if the page is initially requested
		{
			$this->DDFisioKateg->DataSource=FisioKategRecord::finder()->findAll();
			$this->DDFisioKateg->dataBind(); 
			
			$this->DDFisioKel->DataSource=FisioKelRecord::finder()->findAll();
			$this->DDFisioKel->dataBind();
			
			$dataFisioTdkRecord=$this->FisioTdkRecord;
			
		// Populates the input controls with the existing user data
			$this->ID->Text=$dataFisioTdkRecord->kode;
			$this->nama->Text=$dataFisioTdkRecord->nama;			
			$this->DDFisioKateg->SelectedValue=$dataFisioTdkRecord->kategori;
			$this->DDFisioKel->SelectedValue=$dataFisioTdkRecord->kelompok;
			
			$dataTarif = FisioTarifRecord::finder()->find('id = ?',$dataFisioTdkRecord->kode);
			$this->Tarif1->Text = $dataTarif->tarif;
			$this->Tarif2->Text = $dataTarif->tarif1;
			$this->Tarif3->Text = $dataTarif->tarif2;
			$this->Tarif4->Text = $dataTarif->tarif3;
			
			$this->nama->Focus();
			
			$this->setViewState('namaAwal',strtolower($dataFisioTdkRecord->nama));
		}
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
	
	public function simpanClicked($sender,$param)	
	{			
		$nama = strtolower(trim($this->nama->Text));
			
		$kel = $this->DDFisioKel->SelectedValue;
		$namaKel =  $this->ambilTxt($this->DDFisioKel);
		
		$kateg = $this->DDFisioKateg->SelectedValue;
		$namaKateg =  $this->ambilTxt($this->DDFisioKateg);
		
		if($this->IsValid)  // when all validations succeed
        {
			if($this->getViewState('namaAwal') != $nama)
			{
				$sql = "SELECT nama FROM tbm_fisio_tindakan WHERE LOWER(nama) = '$nama' AND kategori = '$kateg' AND kelompok = '$kel' ";
				$arr = $this->queryAction($sql,'S');
				
				if($arr)
				{
					$this->getPage()->getClientScript()->registerEndScript
						('','unmaskContent();
							jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan '.ucwords($this->nama->Text).' untuk kelompok '.$namaKel.' dan kategori '.$namaKateg.' sudah ada dalam database !</p>\',timeout: 4000,dialog:{
								modal: true
							}});');	
							
					$this->Page->CallbackClient->focus($this->nama);	
				}
				else
				{
					$this->prosesSimpan();
				}		
			}
			else
			{
				$this->prosesSimpan();
			}
        }			
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
		}
	}
	
	public function prosesSimpan()
	{
		$dataFisioTdkRecord = $this->FisioTdkRecord;  
		$dataFisioTdkRecord->nama = ucwords($this->nama->Text);				
		$dataFisioTdkRecord->kategori = $this->DDFisioKateg->SelectedValue;
		$dataFisioTdkRecord->kelompok = $this->DDFisioKel->SelectedValue; 
		$dataFisioTdkRecord->save(); 	
		
		$dataTarif = FisioTarifRecord::finder()->find('id = ?',$dataFisioTdkRecord->kode);
		$dataTarif->tarif = $this->Tarif1->Text;	
		$dataTarif->tarif1 = $this->Tarif2->Text;	
		$dataTarif->tarif2 = $this->Tarif3->Text;	
		$dataTarif->tarif3 = $this->Tarif4->Text;	
		$dataTarif->save(); 
		
		$this->getPage()->getClientScript()->registerEndScript
		('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Tindakan '.ucwords($this->getViewState('namaAwal')).' telah sukses diperbaharui<br/><br/></p>\',timeout: 600000,dialog:{
				modal: true,
				buttons: {
					"OK": function() {
						jQuery( this ).dialog( "close" );
						maskContent();
						konfirmasi(\'tidak\');
					}
				}
			}});');	
		
		$this->nama->Text = '';
		$this->DDFisioKateg->SelectedValue = 'empty';
		$this->DDFisioKel->SelectedValue = 'empty';
		
		$this->Tarif1->Text = '0';
		$this->Tarif2->Text = '0';
		$this->Tarif3->Text = '0';
		$this->Tarif4->Text = '0';
	}
	
	public function prosesKonfirmasi($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
		
		//$this->getPage()->getClientScript()->registerEndScript('','alert('.$mode.')');	
				
		if($mode == 'ya')
		{	
			$this->Page->CallbackClient->focus($this->nama);
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		}
		else
		{
			$this->Response->redirect($this->Service->constructUrl('Fisio.masterFisio'));	
		}
	}
		
	protected function getFisioTdkRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$FisioTdkRecord=FisioTdkRecord::finder()->findByKode($ID);		
		if(!($FisioTdkRecord instanceof FisioTdkRecord))
			throw new THttpException(500,'id tidak benar.');
		return $FisioTdkRecord;
	}	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Fisio.masterFisio'));		
	}
}
?>