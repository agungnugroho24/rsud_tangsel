<?php
class BayarRetribusi extends SimakConf
{	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	public function onPreRender($param)
	{
		parent::onPreRender($param);
		if(!$this->IsPostBack && !$this->IsCallBack)  
		{			
			if($this->Request['id'] != '')
			{
				$id = $this->Request['id'];
				$data = KasirPendaftaranRecord::finder()->findByPk($this->Request['id']);
				$no_trans = $data->no_trans;
				$cm = RwtjlnRecord::finder()->findByPk($no_trans)->cm;
				$id_klinik = RwtjlnRecord::finder()->findByPk($no_trans)->id_klinik;
				$nama = PasienRecord::finder()->findByPk($cm)->nama;
				$klinik = PoliklinikRecord::finder()->findByPk($id_klinik)->nama;
				
				$this->txt2->Text = $cm;
				$this->txt3->Text = $nama;
				$this->txt4->Text = 'Rp. '.number_format($data->tarif,'2',',','.');
				$this->txt6->Text = $klinik;
				
				$this->cetakBtn->Enabled = false;
				$this->bayar->focus();
			}
		}
	}	
	
	public function bayarChanged($sender,$param)
	{
		$id = $this->Request['id'];
		$data = KasirPendaftaranRecord::finder()->findByPk($this->Request['id']);
		$tarif = $data->tarif;
		
		$bayar = trim($this->bayar->Text);
		if(intval($bayar) && abs($bayar) > 0)
		{
			if(intval($bayar) >= $tarif)
			{
				$this->bayar->Text = intval($bayar);
				$this->txt5->Text = 'Rp. '.number_format($bayar-$tarif,'2',',','.');	
				$this->cetakBtn->Enabled = true;
				$this->Page->CallbackClient->focus($this->cetakBtn);
			}
			else
			{
				$this->cetakBtn->Enabled = false;
				$this->bayar->Text = '';		
				$this->txt5->Text = '';		
				$this->Page->CallbackClient->focus($this->bayar);
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					window.parent.jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error"><strong>Pembayaran kurang!</strong><br/><br/></p>\',timeout: 4000,dialog:{
						modal: true
					}});');	
			}
		}
		else
		{
			$this->cetakBtn->Enabled = false;
			$this->bayar->Text = '';		
			$this->txt5->Text = '';		
			$this->Page->CallbackClient->focus($this->bayar);
			$this->getPage()->getClientScript()->registerEndScript
			('','unmaskContent();
				window.parent.jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error"><strong>Jumlah bayar tidak sesuai!</strong><br/><br/></p>\',timeout: 4000,dialog:{
					modal: true
				}});');	
		}
	}
	
	public function simpanClicked()
	{
		$this->Response->reload();	
	}
	
	public function cetakClicked()
	{			
		if($this->Request['id'] != '')
		{
			$id = $this->Request['id'];
			$data = KasirPendaftaranRecord::finder()->findByPk($this->Request['id']);
			$data->st_flag = '1';
			$data->tgl_kasir=date('y-m-d');
			$data->wkt_kasir=date('G:i:s');
			$data->operator_kasir=$this->User->Name;
			$data->save();
		}
			
		$this->getPage()->getClientScript()->registerEndScript('','window.parent.maskContent(); window.parent.prosesModal("'.$this->Request['id'].'"); jQuery.FrameDialog.closeDialog();');		
	}
	
	public function clear()
	{		
		$this->nama->Text = '';
	}
	
    public function batalClicked($sender,$param)
	{		
		$this->msg->Text = '';
		$this->getPage()->getClientScript()->registerEndScript('','window.parent.maskContent(); window.parent.prosesModal(); jQuery.FrameDialog.closeDialog();');
		//$this->getPage()->getClientScript()->registerEndScript('','window.parent.maskContent(); window.parent.prosesModal("'.$this->Request['id'].'"); jQuery.FrameDialog.closeDialog();');
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('admin.KabAdmin'));		
	}
}
?>