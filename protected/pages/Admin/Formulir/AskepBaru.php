<?php

class AskepBaru extends SimakConf
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
        $param->IsValid=(AskepRecord::finder()->findByPk($this->ID->Text)===null);		
    }	   
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$nama = strtolower(trim($this->nama->Text));
			$kelas = $this->DDKelas->SelectedValue;
			
			$sql = "SELECT nama FROM tbm_askep WHERE LOWER(nama) = '$nama' AND kelas = '$kelas' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Jasa Asuhan Keperawatan dengan nama :<br/> <b>'.ucwords($this->nama->Text).'</b> di <b>'.ucwords($this->ambilTxt($this->DDKelas)).'</b> sudah ada dalam database !</p>\',timeout: 4000,dialog:{
							modal: true
						}});');	
						
				$this->Page->CallbackClient->focus($this->nama);	
			}
			else
			{
				//INSERT tbm_askep
				$data = new AskepRecord();		
				$data->nama = $this->nama->Text;
				$data->kelas = $this->DDKelas->Text;
				$data->tarif = floatval($this->tarifOperator->Text);
				
				if($this->stKenaikan->Checked == true)
					$data->st_persentase_kenaikan = '1';
				else
					$data->st_persentase_kenaikan = '0';
					
				$data->save();		
				
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Jasa Asuhan Keperawatan <b>'.ucwords($this->nama->Text).'</b> di <b>'.ucwords($this->ambilTxt($this->DDKelas)).'</b> telah ditambahkan dalam database. <br/><br/> Apakah akan menambah data baru lagi?</p>\',timeout: 600000,dialog:{
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
				$this->DDKelas->SelectedValue = 'empty';	
				$this->tarifOperator->Text = '0';	
				$this->stKenaikan->Checked = false;
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
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.Askep'));	
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
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.Askep'));		
	}
}
?>