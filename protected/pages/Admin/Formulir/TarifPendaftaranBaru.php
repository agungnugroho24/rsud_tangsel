<?php

class TarifPendaftaranBaru extends SimakConf
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
			$sql="SELECT * FROM tbm_poliklinik ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDPoliklinik->DataSource=$arrData;	
			$this->DDPoliklinik->dataBind();
			
			$this->DDPoliklinik->focus();
		}
	}
	
	public function repeaterDataBound($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			$item->nmPoliklinik->Text = ucwords($item->DataItem['nama']);
        }
    }
		
	public function checkRM($sender,$param)
    {
        // valid if the username is not found in the database
        $param->IsValid=(TarifPendaftaranRecord::finder()->findByPk($this->ID->Text)===null);		
    }	   
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$poliklinik = $this->DDPoliklinik->SelectedValue;
			$shift = $this->shift->SelectedValue;
			
			$sql = "SELECT * FROM tbm_tarif_pendaftaran WHERE  id_klinik = '$poliklinik' AND shift='$shift' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tarif Retribusi untuk poliklinik '.ucwords($this->ambilTxt($this->DDPoliklinik)).'</b> sudah ada dalam database !</p>\',timeout: 4000,dialog:{
							modal: true
						}});');	
						
				$this->Page->CallbackClient->focus($this->DDPoliklinik);	
			}
			else
			{
				$tarifStandar = floatval($this->tarifOperator->Text);
				
				//INSERT tbm_tarif_pendaftaran
				$data = new TarifPendaftaranRecord();	
				$data->id_klinik = $this->DDPoliklinik->SelectedValue;
				$data->shift = $this->shift->SelectedValue;
				$data->tarif = $tarifStandar;
				$data->save();				
				
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Tarif Retribusi untuk poliklinik '.ucwords($this->ambilTxt($this->DDPoliklinik)).'</b> telah ditambahkan dalam database. <br/><br/> Apakah akan menambah Tarif Retribusi baru lagi ?</p>\',timeout: 600000,dialog:{
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
				
				$this->DDPoliklinik->SelectedValue = 'empty';	
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
			$this->Page->CallbackClient->focus($this->DDPoliklinik);
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		}
		else
		{
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifPendaftaran'));	
		}
	}
	
	/*
	public function batalClicked($sender,$param)
	{		
		$this->ID->Text='';			
		$this->DDPoliklinik->Text='';		
		$this->alamat->Text='';		
		$this->telp->Text='';		
		$this->npwp->Text='';		
		$this->npkp->Text='';				
	}
	*/
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifPendaftaran'));		
	}
}
?>