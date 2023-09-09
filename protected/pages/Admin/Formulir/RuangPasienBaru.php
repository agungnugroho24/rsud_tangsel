<?php

class RuangPasienBaru extends SimakConf
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
			
			$sql = "SELECT * FROM tbm_kamar_kelas ORDER BY nama ";
			$this->DDKelas->DataSource = KamarKelasRecord::finder()->findAllBySql($sql);
			$this->DDKelas->dataBind();	
			
			$sql = "SELECT * FROM tbm_kamar_nama ORDER BY nama ";
			$this->DDKamar->DataSource = KamarNamaRecord::finder()->findAllBySql($sql);
			$this->DDKamar->dataBind();	
		}
	}
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$nama = strtolower(trim($this->nama->Text));
			$kelas = $this->DDKelas->SelectedValue;
			$kamar = $this->DDKamar->SelectedValue;
			$jmlBed = intval($this->jmlBed->Text);
			$jmlBedPakai = intval($this->jmlBedPakai->Text);
			
			$sql = "SELECT nama FROM tbm_ruang WHERE LOWER(nama) = '$nama' AND id_kelas = '$kelas' AND id_jns_kamar = '$kamar' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Ruang dengan nama :<br/> <b>'.ucwords($this->nama->Text).'</b><br/> untuk kelas dan kamar yg dipilih sudah ada dalam database !</p>\',timeout: 4000,dialog:{
							modal: true
						}});');	
						
				$this->Page->CallbackClient->focus($this->nama);	
			}
			else
			{
				//INSERT tbm_rad_tindakan
				$data = new RuangRecord();		
				$data->nama = ucwords($this->nama->Text);
				$data->id_kelas = $kelas;
				$data->id_jns_kamar = $kamar;
				$data->jml_bed = $jmlBed;
				$data->jml_bed_pakai = $jmlBedPakai;
				$data->save();	
				
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Ruang Rawat Inap <b>'.ucwords($this->nama->Text).'</b> telah ditambahkan dalam database. <br/><br/> Apakah akan menambah kelas lagi ?</p>\',timeout: 600000,dialog:{
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
				$this->DDKelas->Text = 'empty';
				$this->DDKamar->Text = 'empty';
				$this->jmlBed->Text = '0';
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
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.RuangPasien'));	
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
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.RuangPasien'));		
	}
}
?>