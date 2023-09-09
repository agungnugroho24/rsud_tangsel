<?php
class KlasObatBaru extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
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
			
		if(!$this->IsPostBack)
		{
			$sql="SELECT id FROM tbm_klasifikasi_obat order by id desc";
			$no = KlasifikasiObatRecord::finder()->findBySql($sql);
				
			if($no==NULL)//jika kosong bikin ndiri
			{	
				$urut='001';
				$urut=$no.$urut;
			}else
			{
				$no1=intval(substr($no->getColumnValue('id'),1,4));
				if ($no1==999)
				{
					$urut='K001';
				}else
				{
					$urut=$no1+1;
					$urut='K'.substr('000',-3,3-strlen($urut)).$urut;					
				}
			}
			$this->ID->Text=$urut;		
		
			$this->DDGol->DataSource=GolObatRecord::finder()->findAll();
			$this->DDGol->dataBind();
						
			$this->ID->focus();								
		}		
	}
	
	public function checkRM($sender,$param)
    {       
		// valid if the id kabupaten is not found in the database
		$param->IsValid=(KlasifikasiObatRecord::finder()->findByPk($this->ID->Text)===null);
    }
	
	public function simpanClicked($sender,$param)
	{			
		if($this->IsValid)  // when all validations succeed
        {
           	//$idGol=$this->DDGol->SelectedValue;
           // $sql = "SELECT * FROM tbm_golongan_obat WHERE id = '$idGol'";
        	//$cariNamaGol=GolObatRecord::finder()->findBySql($sql);
			
		    // populates a UserRecord object with user inputs
			$KlasifikasiObatRecord=new KlasifikasiObatRecord;
			$KlasifikasiObatRecord->gol_id=TPropertyValue::ensureString($this->DDGol->SelectedValue);
			$KlasifikasiObatRecord->id=ucwords($this->ID->Text);		            
            $KlasifikasiObatRecord->jenis=ucwords($this->nama->Text);
			//$KlasifikasiObatRecord->nama_kab=ucwords($cariNamaKab->getColumnValue('nama'));
			
			// saves to the database via Active Record mechanism
            $KlasifikasiObatRecord->save(); 			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.KlasObat'));
        }			
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.KlasObat'));		
	}
}
?>