<?php
class IcdMasterEdit extends SimakConf
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
			$this->ambilData();			
			$this->dtd->focus();		
		}
	}
	
	protected function getIcdRecord()
	{
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$IcdRecord=IcdRecord::finder()->findByPk($ID);		
		if(!($IcdRecord instanceof IcdRecord))
			throw new THttpException(500,'id tidak benar.');
		return $IcdRecord;
	}
	
	public function ambilData()
	{	
		$IcdRecord = $this->IcdRecord;
		
		$this->kode->Text = $IcdRecord->kode;
		$this->dtd->Text = $IcdRecord->dtd;
		$this->kat->Text = $IcdRecord->kat;
		$this->nmIndo->Text = $IcdRecord->indonesia;
		$this->nmEng->Text = $IcdRecord->inggris;
		
		$this->setViewState('kodeAwal',$IcdRecord->kode);
	}
	
	public function simpanClicked()
	{			
		if($this->Page->IsValid)  // when all validations succeed
        {	
			$kode = strtolower(trim($this->kode->Text));
			
			if($kode == strtolower(trim($this->getViewState('kodeAwal'))))
			{				
				$IcdRecord= $this->IcdRecord;	
				$IcdRecord->kode = trim($this->kode->Text);
				$IcdRecord->dtd = trim($this->dtd->Text);
				$IcdRecord->kat = trim($this->kat->Text);
				$IcdRecord->indonesia = trim($this->nmIndo->Text);
				$IcdRecord->inggris = trim($this->nmEng->Text);
				$IcdRecord->save();	
				
				$this->msg->Text = '    
				<script type="text/javascript">
					alert("Data telah diperbaharui !");
					window.location="index.php?page=Pendaftaran.IcdAdmin"; 
				</script>';
			}
			else
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
					$IcdRecord= $this->IcdRecord;	
					$IcdRecord->kode = trim($this->kode->Text);
					$IcdRecord->dtd = trim($this->dtd->Text);
					$IcdRecord->kat = trim($this->kat->Text);
					$IcdRecord->indonesia = trim($this->nmIndo->Text);
					$IcdRecord->inggris = trim($this->nmEng->Text);
					$IcdRecord->save();	
					
					/*$this->getPage()->getClientScript()->registerEndScript
						('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>'.trim($this->kode->Text).'.</p>\',timeout: 3000,dialog:{
							modal: true}});');*/
						
					$this->msg->Text = '    
						<script type="text/javascript">
							alert("Data telah diperbaharui !");
							window.location="index.php?page=Pendaftaran.IcdAdmin"; 
						</script>';
					
					
					//$this->clear();
					$this->Page->CallbackClient->focus($this->kode);
				}
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