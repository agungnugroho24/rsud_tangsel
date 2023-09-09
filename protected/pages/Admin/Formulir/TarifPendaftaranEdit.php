<?php
class TarifPendaftaranEdit extends SimakConf
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
			$sql="SELECT * FROM tbm_poliklinik ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDPoliklinik->DataSource=$arrData;	
			$this->DDPoliklinik->dataBind();
			
			$data = $this->TarifPendaftaranRecord;
			$this->DDPoliklinik->SelectedValue = $data->id_klinik;	
			$this->shift->SelectedValue = $data->shift;	
			$this->tarifOperator->Text = floatval(TarifPendaftaranRecord::finder()->find('id=?',array($data->id))->tarif);
				
			$this->DDPoliklinik->Focus();
			$this->setViewState('poliklinikAwal',strtolower($data->id_klinik));
		}
	} 	
	
	public function simpanClicked($sender,$param)	
	{	
		if($this->IsValid)  // when all validations succeed
    {
			$poliklinik = strtolower(trim($this->DDPoliklinik->SelectedValue));
			$shift = $this->shift->SelectedValue;
			
			if($this->getViewState('poliklinikAwal') != $poliklinik)
			{
				$sql = "SELECT * FROM tbm_tarif_pendaftaran WHERE  id_klinik = '$poliklinik' AND shift='$shift' ";
				$arr = $this->queryAction($sql,'S');
				
				if($arr)
				{
					$this->getPage()->getClientScript()->registerEndScript
						('','unmaskContent();
							jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tarif Retribusi untuk poliklinik '.ucwords($this->ambilTxt($this->DDPoliklinik)).'</b> sudah ada dalam database !</p>\',timeout: 4000,dialog:{
							modal: true
						}});');	
							
					$this->Page->CallbackClient->focus($this->DDPoliklinik);	
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
		//UPDATE tbm_tarif_pendaftaran
		$tarifStandar = floatval($this->tarifOperator->Text);
		
		$data = $this->TarifPendaftaranRecord;  	
		$data->id_klinik = $this->DDPoliklinik->SelectedValue;
		$data->shift = $this->shift->SelectedValue;
		$data->tarif = $tarifStandar;
		$data->save();	
		
		$this->getPage()->getClientScript()->registerEndScript
		('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Tarif Retribusi untuk poliklinik '.ucwords($this->ambilTxt($this->DDPoliklinik)).'</b> telah sukses diperbaharui<br/></p>\',timeout: 600000,dialog:{
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
			$this->Page->CallbackClient->focus($this->DDPoliklinik);
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		}
		else
		{
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifPendaftaran'));
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
	
	protected function getTarifPendaftaranRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$TarifPendaftaranRecord=TarifPendaftaranRecord::finder()->findByPk($ID);		
		if(!($TarifPendaftaranRecord instanceof TarifPendaftaranRecord))
			throw new THttpException(500,'id tidak benar.');
		return $TarifPendaftaranRecord;
	}	
	
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifPendaftaran'));			
	}
}
?>