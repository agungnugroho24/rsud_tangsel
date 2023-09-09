<?php
class masterRadEdit extends SimakConf
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
			$this->DDRadKateg->DataSource=RadKategRecord::finder()->findAll();
			$this->DDRadKateg->dataBind(); 
			
			$this->DDRadKel->DataSource=RadKelRecord::finder()->findAll();
			$this->DDRadKel->dataBind();
			
			$dataRadTdkRecord=$this->RadTdkRecord;
			
		// Populates the input controls with the existing user data
			$this->ID->Text=$dataRadTdkRecord->kode;
			$this->nama->Text=$dataRadTdkRecord->nama;			
			$this->DDRadKateg->SelectedValue=$dataRadTdkRecord->kategori;
			$this->DDRadKel->SelectedValue=$dataRadTdkRecord->kelompok;
			
			$dataTarif = RadTarifRecord::finder()->find('id = ?',$dataRadTdkRecord->kode);
			$this->Tarif1->Text = $dataTarif->tarif;
			$this->Tarif2->Text = $dataTarif->tarif1;
			$this->Tarif3->Text = $dataTarif->tarif2;
			$this->Tarif4->Text = $dataTarif->tarif3;
			
			$this->nama->Focus();
			
			$this->setViewState('namaAwal',strtolower($dataRadTdkRecord->nama));
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
			
		$kel = $this->DDRadKel->SelectedValue;
		$namaKel =  $this->ambilTxt($this->DDRadKel);
		
		$kateg = $this->DDRadKateg->SelectedValue;
		$namaKateg =  $this->ambilTxt($this->DDRadKateg);
		
		if($this->IsValid)  // when all validations succeed
        {
			if($this->getViewState('namaAwal') != $nama)
			{
				$sql = "SELECT nama FROM tbm_rad_tindakan WHERE LOWER(nama) = '$nama' AND kategori = '$kateg' AND kelompok = '$kel' ";
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
		$dataRadTdkRecord = $this->RadTdkRecord;  
		$dataRadTdkRecord->nama = ucwords($this->nama->Text);				
		$dataRadTdkRecord->kategori = $this->DDRadKateg->SelectedValue;
		$dataRadTdkRecord->kelompok = $this->DDRadKel->SelectedValue; 
		$dataRadTdkRecord->save(); 	
		
		$dataTarif = RadTarifRecord::finder()->find('id = ?',$dataRadTdkRecord->kode);
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
		$this->DDRadKateg->SelectedValue = 'empty';
		$this->DDRadKel->SelectedValue = 'empty';
		
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
			$this->Response->redirect($this->Service->constructUrl('Rad.masterRad'));	
		}
	}
		
	protected function getRadTdkRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$RadTdkRecord=RadTdkRecord::finder()->findByKode($ID);		
		if(!($RadTdkRecord instanceof RadTdkRecord))
			throw new THttpException(500,'id tidak benar.');
		return $RadTdkRecord;
	}	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Rad.masterRad'));		
	}
}
?>