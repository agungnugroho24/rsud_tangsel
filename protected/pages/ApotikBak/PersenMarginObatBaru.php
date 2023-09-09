<?php

class PersenMarginObatBaru extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('3');
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
        $param->IsValid=(ObatKelompokMarginRecord::finder()->findByPk($this->ID->Text)===null);		
    }	   
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$nama = mysql_escape_string(strtolower(trim($this->nama->Text)));
			
			$sql = "SELECT nama FROM tbm_obat_kelompok_margin WHERE LOWER(nama) = '$nama' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Kelompok Margin :<br/> <b>'.ucwords($this->nama->Text).'</b><br/> sudah ada dalam database !</p>\',timeout: 4000,dialog:{
							modal: true
						}});');	
						
				$this->Page->CallbackClient->focus($this->nama);	
			}
			else
			{				
				//INSERT tbm_obat_kelompok_margin
				$data = new ObatKelompokMarginRecord();		
				$data->nama = $this->nama->Text;
				$data->persentase = floatval($this->persentase1->Text);
				$data->persentase_asuransi = floatval($this->persentase2->Text);
				$data->persentase_jamper = floatval($this->persentase3->Text);
				$data->persentase_unit_internal = floatval($this->persentase4->Text);
				$data->save();	
				
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Kelompok Margin <b>'.mysql_escape_string(ucwords($this->nama->Text)).'</b> telah ditambahkan dalam database. <br/><br/> Apakah akan menambah Kelompok Margin baru lagi ?</p>\',timeout: 600000,dialog:{
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
				$this->persentase1->Text = '0';	
				$this->persentase2->Text = '0';	
				$this->persentase3->Text = '0';	
				$this->persentase4->Text = '0';	
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
			$this->Response->redirect($this->Service->constructUrl('Apotik.PersenMarginObat'));	
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
		$this->Response->redirect($this->Service->constructUrl('Apotik.PersenMarginObat'));		
	}
}
?>
