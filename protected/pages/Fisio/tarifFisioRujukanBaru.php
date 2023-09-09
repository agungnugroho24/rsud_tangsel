<?php
class tarifFisioRujukanBaru extends SimakConf
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
			$this->DDFisioKel->DataSource=FisioKelRecord::finder()->findAll();
			$this->DDFisioKel->dataBind();
			
			$this->DDFisioKateg->DataSource=FisioKategRecord::finder()->findAll();
			$this->DDFisioKateg->dataBind();
			
			$this->DDJenisFoto->DataSource=FisioTdkRecord::finder()->findAll();
			$this->DDJenisFoto->dataBind();
						
			$this->DDfisioRujuk->DataSource=FisioRujukanRecord::finder()->findAll();
			$this->DDfisioRujuk->dataBind();
						
			$this->DDfisioRujuk->focus();								
		}		
		else
		{
			$this->test->Text = '';
		}
	}
	
	public function DDFisioKelChanged($sender,$param)
	{/*
		if  ($this->DDFisioKel->SelectedValue=='')
		{
			$this->DDFisioKateg->DataSource=FisioKategRecord::finder()->findAll();
			$this->DDFisioKateg->dataBind();
			$this->DDFisioKateg->Enabled=false;
			$this->DDFisioKel->focus();
		}else
		{*/
			$this->DDFisioKateg->Enabled=true;
			$this->DDFisioKateg->focus();		
		//}
	}
	
	public function DDFisioKategChanged($sender,$param)
	{
		if  ($this->DDFisioKateg->SelectedValue=='')
		{
			$this->DDJenisFoto->DataSource=FisioKategRecord::finder()->findAll();
			$this->DDJenisFoto->dataBind();
			$this->DDJenisFoto->Enabled=false;
			$this->DDFisioKateg->focus();
		}else
		{
			$kel=$this->DDFisioKel->SelectedValue;
			$kateg=$this->DDFisioKateg->SelectedValue;
			$sql="select kode,nama from tbm_fisio_tindakan where kelompok = '$kel' AND kategori = '$kateg'";
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
			$idFisioRujukan = $this->DDfisioRujuk->SelectedValue;
			$nmFisioRujukan = $this->ambilTxt($this->DDfisioRujuk);
			
		    $sql = "SELECT * FROM tbm_fisio_rujukan_tarif WHERE id_tdk_fisio = '$idTdk' AND id_fisio_rujukan = '$idFisioRujukan'";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > 0)
			{
				$this->test->Text = '    
				<script type="text/javascript">
					alert("Tarif untuk ID Tindakan '.$idTdk.' dan Fisio '.$nmFisioRujukan.' sebagai Elektromedik Rujukan \nsudah ada dalam database.!");
					document.all.'.$this->DDfisioRujuk->getClientID().'.focus();
				</script>';
				
			}
			else
			{
				$newFisioRujukanTarifRecord=new FisioRujukanTarifRecord;
				$newFisioRujukanTarifRecord->id_tdk_fisio = TPropertyValue::ensureString($this->DDJenisFoto->SelectedValue);
				$newFisioRujukanTarifRecord->id_fisio_rujukan = $this->DDfisioRujuk->SelectedValue;
				$newFisioRujukanTarifRecord->tarif=$this->Tarif1->Text;	
				$newFisioRujukanTarifRecord->tarif1=$this->Tarif2->Text;	
				$newFisioRujukanTarifRecord->tarif2=$this->Tarif3->Text;	
				$newFisioRujukanTarifRecord->tarif3=$this->Tarif4->Text;	
				$newFisioRujukanTarifRecord->tarif4=$this->Tarif5->Text;	
				$newFisioRujukanTarifRecord->tarif5=$this->Tarif6->Text;
				
				$newFisioRujukanTarifRecord->save(); 			
				
				$this->test->Text = '    
				<script type="text/javascript">
					alert("Tarif Elektromedik Rujukan Baru Telah Masuk Dalam Database.");
					window.location="index.php?page=Fisio.tarifFisioRujukan"; 
				</script>';
				
				//$this->Response->redirect($this->Service->constructUrl('Fisio.tarifFisioRujukan'));
			}			
        }			
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Fisio.tarifFisioRujukan'));		
	}
}
?>