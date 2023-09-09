<?php
class tarifRadRujukanBaru extends SimakConf
{
 public function onLoad($param)
	{
		parent::onLoad($param);
		/*
		$tmpVar=$this->authApp('8');//ID aplikasi keKaban
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
		{
			$this->Response->redirect($this->Service->constructUrl('Home'));
		
		}
		*/
			
		if(!$this->IsPostBack){
			$this->DDRadKel->DataSource=CtScanKelRecord::finder()->findAll();
			$this->DDRadKel->dataBind();
			
			$this->DDRadKateg->DataSource=CtScanKategRecord::finder()->findAll();
			$this->DDRadKateg->dataBind();
			
			$this->DDJenisFoto->DataSource=CtScanTdkRecord::finder()->findAll();
			$this->DDJenisFoto->dataBind();
						
			$this->DDradRujuk->DataSource=CtScanRujukanRecord::finder()->findAll();
			$this->DDradRujuk->dataBind();
						
			$this->DDradRujuk->focus();								
		}		
		else
		{
			$this->test->Text = '';
		}
	}
	
	public function DDRadKelChanged($sender,$param)
	{/*
		if  ($this->DDRadKel->SelectedValue=='')
		{
			$this->DDRadKateg->DataSource=CtScanKategRecord::finder()->findAll();
			$this->DDRadKateg->dataBind();
			$this->DDRadKateg->Enabled=false;
			$this->DDRadKel->focus();
		}else
		{*/
			$this->DDRadKateg->Enabled=true;
			$this->DDRadKateg->focus();		
		//}
	}
	
	public function DDRadKategChanged($sender,$param)
	{
		if  ($this->DDRadKateg->SelectedValue=='')
		{
			$this->DDJenisFoto->DataSource=CtScanKategRecord::finder()->findAll();
			$this->DDJenisFoto->dataBind();
			$this->DDJenisFoto->Enabled=false;
			$this->DDRadKateg->focus();
		}else
		{
			$kel=$this->DDRadKel->SelectedValue;
			$kateg=$this->DDRadKateg->SelectedValue;
			$sql="select kode,nama from tbm_ctscan_tindakan where kelompok = '$kel' AND kategori = '$kateg'";
			$this->DDJenisFoto->DataSource=$this->queryAction($sql,'S');
			$this->DDJenisFoto->dataBind();
			$this->DDJenisFoto->Enabled=true;
			$this->DDJenisFoto->focus();	
			//$this->test->text=$kel.'+'.$kateg;
		}
	}
	
	public function DDJenisFotoChanged($sender,$param)
	{
		if  ($this-> DDJenisFoto->SelectedValue=='')
		{
			$this->Tarif1->Enabled=false;
			$this->Tarif2->Enabled=false;
			$this->Tarif3->Enabled=false;
			$this->Tarif4->Enabled=false;
			$this->Tarif5->Enabled=false;
			$this->Tarif6->Enabled=false;
			
			$this-> DDJenisFoto->focus();
		}else
		{
			$this->Tarif1->ReadOnly=false;
			$this->Tarif2->ReadOnly=false;
			$this->Tarif3->ReadOnly=false;
			$this->Tarif4->ReadOnly=false;
			$this->Tarif5->ReadOnly=false;
			$this->Tarif6->ReadOnly=false;
			
			$this->Tarif1->Enabled=true;
			$this->Tarif2->Enabled=true;
			$this->Tarif3->Enabled=true;
			$this->Tarif4->Enabled=true;
			$this->Tarif5->Enabled=true;
			$this->Tarif6->Enabled=true;
			
			$this->Tarif1->focus();		
		}
		//$this->test->text=$this->DDJenisFoto->SelectedValue;
	}
	
	public function simpanClicked($sender,$param)
	{			
		if($this->IsValid)  // when all validations succeed
        {
			$idTdk = $this->DDJenisFoto->SelectedValue;
			$idRadRujukan = $this->DDradRujuk->SelectedValue;
			$nmRadRujukan = $this->ambilTxt($this->DDradRujuk);
			
		    $sql = "SELECT * FROM tbm_ctscan_rujukan_tarif WHERE id_tdk_ctscan = '$idTdk' AND id_ctscan_rujukan = '$idRadRujukan'";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > 0)
			{
				$this->test->Text = '    
				<script type="text/javascript">
					alert("Tarif untuk ID Tindakan '.$idTdk.' dan Rad '.$nmRadRujukan.' sebagai CT Scan Rujukan \nsudah ada dalam database.!");
					document.all.'.$this->DDradRujuk->getClientID().'.focus();
				</script>';
				
			}
			else
			{
				$newCtScanRujukanTarifRecord=new CtScanRujukanTarifRecord;
				$newCtScanRujukanTarifRecord->id_tdk_ctscan = TPropertyValue::ensureString($this->DDJenisFoto->SelectedValue);
				$newCtScanRujukanTarifRecord->id_ctscan_rujukan = $this->DDradRujuk->SelectedValue;
				$newCtScanRujukanTarifRecord->tarif=$this->Tarif1->Text;	
				$newCtScanRujukanTarifRecord->tarif1=$this->Tarif2->Text;	
				$newCtScanRujukanTarifRecord->tarif2=$this->Tarif3->Text;	
				$newCtScanRujukanTarifRecord->tarif3=$this->Tarif4->Text;	
				$newCtScanRujukanTarifRecord->tarif4=$this->Tarif5->Text;	
				$newCtScanRujukanTarifRecord->tarif5=$this->Tarif6->Text;
				
				$newCtScanRujukanTarifRecord->save(); 			
				
				$this->test->Text = '    
				<script type="text/javascript">
					alert("Tarif CT Scan Rujukan Baru Telah Masuk Dalam Database.");
					window.location="index.php?page=CtScan.tarifRadRujukan"; 
				</script>';
				
				//$this->Response->redirect($this->Service->constructUrl('CtScan.tarifRadRujukan'));
			}			
        }			
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('CtScan.tarifRadRujukan'));		
	}
}
?>