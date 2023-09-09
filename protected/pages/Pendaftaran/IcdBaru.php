<?php
class IcdBaru extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	 public function onPreRender($param)
	{
		parent::onPreRender($param);
		if(!$this->IsPostBack && !$this->IsCallBack)  
		{
			
		}
		
	}	
	
	public function onLoad($param)
	{
		if(!$this->IsPostBack && !$this->IsCallBack)  
		{						
			$this->kode->focus();			
		}
	}
	
	public function modeInputChanged()
	{
		$this->kode->Text = '';
		if($this->modeInput->SelectedValue == '0')
		{
			$this->valKode->Enabled = false;
			$this->kode->Enabled = false;
		}
		else
		{
			$this->valKode->Enabled = true;
			$this->kode->Enabled = true;
		}
	}
	
	public function noUrutIcd()
    {	
		//Mbikin No Urut
		$find = IcdRecord::finder();		
		$sql = "SELECT kode FROM tbm_icd WHERE SUBSTR(tbm_icd.kode,1,2) = 'NS' GROUP BY kode ORDER by kode desc LIMIT 1";
		$no = $find->findBySql($sql);
		
		if($no==NULL)//jika kosong bikin ndiri
		{
			$today=date("Ym");
			$urut='00000001';
			//$notrans=$today.$consModul.$urut;
			$notrans = 'NS'.$urut;
		}
		else
		{	
			$urut = intval(substr($no->getColumnValue('kode'),2,8));
			if ($urut==99999999)
			{
				$urut=1;
				$urut=substr('00000000',0,8-strlen($urut)).$urut;
				//$notrans=$today.$consModul.$urut;// '01' adalah kode untuk rawat jalan
				$notrans = 'NS'.$urut;
			}else{
				$urut=$urut+1;
				$urut=substr('00000000',0,8-strlen($urut)).$urut;
				//$notrans=$today.$consModul.$urut;// '01' adalah kode untuk rawat jalan
				$notrans = 'NS'.$urut;
			}	
		}
		return $notrans;
	}	
	
	public function simpanClicked()
	{			
		if($this->Page->IsValid)  // when all validations succeed
        {	
			$kode = strtolower(trim($this->kode->Text));
			
			if($this->modeInput->SelectedValue > 0)
			{
				$sql = "SELECT kode FROM tbm_icd WHERE LOWER(kode) = '$kode'";
			
				if(IcdRecord::finder()->findBySql($sql))
				{
					$this->Page->CallbackClient->focus($this->kode);
					$this->getPage()->getClientScript()->registerEndScript
					('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Kode yang dimasukan sudah ada. Silakan ganti dengan kode yang lain.</p>\',timeout: 3000,dialog:{
						modal: true}});');	
				}
				else
				{
					$data= new IcdRecord();
					
					if($this->modeInput->SelectedValue > 0)
						$data->kode = trim($this->kode->Text);	
					else
						$data->kode = $this->noUrutIcd();
					
					$data->dtd = trim($this->dtd->Text);
					$data->kat = trim($this->kat->Text);
					$data->indonesia = trim($this->nmIndo->Text);
					$data->inggris = trim($this->nmEng->Text);
					$data->save();	
					
					$this->getPage()->getClientScript()->registerEndScript
					('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Data ICD baru telah dimasukan dalam database!</p>\',timeout: 3000,dialog:{
						modal: true}});');
					
					$this->clear();
					$this->Page->CallbackClient->focus($this->kode);
				}
			}
			else
			{
				$data= new IcdRecord();
					
				if($this->modeInput->SelectedValue > 0)
					$data->kode = trim($this->kode->Text);	
				else
					$data->kode = $this->noUrutIcd();
				
				$data->dtd = trim($this->dtd->Text);
				$data->kat = trim($this->kat->Text);
				$data->indonesia = trim($this->nmIndo->Text);
				$data->inggris = trim($this->nmEng->Text);
				$data->save();	
				
				$this->getPage()->getClientScript()->registerEndScript
				('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Data ICD baru telah dimasukan dalam database!</p>\',timeout: 3000,dialog:{
					modal: true}});');
				
				$this->clear();
				$this->Page->CallbackClient->focus($this->kode);
			}
		}			
	}
	
	public function clear()
	{		
		$this->kode->Text = '';
		$this->dtd->Text = '';
		$this->kat->Text = '';
		$this->nmIndo->Text = '';
		$this->nmEng->Text = '';
	}
	
    public function batalClicked($sender,$param)
	{		
		$this->msg->Text = '';
		$this->clear();
		$this->Page->CallbackClient->focus($this->kode);
		//$this->Response->redirect($this->Service->constructUrl('listBiblio'));		
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.IcdAdmin'));		
	}
}
?>