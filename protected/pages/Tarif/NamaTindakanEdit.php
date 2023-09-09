<?php
class NamaTindakanEdit extends SimakConf
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
			$sql = "SELECT * FROM tbm_poliklinik ORDER BY nama ";
			$this->DDKlinik->DataSource = PoliklinikRecord::finder()->findAllBySql($sql);
			$this->DDKlinik->dataBind();
			
			$data = $this->NamaTindakanRecord;
			
			// Populates the input controls with the existing user data	
			$this->nama->Text = $data->nama;
			$this->DDKlinik->SelectedValue = $data->id_klinik;
			
			$this->tarifOperator->Text = floatval(TarifTindakanRecord::finder()->find('id=?',array($data->id))->biaya1);
				
			$this->nama->Focus();
			$this->setViewState('namaAwal',strtolower($data->nama));
			$this->setViewState('poliAwal',$data->id_klinik);
		}
	} 	
	
	public function simpanClicked($sender,$param)	
	{	
		if($this->IsValid)  // when all validations succeed
        {
			$nama = strtolower(trim($this->nama->Text));
			$idKlinik = $this->DDKlinik->SelectedValue;
			
			if($this->getViewState('namaAwal') != $nama || $this->getViewState('poliAwal') != $idKlinik)
			{
				$sql = "SELECT nama FROM tbm_nama_tindakan WHERE LOWER(nama) = '$nama' AND id_klinik='$idKlinik'";
				$arr = $this->queryAction($sql,'S');
				
				if($arr)
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Nama Tindakan :<br/> <b>'.ucwords($this->nama->Text).'</b> untuk poliklinik <b>'.$this->ambilTxt($this->DDKlinik).'</b><br/> sudah ada dalam database !</p>\',timeout: 4000,dialog:{
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
		//UPDATE tbm_nama_tindakan
		$data = $this->NamaTindakanRecord;  	
		$data->nama = ucwords($this->nama->Text);				
		$data->id_klinik = $this->DDKlinik->SelectedValue;	
		$data->save();	
		
		$idTindakan = $data->id;
		
		$tarifStandar = floatval($this->tarifOperator->Text);
			
		//INSERT tbm_tarif_tindakan
		$dataTarif = TarifTindakanRecord::finder()->find('id=?',array($idTindakan));
		//$dataTarif->js_dok_obgyn = $tarifStandar + ($tarifStandar * $persentaseKenaikan / 100);
		$dataTarif->biaya1 = $tarifStandar;
		$dataTarif->save(); 	
		
		$this->getPage()->getClientScript()->registerEndScript
		('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Nama Tindakan <b>'.ucwords($this->getViewState('namaAwal')).'</b> telah sukses diperbaharui<br/><br/></p>\',timeout: 600000,dialog:{

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
			$this->Response->redirect($this->Service->constructUrl('Tarif.NamaTindakan'));
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
	
	protected function getNamaTindakanRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$NamaTindakanRecord=NamaTindakanRecord::finder()->findByPk($ID);		
		if(!($NamaTindakanRecord instanceof NamaTindakanRecord))
			throw new THttpException(500,'id tidak benar.');
		return $NamaTindakanRecord;
	}	
	
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Tarif.NamaTindakan'));			
	}
}
?>