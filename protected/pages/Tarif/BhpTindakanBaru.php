<?php

class BhpTindakanBaru extends SimakConf
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
			/*
			$sql = "SELECT * FROM tbm_poliklinik ORDER BY nama ";
			$this->DDKlinik->DataSource = PoliklinikRecord::finder()->findAllBySql($sql);
			$this->DDKlinik->dataBind();
			*/
		}
	}
		
	public function checkRM($sender,$param)
    {
        // valid if the username is not found in the database
        $param->IsValid=(OperasiNamaRecord::finder()->findByPk($this->ID->Text)===null);		
    }	   
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$nama = strtolower(trim($this->nama->Text));
			$jenis = $this->jenis->SelectedValue;
			
			$sql = "SELECT nama FROM tbm_bhp_tindakan WHERE LOWER(nama) = '$nama' AND st='$jenis'";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Nama BHP Tindakan :<br/> <b>'.ucwords($this->nama->Text).'</b> sudah ada dalam database !</p>\',timeout: 4000,dialog:{
							modal: true
						}});');	
						
				$this->Page->CallbackClient->focus($this->nama);	
			}
			else
			{
				//INSERT tbm_bhp_tindakan
				$kodeTdk = $this->numUrut('tbm_bhp_tindakan',BhpTindakanRecord::finder(),4);
				
				$data = new BhpTindakanRecord();		
				$data->id = $kodeTdk;
				$data->nama = ucwords($this->nama->Text);	
				$data->tarif = floatval($this->tarifOperator->Text);
				$data->st = $this->jenis->SelectedValue;
				$data->save();	
				
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">BHP Tindakan <b>'.ucwords($this->nama->Text).'</b> telah ditambahkan dalam database. <br/><br/> Apakah akan menambah BHP Tindakan baru lagi ?</p>\',timeout: 600000,dialog:{
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
				$this->jenis->SelectedValue = '0';	
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
			$this->Page->CallbackClient->focus($this->nama);
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		}
		else
		{
			$this->Response->redirect($this->Service->constructUrl('Tarif.BhpTindakan'));	
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
		$this->Response->redirect($this->Service->constructUrl('Tarif.BhpTindakan'));		
	}

}
?>
