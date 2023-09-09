<?php
class DerivatObatBaru extends SimakConf
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
			
		if(!$this->IsPostBack){
		
			$sql="SELECT id FROM tbm_derivat_obat order by id desc";
			$no = DerivatObatRecord::finder()->findBySql($sql);
				
			if($no==NULL)//jika kosong bikin ndiri
			{	
				$urut='001';
				$urut=$no.$urut;
			}else
			{
				$no1=intval(substr($no->getColumnValue('id'),1,4));
				if ($no1==999)
				{
					$urut='D001';
				}else
				{
					$urut=$no1+1;
					$urut='D'.substr('000',-3,3-strlen($urut)).$urut;					
				}
			}
			$this->ID->Text=$urut;		
		
			$this->DDGol->DataSource=GolObatRecord::finder()->findAll();
			$this->DDGol->dataBind();
						
			$this->ID->focus();	
			
			$this->DDGol->DataSource=GolObatRecord::finder()->findAll();
			$this->DDGol->dataBind();
			
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll();
			$this->DDKlas->dataBind();
						
			$this->ID->focus();								
		}		
	}
	
	public function selectionChangedGol($sender,$param)
	{		
		if($this->DDGol->SelectedValue != ''){
			$id_gol = $this->DDGol->SelectedValue;	
			//$this->setViewState('idKab',$kab,'');
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll('gol_id = ?', $id_gol);
			$this->DDKlas->dataBind(); 	
			$this->DDKlas->Enabled=true;
		}
		else{
			$this->DDKlas->Enabled=false;
		}
	} 
	
	public function checkRM($sender,$param)
    {       
		// valid if the id kabupaten is not found in the database
		$param->IsValid=(DerivatObatRecord::finder()->findByPk($this->ID->Text)===null);
    }
	
	public function simpanClicked($sender,$param)
	{			
		if($this->IsValid)  // when all validations succeed
        {
           	//$idGol=$this->DDGol->SelectedValue;
           // $sql = "SELECT * FROM tbm_golongan_obat WHERE id = '$idGol'";
        	//$cariNamaGol=GolObatRecord::finder()->findBySql($sql);
			
		    // populates a UserRecord object with user inputs
			$DerObatRecord=new DerivatObatRecord;
			$DerObatRecord->gol_id=TPropertyValue::ensureString($this->DDGol->SelectedValue);
			$DerObatRecord->klas_id=TPropertyValue::ensureString($this->DDKlas->SelectedValue);
			$DerObatRecord->id=ucwords($this->ID->Text);		            
            $DerObatRecord->nama=ucwords($this->nama->Text);
			//$KlasifikasiObatRecord->nama_kab=ucwords($cariNamaKab->getColumnValue('nama'));
			
			// saves to the database via Active Record mechanism
            $DerObatRecord->save(); 			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.DerivatObat'));
        }			
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.DerivatObat'));		
	}
}
?>