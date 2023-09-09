<?php
class BhpTindakanEdit extends SimakConf
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
			/*
			$sql = "SELECT * FROM tbm_poliklinik ORDER BY nama ";
			$this->DDKlinik->DataSource = PoliklinikRecord::finder()->findAllBySql($sql);
			$this->DDKlinik->dataBind();
			*/
			
			$data = $this->BhpTindakanRecord;
			$this->nama->Text = $data->nama;
			$this->jenis->SelectedValue = $data->st;
			$this->tarifOperator->Text = floatval($data->tarif);
				
			$this->nama->Focus();
			$this->setViewState('namaAwal',strtolower($data->nama));
			$this->setViewState('stAwal',$data->st);
		}
	} 	
	
	public function simpanClicked($sender,$param)	
	{	
		if($this->IsValid)  // when all validations succeed
        {
			$nama = strtolower(trim($this->nama->Text));
			$st = $this->jenis->SelectedValue;
			
			if($this->getViewState('namaAwal') != $nama || $this->getViewState('stAwal') != $st)
			{
				$sql = "SELECT nama FROM tbm_bhp_tindakan WHERE LOWER(nama) = '$nama' AND st='$st'";
				$arr = $this->queryAction($sql,'S');
				
				if($arr)
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Nama Tindakan :<br/> <b>'.ucwords($this->nama->Text).'</b> untuk jenis <b>'.$this->ambilTxt($this->jenis).'</b><br/> sudah ada dalam database !</p>\',timeout: 4000,dialog:{
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
		//UPDATE tbm_bhp_tindakan
		$data = $this->BhpTindakanRecord;  	
		$data->nama = ucwords($this->nama->Text);	
		$data->tarif = floatval($this->tarifOperator->Text);
		$data->st = $this->jenis->SelectedValue;	
		$data->save();	
		
		$this->getPage()->getClientScript()->registerEndScript
		('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">BHP Tindakan <b>'.ucwords($this->getViewState('namaAwal')).'</b> telah sukses diperbaharui<br/><br/></p>\',timeout: 600000,dialog:{

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
			$this->Response->redirect($this->Service->constructUrl('Tarif.BhpTindakan'));
		}
	}
	
	/*
	protected function originPath ()
	{
		$mode=$this->Request['mode'];
		if($mode == '01'){
			$balik='Tarif.BhpTindakan';
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
	
	protected function getBhpTindakanRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$BhpTindakanRecord=BhpTindakanRecord::finder()->findByPk($ID);		
		if(!($BhpTindakanRecord instanceof BhpTindakanRecord))
			throw new THttpException(500,'id tidak benar.');
		return $BhpTindakanRecord;
	}	
	
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Tarif.BhpTindakan'));			
	}
}
?>