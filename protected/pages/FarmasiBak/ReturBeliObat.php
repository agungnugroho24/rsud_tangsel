<?php
class ReturBeliObat extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('3');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{	
			$this->notrans->Enabled=true;	
			
			$this->DDObat->DataSource=ObatRecord::finder()->findAll();
			$this->DDObat->dataBind();
			
			$this->DDObat->Enabled=true;
		}
    }
	
	public function chObat($sender,$param)
	{
		$nofak=$this->notrans->text;
		$obat=$this->DDObat->SelectedValue;
		$sql="SELECT 
				  tbt_obat_masuk.jumlah AS jumlah
				FROM
				  tbt_obat_masuk
				WHERE
				  tbt_obat_masuk.no_faktur = '$nofak' AND
				  tbt_obat_masuk.id_obat = '$obat'
				GROUP BY
				  tbt_obat_masuk.id_obat";		
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{			
			$this->jml->Text=$row['jumlah'];	
		}	
	}
		
	public function batalClicked($sender,$param)
    {	
		$this->Response->Reload();
	}
		
	public function checkRegister($sender,$param)
    {			
		$nofak=$this->notrans->text;
		$sql="SELECT 
				  tbt_obat_masuk.id_obat AS kode,
				  tbm_obat.nama AS nama
				FROM
				  tbt_obat_masuk
				  INNER JOIN tbm_obat ON (tbt_obat_masuk.id_obat = tbm_obat.kode)
				WHERE
				  tbt_obat_masuk.no_faktur = '$nofak'
				GROUP BY
				  tbt_obat_masuk.id_obat";
		$arr=$this->queryAction($sql,'S');
		$jmlData=count($arr);	
		if($jmlData>0)
		{
			$this->DDObat->DataSource=$arr;		
			$this->DDObat->dataBind();
			$this->DDObat->Enabled=true;
		}
    }
	
	public function keluarClicked($sender,$param)
	{	
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}	
	
	public function simpanClicked($sender,$param)
    {	
		if($this->IsValid)  // when all validations succeed
        {
			$nofak=$this->notrans->text;
			$obat=$this->DDObat->SelectedValue;
			$jml=$this->jml->Text;
			$sql="UPDATE tbt_obat_masuk SET jumlah = jumlah-'$jml' WHERE no_faktur = '$nofak' AND id_obat = '$obat'";
			$apdet=$this->queryAction($sql,'C');
			$sql="UPDATE tbt_stok_gudang SET jumlah = jumlah-'$jml' WHERE id_obat = '$obat'";
			$apdet=$this->queryAction($sql,'C');
			$this->Response->Reload();
		}
	}
}
?>
