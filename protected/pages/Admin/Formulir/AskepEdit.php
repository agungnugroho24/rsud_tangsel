<?php
class AskepEdit extends SimakConf
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
			$sql="SELECT * FROM tbm_kamar_kelas ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDKelas->DataSource=$arrData;	
			$this->DDKelas->dataBind();
			
			$data = $this->AskepRecord;
			$this->nama->Text = $data->nama;	
			$this->DDKelas->Text = $data->kelas;	
			$this->tarifOperator->Text = floatval($data->tarif);
			
			if($data->st_persentase_kenaikan == '1')
				$this->stKenaikan->Checked = true;
			else
				$this->stKenaikan->Checked = false;
			
			$this->nama->Focus();
			$this->setViewState('namaAwal',strtolower($data->nama));
			$this->setViewState('kelasAwal',strtolower($data->kelas));
		}
	} 	
	
	public function simpanClicked($sender,$param)	
	{	
		if($this->IsValid)  // when all validations succeed
        {
			$nama = strtolower(trim($this->nama->Text));
			$kelas = $this->DDKelas->SelectedValue;
			
			if($this->getViewState('namaAwal') != $nama || $this->getViewState('kelasAwal') != $kelas)
			{
				$sql = "SELECT nama FROM tbm_askep WHERE LOWER(nama) = '$nama' AND kelas = '$kelas' ";
				$arr = $this->queryAction($sql,'S');
				
				if($arr)
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Jasa Asuhan Keperawatan dengan nama <b>'.$this->nama->Text.'</b> di <b>'.ucwords($this->ambilTxt($this->DDKelas)).'</b> sudah ada dalam database !</p>\',timeout: 4000,dialog:{
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
		//UPDATE tbm_rad_tindakan
		$data = $this->AskepRecord;  	
		$data->nama = ucwords($this->nama->Text);
		$data->kelas = $this->DDKelas->Text;
		$data->tarif = floatval($this->tarifOperator->Text);
		
		if($this->stKenaikan->Checked == true)
			$data->st_persentase_kenaikan = '1';
		else
			$data->st_persentase_kenaikan = '0';
		
		$data->save();	
		
		
		$this->getPage()->getClientScript()->registerEndScript
		('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Jasa Asuhan Keperawatan  <b>'.ucwords($this->getViewState('namaAwal')).'</b> di <b>'.ucwords($this->ambilTxt($this->DDKelas)).'</b> telah sukses diperbaharui<br/><br/></p>\',timeout: 600000,dialog:{
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
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.Askep'));
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
	
	protected function getAskepRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$AskepRecord=AskepRecord::finder()->findByPk($ID);		
		if(!($AskepRecord instanceof AskepRecord))
			throw new THttpException(500,'id tidak benar.');
		return $AskepRecord;
	}	
	
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.Askep'));			
	}
}
?>