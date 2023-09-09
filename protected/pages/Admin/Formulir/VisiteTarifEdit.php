<?php
class VisiteTarifEdit extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('9');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	public function onPreRender($param)
	{
		parent::onPreRender($param);
		if(!$this->IsPostBack && !$this->IsCallBack)        
		{
			$sql="SELECT * FROM tbm_kamar_kelas ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDKelas->DataSource=$arrData;	
			$this->DDKelas->dataBind();
			
			$data = $this->TarifVisiteRwtInapRecord;
			$this->DDKelompok->SelectedValue = $data->kel_dokter;	
			$this->DDKelas->SelectedValue = $data->kelas;	
			$this->tarifOperator->Text = floatval(TarifVisiteRwtInapRecord::finder()->find('id=?',array($data->id))->tarif);
				
			$this->DDKelompok->Focus();
			$this->setViewState('namaAwal',strtolower($data->kel_dokter));
			$this->setViewState('kelasAwal',strtolower($data->kelas));
		}
	} 	
	
	public function simpanClicked($sender,$param)	
	{	
		if($this->IsValid)  // when all validations succeed
    {
			$nama = strtolower(trim($this->DDKelompok->SelectedValue));
			$kelas = strtolower(trim($this->DDKelas->SelectedValue));
			
			if($this->getViewState('namaAwal') != $nama || $this->getViewState('kelasAwal') != $kelas)
			{
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
		//UPDATE tbm_inap_visite
		$tarifStandar = floatval($this->tarifOperator->Text);
		
		$data = $this->TarifVisiteRwtInapRecord;  	
		$data->kel_dokter = $this->DDKelompok->SelectedValue;
		$data->kelas = $this->DDKelas->SelectedValue;
		$data->tarif = $tarifStandar;
		$data->save();	
		
		$this->getPage()->getClientScript()->registerEndScript
		('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Tarif Visite untuk kelompok dokter <b>'.ucwords($this->ambilTxt($this->DDKelompok)).'</b> <b>'.ucwords($this->ambilTxt($this->DDKelas)).'</b> telah sukses diperbaharui<br/><br/></p>\',timeout: 600000,dialog:{
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
			$this->Page->CallbackClient->focus($this->DDKelompok);
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		}
		else
		{
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.VisiteTarif'));
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
	
	protected function getTarifVisiteRwtInapRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$TarifVisiteRwtInapRecord=TarifVisiteRwtInapRecord::finder()->findByPk($ID);		
		if(!($TarifVisiteRwtInapRecord instanceof TarifVisiteRwtInapRecord))
			throw new THttpException(500,'id tidak benar.');
		return $TarifVisiteRwtInapRecord;
	}	
	
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.VisiteTarif'));			
	}
}
?>