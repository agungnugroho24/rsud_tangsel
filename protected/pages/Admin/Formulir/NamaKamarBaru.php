<?php

class NamaKamarBaru extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('9');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	public function onLoad($param)
	{				
		parent::onLoad($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)        		
		{			
			$this->nama->focus();
		}
	}
	
	public function repeaterDataBound($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			$item->nmKelas->Text = ucwords($item->DataItem['nama']);
        }
    }
		
	public function checkRM($sender,$param)
    {
        // valid if the username is not found in the database
        $param->IsValid=(KamarNamaRecord::finder()->findByPk($this->ID->Text)===null);		
    }	   
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$nama = strtolower(trim($this->nama->Text));
			
			$sql = "SELECT nama FROM tbm_kamar_nama WHERE LOWER(nama) = '$nama' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Kamar Rawat Inap dengan nama :<br/> <b>'.ucwords($this->nama->Text).'</b><br/> sudah ada dalam database !</p>\',timeout: 4000,dialog:{
							modal: true
						}});');	
						
				$this->Page->CallbackClient->focus($this->nama);	
			}
			else
			{
				//INSERT tbm_kamar_nama
				$data = new KamarNamaRecord();		
				$data->nama = $this->nama->Text;
				$data->save();				
				
				
				//INSERT tbm_operasi_kamar_tarif
				$sql = "SELECT id FROM tbm_kamar_nama ORDER BY id DESC";
				$idKamar = KamarNamaRecord::finder()->findBySql($sql)->id;
				$tarifStandar = floatval($this->tarifOperator->Text);
				
				//INSERT tbm_operasi_kamar_tarif
				if(floatval($this->tarifOperator->Text)) //kelas III
				{
					$dataTarif = new KamarHargaRecord;
					$dataTarif->id_kmr = $idKamar;
					$dataTarif->id_kls = '5';
					$dataTarif->tarif = $tarifStandar;
					$dataTarif->save(); 
				}
				
				if(floatval($this->tarifOperator2->Text)) //kelas II
				{
					$dataTarif = new KamarHargaRecord;
					$dataTarif->id_kmr = $idKamar;
					$dataTarif->id_kls = '4';
					$dataTarif->tarif = $this->tarifOperator2->Text;
					$dataTarif->save(); 
				}
				
				if(floatval($this->tarifOperator3->Text)) //kelas I
				{
					$dataTarif = new KamarHargaRecord;
					$dataTarif->id_kmr = $idKamar;
					$dataTarif->id_kls = '1';
					$dataTarif->tarif = $this->tarifOperator3->Text;
					$dataTarif->save(); 
				}
				
				if(floatval($this->tarifOperator4->Text)) //kelas I Utama
				{
					$dataTarif = new KamarHargaRecord;
					$dataTarif->id_kmr = $idKamar;
					$dataTarif->id_kls = '3';
					$dataTarif->tarif = $this->tarifOperator4->Text;
					$dataTarif->save(); 
				}
				
				if(floatval($this->tarifOperator5->Text)) //kelas VIP
				{
					$dataTarif = new KamarHargaRecord;
					$dataTarif->id_kmr = $idKamar;
					$dataTarif->id_kls = '2';
					$dataTarif->tarif = $this->tarifOperator5->Text;
					$dataTarif->save(); 
				}
				
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Kamar Rawat Inap <b>'.ucwords($this->nama->Text).'</b> telah ditambahkan dalam database. <br/><br/> Apakah akan menambah kamar rawat inap baru lagi ?</p>\',timeout: 600000,dialog:{
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
				$this->tarifOperator->Text = '0';	
				$this->tarifOperator2->Text = '0';	
				$this->tarifOperator3->Text = '0';	
				$this->tarifOperator4->Text = '0';	
				$this->tarifOperator5->Text = '0';	
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
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.NamaKamar'));	
		}
	}
	
	/*
	public function batalClicked($sender,$param)
	{		
		$this->ID->Text='';			
		$this->nama->Text='';		
		$this->alamat->Text='';		
		$this->telp->Text='';		
		$this->npwp->Text='';		
		$this->npkp->Text='';				
	}
	*/
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.NamaKamar'));		
	}
}
?>