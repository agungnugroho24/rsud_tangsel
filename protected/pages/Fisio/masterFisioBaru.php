<?php
class masterFisioBaru extends SimakConf
{
	public function onInit($param)
	{		
		parent::onInit($param);
		$tmpVar=$this->authApp('6');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	}
	
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
			$this->DDFisioKateg->DataSource=FisioKategRecord::finder()->findAll();
			$this->DDFisioKateg->dataBind();
			
			$this->DDFisioKel->DataSource=FisioKelRecord::finder()->findAll();
			$this->DDFisioKel->dataBind();
						
			$this->nama->focus();								
		}		
	}
	
	public function checkRM($sender,$param)
    {       
		// valid if the id kabupaten is not found in the database
		$param->IsValid=(FisioTdkRecord::finder()->findByPk($this->ID->Text)===null);
    }
	
	public function simpanClicked($sender,$param)
	{			
		if($this->IsValid)  // when all validations succeed
        {
			$nama = strtolower(trim($this->nama->Text));
			
			$kel = $this->DDFisioKel->SelectedValue;
			$namaKel =  $this->ambilTxt($this->DDFisioKel);
			
			$kateg = $this->DDFisioKateg->SelectedValue;
			$namaKateg =  $this->ambilTxt($this->DDFisioKateg);
			
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
				//INSERT tbm_fisio_tindakan
				$newFisioTdkRecord=new FisioTdkRecord;
				$newFisioTdkRecord->kategori = $this->DDFisioKateg->SelectedValue;
				$newFisioTdkRecord->kelompok = $this->DDFisioKel->SelectedValue;        
				$newFisioTdkRecord->nama = ucwords($this->nama->Text);
				$newFisioTdkRecord->save();
				
				//INSERT tbm_lab_tarif
				$sql = "SELECT kode FROM tbm_fisio_tindakan ORDER BY kode DESC";
				$kodeTdk = FisioTdkRecord::finder()->findBySql($sql)->kode;
					
				$newLabTarifRecord = new FisioTarifRecord;
				$newLabTarifRecord->id = $kodeTdk;
				$newLabTarifRecord->tarif = $this->Tarif1->Text;	
				$newLabTarifRecord->tarif1 = $this->Tarif2->Text;	
				$newLabTarifRecord->tarif2 = $this->Tarif3->Text;	
				$newLabTarifRecord->tarif3 = $this->Tarif4->Text;	
				$newLabTarifRecord->save(); 
				
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Tindakan '.ucwords($this->nama->Text).' telah ditambahkan dalam database. <br/><br/> Apakah akan menambah tindakan baru lagi ?</p>\',timeout: 600000,dialog:{
						modal: true,
						buttons: {
							"Ya": function() {
								jQuery( this ).dialog( "close" );
								konfirmasi(\'ya\');
							},
							Tidak: function() {
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
				
				//$this->Response->redirect($this->Service->constructUrl('Fisio.masterFisio'));
			}	
        }	
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
		}		
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
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Fisio.masterFisio'));		
	}
}
?>