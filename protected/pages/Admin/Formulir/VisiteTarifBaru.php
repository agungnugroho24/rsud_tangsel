<?php

class VisiteTarifBaru extends SimakConf
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
			$sql="SELECT * FROM tbm_kamar_kelas ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDKelas->DataSource=$arrData;	
			$this->DDKelas->dataBind();
			
			$this->DDKelompok->focus();
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
        $param->IsValid=(TarifVisiteRwtInapRecord::finder()->findByPk($this->ID->Text)===null);		
    }	   
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$nama = $this->DDKelompok->SelectedValue;
			$kelas = $this->DDKelas->SelectedValue;
			
			$sql = "SELECT * FROM tbm_inap_visite WHERE kel_dokter = '$nama' AND kelas = '$kelas' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tarif Visite untuk kelompok dokter <b>'.ucwords($this->ambilTxt($this->DDKelompok)).'</b> di <b>'.ucwords($this->ambilTxt($this->DDKelas)).'</b> sudah ada dalam database !</p>\',timeout: 4000,dialog:{
							modal: true
						}});');	
						
				$this->Page->CallbackClient->focus($this->DDKelompok);	
			}
			else
			{
				$tarifStandar = floatval($this->tarifOperator->Text);
				
				//INSERT tbm_inap_visite
				$data = new TarifVisiteRwtInapRecord();		
				$data->kel_dokter = $this->DDKelompok->SelectedValue;
				$data->kelas = $this->DDKelas->SelectedValue;
				$data->tarif = $tarifStandar;
				$data->save();				
				
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Tarif Visite untuk kelompok dokter <b>'.ucwords($this->ambilTxt($this->DDKelompok)).'</b> <b>'.ucwords($this->ambilTxt($this->DDKelas)).'</b> telah ditambahkan dalam database. <br/><br/> Apakah akan menambah Tarif Visite baru lagi ?</p>\',timeout: 600000,dialog:{
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
				
				$this->DDKelompok->SelectedValue = 'empty';	
				$this->DDKelas->SelectedValue = 'empty';	
				$this->tarifOperator->Text = '0';				
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
			$this->Page->CallbackClient->focus($this->DDKelompok);
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		}
		else
		{
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.VisiteTarif'));	
		}
	}
	
	/*
	public function batalClicked($sender,$param)
	{		
		$this->ID->Text='';			
		$this->DDKelompok->Text='';		
		$this->alamat->Text='';		
		$this->telp->Text='';		
		$this->npwp->Text='';		
		$this->npkp->Text='';				
	}
	*/
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.VisiteTarif'));		
	}
}
?>