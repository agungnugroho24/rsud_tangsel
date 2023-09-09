<?php
class PerusPasienEdit extends SimakConf
{

	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('9');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	public function onPreLoad($param)
	{
		if(!$this->IsPostBack && !$this->IsCallBack)        
		{
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			
			$this->DDKel->DataSource = KelompokRecord::finder()->findAll($criteria);
			$this->DDKel->DataBind();
			
			$data = $this->PerusahaanRecord;
			$this->nama->Text = $data->nama;
			$this->DDKel->SelectedValue= $data->id_kel;
			$this->st->SelectedValue= $data->st;
				
			$this->nama->Focus();
			$this->setViewState('namaAwal',strtolower($data->nama));
			$this->setViewState('kelAwal',$data->id_kel);
			$this->setViewState('stAwal',$data->st);
		}
	} 
	
	public function simpanClicked($sender,$param)	
	{	
		if($this->IsValid)  // when all validations succeed
        {
			$nama = strtolower(trim($this->nama->Text));
			$idKel = $this->DDKel->SelectedValue;
			$st = $this->st->SelectedValue;
			
			if($this->getViewState('namaAwal') != $nama || $this->getViewState('kelAwal') != $idKel || $this->getViewState('stAwal') != $st)
			{
				$sql = "SELECT nama FROM tbm_perusahaan_asuransi WHERE LOWER(nama) = '$nama' AND id_kel = '$idKel' AND st='$st'";
				$arr = $this->queryAction($sql,'S');
				
				if($arr)
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Nama Perusahaan Asuransi untuk kelompok dan status perawatan yg dipilih sudah ada dalam database.</p>\',timeout: 4000,dialog:{
							modal: true,
							buttons: {
								"OK": function() {
									jQuery( this ).dialog( "close" );
								},
							}
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
	
	protected function prosesSimpan()
	{
		$idKel = $this->DDKel->SelectedValue;
		$st = $this->st->SelectedValue;
		
		//UPDATE tbm_perusahaan_asuransi
		$data = $this->PerusahaanRecord;  	
		$data->id_kel = $idKel;
		$data->nama = ucwords(trim($this->nama->Text));
		$data->st = $st;
		$data->save();	
		
		$this->getPage()->getClientScript()->registerEndScript
		('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Perusahaan Asuransi <b>'.ucwords($this->getViewState('namaAwal')).'</b> telah sukses diperbaharui<br/><br/></p>\',timeout: 600000,dialog:{
				modal: true,
				buttons: {
					"OK": function() {
						jQuery( this ).dialog( "close" );
						maskContent();
						konfirmasi(\'tidak\');
					}
				}
			}});');	
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
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.PerusPasien'));
		}
	}
	
	/*
	protected function originPath ()
	{
		$mode=$this->Request['mode'];
		if($mode == '01'){
			$balik='Tarif.NamaTindakan';
		}else if($mode == '02'){
			$balik='Pendaftaran.DaftarCariRwtJln';
		}else if($mode == '03'){
			$balik='Pendaftaran.DaftarCariRwtInap';
		}else if($mode == '04'){
			$balik='Pendaftaran.DaftarCariRwtIgd';
		}else if($mode == '05'){
			$balik='Pendaftaran.kunjPas';
		}else if($mode == '06'){
			$balik='Pendaftaran.kunjPasIgd';
		}
		return $balik;
	}
	*/
	
	protected function getPerusahaanRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$PerusahaanRecord=PerusahaanRecord::finder()->findByPk($ID);		
		if(!($PerusahaanRecord instanceof PerusahaanRecord))
			throw new THttpException(500,'id tidak benar.');
		return $PerusahaanRecord;
	}	
	
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.PerusPasien'));			
	}
}
?>