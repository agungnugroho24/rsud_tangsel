<?php
class tarifLabRujukanBaru extends SimakConf
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
			$this->DDRadKel->DataSource=LabKelRecord::finder()->findAll();
			$this->DDRadKel->dataBind();
			
			$this->DDRadKateg->DataSource=LabKategRecord::finder()->findAll();
			$this->DDRadKateg->dataBind();
			
			$this->DDJenisFoto->DataSource=LabTdkRecord::finder()->findAll();
			$this->DDJenisFoto->dataBind();
						
			$this->DDlabRujuk->DataSource=LabRujukanRecord::finder()->findAll();
			$this->DDlabRujuk->dataBind();
						
			$this->DDlabRujuk->focus();								
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
			$this->DDRadKateg->DataSource=RadKategRecord::finder()->findAll();
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
			$this->DDJenisFoto->DataSource=LabKategRecord::finder()->findAll();
			$this->DDJenisFoto->dataBind();
			$this->DDJenisFoto->Enabled=false;
			$this->DDRadKateg->focus();
		}else
		{
			$kel=$this->DDRadKel->SelectedValue;
			$kateg=$this->DDRadKateg->SelectedValue;
			if  ($kateg=='0')
			{
				$sql="select kode,nama from tbm_lab_tindakan where kode like 'U%'";
			}else{
				$sql="select kode,nama from tbm_lab_tindakan where kelompok = '$kel' AND kategori = '$kateg'";
			}
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
			$idLabRujukan = $this->DDlabRujuk->SelectedValue;
			$nmLabRujukan = $this->ambilTxt($this->DDlabRujuk);
			
		    $sql = "SELECT * FROM tbm_lab_rujukan_tarif WHERE id_tdk_lab = '$idTdk' AND id_lab_rujukan = '$idLabRujukan'";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > 0)
			{
				$this->test->Text = '    
				<script type="text/javascript">
					alert("Tarif untuk ID Tindakan '.$idTdk.' dan Lab '.$nmLabRujukan.' sebagai Laboratorium Rujukan \nsudah ada dalam database.!");
					document.all.'.$this->DDlabRujuk->getClientID().'.focus();
				</script>';
				
			}
			else
			{
				$newLabTarifRecord=new LabRujukanTarifRecord;
				$newLabTarifRecord->id_tdk_lab = TPropertyValue::ensureString($this->DDJenisFoto->SelectedValue);
				$newLabTarifRecord->id_lab_rujukan = $this->DDlabRujuk->SelectedValue;
				$newLabTarifRecord->tarif=$this->Tarif1->Text;	
				$newLabTarifRecord->tarif1=$this->Tarif2->Text;	
				$newLabTarifRecord->tarif2=$this->Tarif3->Text;	
				$newLabTarifRecord->tarif3=$this->Tarif4->Text;	
				$newLabTarifRecord->tarif4=$this->Tarif5->Text;	
				$newLabTarifRecord->tarif5=$this->Tarif6->Text;
				
				$newLabTarifRecord->save(); 			
				
				$this->test->Text = '    
				<script type="text/javascript">
					alert("Tarif Laboratorium Rujukan Baru Telah Masuk Dalam Database.");
					window.location="index.php?page=Lab.tarifLabRujukan"; 
				</script>';
				
				//$this->Response->redirect($this->Service->constructUrl('Lab.tarifLabRujukan'));
			}			
        }			
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Lab.tarifLabRujukan'));		
	}
}
?>