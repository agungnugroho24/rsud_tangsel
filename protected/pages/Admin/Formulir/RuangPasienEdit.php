<?php
class RuangPasienEdit extends SimakConf
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
			$sql = "SELECT * FROM tbm_kamar_kelas ORDER BY nama ";
			$this->DDKelas->DataSource = KamarKelasRecord::finder()->findAllBySql($sql);
			$this->DDKelas->dataBind();	
			
			$sql = "SELECT * FROM tbm_kamar_nama ORDER BY nama ";
			$this->DDKamar->DataSource = KamarNamaRecord::finder()->findAllBySql($sql);
			$this->DDKamar->dataBind();	
			
			$data = $this->RuangRecord;
			$this->nama->Text = $data->nama;
			$this->DDKelas->SelectedValue = $data->id_kelas;
			$this->DDKamar->SelectedValue = $data->id_jns_kamar;
			$this->jmlBed->Text = $data->jml_bed;
			$this->jmlBedPakai->Text = $data->jml_bed_pakai;
				
			$this->nama->Focus();
			$this->setViewState('namaAwal',strtolower($data->nama));
			$this->setViewState('kelasAwal',$data->id_kelas);
			$this->setViewState('kamarAwal',$data->id_jns_kamar);
			$this->setViewState('jmlAwal',$data->jml_bed);
		}
	} 
	
	public function simpanClicked($sender,$param)	
	{	
		if($this->IsValid)  // when all validations succeed
        {
			$nama = strtolower(trim($this->nama->Text));
			$kelas = $this->DDKelas->SelectedValue;
			$kamar = $this->DDKamar->SelectedValue;
			$jmlBed = intval($this->jmlBed->Text);
			
			if($this->getViewState('namaAwal') != $nama || $this->getViewState('kelasAwal') != $kelas || $this->getViewState('kamarAwal') != $kamar)
			{
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
		$nama = strtolower(trim($this->nama->Text));
		$kelas = $this->DDKelas->SelectedValue;
		$kamar = $this->DDKamar->SelectedValue;
		$jmlBed = intval($this->jmlBed->Text);
		$jmlBedPakai = intval($this->jmlBedPakai->Text);
		
		//UPDATE tbm_rad_tindakan
		$data = $this->RuangRecord;  	
		$data->nama = ucwords($this->nama->Text);
		$data->id_kelas = $kelas;
		$data->id_jns_kamar = $kamar;
		$data->jml_bed = $jmlBed;
		$data->jml_bed_pakai = $jmlBedPakai;
		/*
		if($this->getViewState('jmlAwal') > $jmlBed)
			$data->jml_bed_pakai = $data->jml_bed_pakai	+ ($this->getViewState('jmlAwal') - $jmlBed);
		elseif($this->getViewState('jmlAwal') < $jmlBed)
		{
			if($data->jml_bed_pakai > 0)
				$data->jml_bed_pakai = $data->jml_bed_pakai	- ($jmlBed - $this->getViewState('jmlAwal'));
		}
		*/
		$data->save();	
		
		$this->getPage()->getClientScript()->registerEndScript
		('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Ruang Rawat Inap dengan nama <b>'.ucwords($this->getViewState('namaAwal')).'</b> telah sukses diperbaharui<br/><br/></p>\',timeout: 600000,dialog:{
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
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.RuangPasien'));
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
	
	protected function getRuangRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$RuangRecord=RuangRecord::finder()->findByPk($ID);		
		if(!($RuangRecord instanceof RuangRecord))
			throw new THttpException(500,'id tidak benar.');
		return $RuangRecord;
	}	
	
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.RuangPasien'));			
	}
}
?>