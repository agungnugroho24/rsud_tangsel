<?php
class mainSimpeg extends SimakConf
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if(!$this->IsPostBack){	
			$this->DDKabKota->DataSource=KabupatenRecord::finder()->findAll();
			$this->DDKabKota->dataBind();
				
			//temp tabel Anak
			if($this->getViewState('tempTblAnak'))
			{
				$tempTblAnak = $this->getViewState('tempTblAnak');
				$sql="SELECT * FROM $tempTblAnak ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				$this->DGanak->DataSource=$arrData;
				$this->DGanak->dataBind();						
			}	
			
			//temp tabel Kepangkatan
			if($this->getViewState('tempTblPangkat'))
			{
				$tempTblPangkat = $this->getViewState('tempTblPangkat');
				$sql="SELECT * FROM $tempTblPangkat ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				$this->DGpangkat->DataSource=$arrData;
				$this->DGpangkat->dataBind();						
			}	
			
			//temp tabel Kepangkatan
			if($this->getViewState('tempTblJabatan'))
			{
				$tempTblJabatan = $this->getViewState('tempTblJabatan');
				$sql="SELECT * FROM $tempTblJabatan ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				$this->DGjabatan->DataSource=$arrData;
				$this->DGjabatan->dataBind();						
			}	
			
			//temp tabel Pendidikan
			if($this->getViewState('tempTblPendidikan'))
			{
				$tempTblPendidikan = $this->getViewState('tempTblPendidikan');
				$sql="SELECT * FROM $tempTblPendidikan ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				$this->DGpendidikan->DataSource=$arrData;
				$this->DGpendidikan->dataBind();						
			}	
			
			//temp tabel Pelatihan penjenjangan
			if($this->getViewState('tempTblPelatihan'))
			{
				$tempTblPelatihan = $this->getViewState('tempTblPelatihan');
				$sql="SELECT * FROM $tempTblPelatihan ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				$this->DGpelatihan->DataSource=$arrData;
				$this->DGpelatihan->dataBind();						
			}	
			
			//temp tabel Pelatihan Teknis
			if($this->getViewState('tempTblPelatihanTek'))
			{
				$tempTblPelatihanTek = $this->getViewState('tempTblPelatihanTek');
				$sql="SELECT * FROM $tempTblPelatihanTek ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				$this->DGpelatihanTek->DataSource=$arrData;
				$this->DGpelatihanTek->dataBind();						
			}	
			
			//temp tabel Pelatihan Teknis
			if($this->getViewState('tempTblPenghargaan'))
			{
				$tempTblPenghargaan = $this->getViewState('tempTblPenghargaan');
				$sql="SELECT * FROM $tempTblPenghargaan ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				$this->DGPenghargaan->DataSource=$arrData;
				$this->DGPenghargaan->dataBind();						
			}	
			
		}		
	}
	
	public function onLoadComplete($param)
   	{
   		parent::onLoadComplete($param);
		$this->nmAnak->Text='';
		$this->DPtglLhrAnak->Text='';
						
		$this->nmPangkat->Text='';
		$this->gol->Text='';
		$this->skTMT->Text='';
		$this->nmPejabat->Text='';
		
		$this->nmJabatan->Text='';
		$this->DPtglAwalJbtn->Text='';
		$this->DPtglAkhirJbtn->Text=''; 
		$this->sf->Text='';
		$this->eselon->Text='';
		
		$this->nmPendidikan->Text='';
		$this->thnLulus->Text='';
		$this->jur->Text='';
		$this->instanPenyelenggara->Text='';
		
		$this->nmPelatihan->Text='';
		$this->thnPelatihan->Text='';
		$this->lamaPelatihan->Text='';
		$this->penyelenggaraPelatihan->Text='';
		
		$this->nmPelatihanTek->Text='';
		$this->thnPelatihanTek->Text='';
		$this->lamaPelatihanTek->Text='';
		$this->penyelenggaraPelatihanTek->Text='';
		
		$this->nmPenghargaan->Text='';
		$this->DPtglPenghargaan->Text='';
		$this->noPenghargaan->Text='';
		$this->instanPenghargaan->Text='';
	}	
	
	
	public function cekNipGabung($sender,$param)
    {       
		// valid if the nip is not found in the database
		$NimTmp=$this->nip1->Text.$this->nip2->Text.$this->nip3->Text;
        $param->IsValid=SimpegRecord::finder()->findByPk($NimTmp)===null;
    }
	
	public function cekNipKurang($sender,$param)
    {       
		// valid if the nip is not found in the database
		$NimTmp=$this->nip1->Text.$this->nip2->Text.$this->nip3->Text;	
        $param->IsValid=strlen($NimTmp)==9;
    }
	
	public function tbhAnakClick()
   	{   		
		if (!$this->getViewState('tempTblAnak'))
		{
		$tempTblAnak = 'temp_';
		$tempTblAnak .= substr(microtime(),2,5);		
		$this->setViewState('tempTblAnak',$tempTblAnak);
		$sql="CREATE TABLE $tempTblAnak (id INT (2) auto_increment, nama VARCHAR(30), tgl_lahir DATE , jns_kelamin VARCHAR(30), PRIMARY KEY (id)) ENGINE = MEMORY";
		
		$this->queryAction($sql,'C');//Create new tabel bro...							 
		$nama=$this->nmAnak->Text;
		$tgl_lahir=$this->ConvertDate($this->DPtglLhrAnak->Text,'2');
		$jns_kelamin=TPropertyValue::ensureString($this->collectSelectionListResult($this->jkAnak));
		
		$sql="INSERT INTO $tempTblAnak (nama,tgl_lahir,jns_kelamin) VALUES ('$nama','$tgl_lahir','$jns_kelamin')";
		$this->queryAction($sql,'C');//Insert new row in tabel bro...
		
		$sql="SELECT * FROM $tempTblAnak ORDER BY id";
		$this->DGanak->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
		$this->DGanak->dataBind();						
		}
		else
		{
			$tempTblAnak = $this->getViewState('tempTblAnak');						 
			$nama=$this->nmAnak->Text;
			$tgl_lahir=$this->ConvertDate($this->DPtglLhrAnak->Text,'2');
			$jns_kelamin=TPropertyValue::ensureString($this->collectSelectionListResult($this->jkAnak));
			
			$sql="INSERT INTO $tempTblAnak (nama,tgl_lahir,jns_kelamin) VALUES ('$nama','$tgl_lahir','$jns_kelamin')";
			$this->queryAction($sql,'C');//Insert new row in tabel bro...
			
			$sql="SELECT * FROM $tempTblAnak ORDER BY id";
			$this->DGanak->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->DGanak->dataBind();					
		}						
		
		$this->nmAnak->focus();
   	} 
	
	public function tbhPangkatClick()
   	{   		
		if (!$this->getViewState('tempTblPangkat'))
		{
		$tempTblPangkat = 'temp_';
		$tempTblPangkat .= substr(microtime(),2,5);		
		$this->setViewState('tempTblPangkat',$tempTblPangkat);
		$sql="CREATE TABLE $tempTblPangkat (id INT (2) auto_increment, nama VARCHAR(30), gol VARCHAR(30), sk VARCHAR(30), pjbt VARCHAR(30),PRIMARY KEY (id)) ENGINE = MEMORY";
		
		$this->queryAction($sql,'C');//Create new tabel bro...							 
		$nama=$this->nmPangkat->Text;
		$gol=$this->gol->Text;
		$sk=$this->skTMT->Text;
		$pjbt=$this->nmPejabat->Text;
		
		$sql="INSERT INTO $tempTblPangkat (nama,gol,sk,pjbt) VALUES ('$nama','$gol','$sk','$pjbt')";
		$this->queryAction($sql,'C');//Insert new row in tabel bro...
		
		$sql="SELECT * FROM $tempTblPangkat ORDER BY id";
		$this->DGpangkat->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
		$this->DGpangkat->dataBind();						
		}
		else
		{
			$tempTblPangkat = $this->getViewState('tempTblPangkat');					 
			$nama=$this->nmPangkat->Text;
			$gol=$this->gol->Text;
			$sk=$this->skTMT->Text;
			$pjbt=$this->nmPejabat->Text;
			
			$sql="INSERT INTO $tempTblPangkat (nama,gol,sk,pjbt) VALUES ('$nama','$gol','$sk','$pjbt')";
			$this->queryAction($sql,'C');//Insert new row in tabel bro...
			
			$sql="SELECT * FROM $tempTblPangkat ORDER BY id";
			$this->DGpangkat->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->DGpangkat->dataBind();					
		}						
		
		$this->nmPangkat->focus();
   	} 
	
	public function tbhJabatanClick()
   	{   		
		if (!$this->getViewState('tempTblJabatan'))
		{
		$tempTblJabatan = 'temp_';
		$tempTblJabatan .= substr(microtime(),2,5);		
		$this->setViewState('tempTblJabatan',$tempTblJabatan);
		$sql="CREATE TABLE $tempTblJabatan (id INT (2) auto_increment, nama VARCHAR(30), tgl_mulai DATE, tgl_akhir DATE, sf VARCHAR(30), eselon VARCHAR(30),PRIMARY KEY (id)) ENGINE = MEMORY";
		
		$this->queryAction($sql,'C');//Create new tabel bro...							 
		$nama=$this->nmJabatan->Text;
		$tgl_mulai= $this->ConvertDate($this->DPtglAwalJbtn->Text,'2');
		$tgl_akhir=$this->ConvertDate($this->DPtglAkhirJbtn->Text,'2'); 
		$sf=$this->sf->Text;
		$eselon=$this->eselon->Text;
		
		$sql="INSERT INTO $tempTblJabatan (nama,tgl_mulai,tgl_akhir,sf,eselon) VALUES ('$nama','$tgl_mulai','$tgl_akhir','$sf','$eselon')";
		$this->queryAction($sql,'C');//Insert new row in tabel bro...
		
		$sql="SELECT * FROM $tempTblJabatan ORDER BY id";
		$this->DGjabatan->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
		$this->DGjabatan->dataBind();						
		}
		else
		{
			$tempTblJabatan = $this->getViewState('tempTblJabatan');					 
			$nama=$this->nmJabatan->Text;
			$tgl_mulai= $this->ConvertDate($this->DPtglAwalJbtn->Text,'2');
			$tgl_akhir=$this->ConvertDate($this->DPtglAkhirJbtn->Text,'2'); 
			$sf=$this->sf->Text;
			$eselon=$this->eselon->Text;
		
		$sql="INSERT INTO $tempTblJabatan (nama,tgl_mulai,tgl_akhir,sf,eselon) VALUES ('$nama','$tgl_mulai','$tgl_akhir','$sf','$eselon')";
		$this->queryAction($sql,'C');//Insert new row in tabel bro...
		
		$sql="SELECT * FROM $tempTblJabatan ORDER BY id";
		$this->DGjabatan->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
		$this->DGjabatan->dataBind();					
		}						
		
		$this->nmJabatan->focus();
   	} 
	
	public function tbhPendidikanClick()
   	{   		
		if (!$this->getViewState('tempTblPendidikan'))
		{
		$tempTblPendidikan = 'temp_';
		$tempTblPendidikan .= substr(microtime(),2,5);		
		$this->setViewState('tempTblPendidikan',$tempTblPendidikan);
		$sql="CREATE TABLE $tempTblPendidikan (id INT (2) auto_increment, nama VARCHAR(30), thn_lulus VARCHAR(30), jurusan VARCHAR(30), instansi VARCHAR(30), PRIMARY KEY (id)) ENGINE = MEMORY";
		
		$this->queryAction($sql,'C');//Create new tabel bro...							 
		$nama=$this->nmPendidikan->Text;
		$thn_lulus=$this->thnLulus->Text;
		$jurusan=$this->jur->Text;
		$instansi=$this->instanPenyelenggara->Text;
		
		$sql="INSERT INTO $tempTblPendidikan (nama,thn_lulus,jurusan,instansi) VALUES ('$nama','$thn_lulus','$jurusan','$instansi')";
		$this->queryAction($sql,'C');//Insert new row in tabel bro...
		
		$sql="SELECT * FROM $tempTblPendidikan ORDER BY id";
		$this->DGpendidikan->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
		$this->DGpendidikan->dataBind();						
		}
		else
		{
			$tempTblPendidikan = $this->getViewState('tempTblPendidikan');				 
			$nama=$this->nmPendidikan->Text;
			$thn_lulus=$this->thnLulus->Text;
			$jurusan=$this->jur->Text;
			$instansi=$this->instanPenyelenggara->Text;
		
		$sql="INSERT INTO $tempTblPendidikan (nama,thn_lulus,jurusan,instansi) VALUES ('$nama','$thn_lulus','$jurusan','$instansi')";
		$this->queryAction($sql,'C');//Insert new row in tabel bro...
		
		$sql="SELECT * FROM $tempTblPendidikan ORDER BY id";
		$this->DGpendidikan->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
		$this->DGpendidikan->dataBind();						
		}						
		
		$this->nmPendidikan->focus();
   	} 
	
	
	public function tbhPelatihanClick()
   	{   		
		if (!$this->getViewState('tempTblPelatihan'))
		{
		$tempTblPelatihan = 'temp_';
		$tempTblPelatihan .= substr(microtime(),2,5);		
		$this->setViewState('tempTblPelatihan',$tempTblPelatihan);
		$sql="CREATE TABLE $tempTblPelatihan (id INT (2) auto_increment, nama VARCHAR(30), thn VARCHAR(30), lama VARCHAR(30), instansi VARCHAR(30), PRIMARY KEY (id)) ENGINE = MEMORY";
		
		$this->queryAction($sql,'C');//Create new tabel bro...							 
		$nama=$this->nmPelatihan->Text;
		$thn=$this->thnPelatihan->Text;
		$lama=$this->lamaPelatihan->Text;
		$instansi=$this->penyelenggaraPelatihan->Text;
		
		$sql="INSERT INTO $tempTblPelatihan (nama,thn,lama,instansi) VALUES ('$nama','$thn','$lama','$instansi')";
		$this->queryAction($sql,'C');//Insert new row in tabel bro...
		
		$sql="SELECT * FROM $tempTblPelatihan ORDER BY id";
		$this->DGpelatihan->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
		$this->DGpelatihan->dataBind();						
		}
		else
		{
			$tempTblPelatihan = $this->getViewState('tempTblPelatihan');
			$nama=$this->nmPelatihan->Text;
			$thn=$this->thnPelatihan->Text;
			$lama=$this->lamaPelatihan->Text;
			$instansi=$this->penyelenggaraPelatihan->Text;
			
			$sql="INSERT INTO $tempTblPelatihan (nama,thn,lama,instansi) VALUES ('$nama','$thn','$lama','$instansi')";
			$this->queryAction($sql,'C');//Insert new row in tabel bro...
			
			$sql="SELECT * FROM $tempTblPelatihan ORDER BY id";
			$this->DGpelatihan->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->DGpelatihan->dataBind();					
		}	
		$this->nmPelatihan->focus();
   	} 
	
	
	public function tbhPelatihanTekClick()
   	{   		
		if (!$this->getViewState('tempTblPelatihanTek'))
		{
		$tempTblPelatihanTek = 'temp_';
		$tempTblPelatihanTek .= substr(microtime(),2,5);		
		$this->setViewState('tempTblPelatihanTek',$tempTblPelatihanTek);
		$sql="CREATE TABLE $tempTblPelatihanTek (id INT (2) auto_increment, nama VARCHAR(30), thn VARCHAR(30), lama VARCHAR(30), instansi VARCHAR(30), PRIMARY KEY (id)) ENGINE = MEMORY";
		
		$this->queryAction($sql,'C');//Create new tabel bro...							 
		$nama=$this->nmPelatihanTek->Text;
		$thn=$this->thnPelatihanTek->Text;
		$lama=$this->lamaPelatihanTek->Text;
		$instansi=$this->penyelenggaraPelatihanTek->Text;
		
		$sql="INSERT INTO $tempTblPelatihanTek (nama,thn,lama,instansi) VALUES ('$nama','$thn','$lama','$instansi')";
		$this->queryAction($sql,'C');//Insert new row in tabel bro...
		
		$sql="SELECT * FROM $tempTblPelatihanTek ORDER BY id";
		$this->DGpelatihanTek->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
		$this->DGpelatihanTek->dataBind();						
		}
		else
		{
			$tempTblPelatihanTek = $this->getViewState('tempTblPelatihanTek');
			$nama=$this->nmPelatihanTek->Text;
			$thn=$this->thnPelatihanTek->Text;
			$lama=$this->lamaPelatihanTek->Text;
			$instansi=$this->penyelenggaraPelatihanTek->Text;
			
			$sql="INSERT INTO $tempTblPelatihanTek (nama,thn,lama,instansi) VALUES ('$nama','$thn','$lama','$instansi')";
			$this->queryAction($sql,'C');//Insert new row in tabel bro...
			
			$sql="SELECT * FROM $tempTblPelatihanTek ORDER BY id";
			$this->DGpelatihanTek->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->DGpelatihanTek->dataBind();					
		}	
		$this->nmPelatihanTek->focus();
   	} 
	
	public function tbhPenghargaanClick()
   	{   		
		if (!$this->getViewState('tempTblPenghargaan'))
		{
		$tempTblPenghargaan = 'temp_';
		$tempTblPenghargaan .= substr(microtime(),2,5);		
		$this->setViewState('tempTblPenghargaan',$tempTblPenghargaan);
		$sql="CREATE TABLE $tempTblPenghargaan (id INT (2) auto_increment, nama VARCHAR(30), tgl DATE, no VARCHAR(30), instansi VARCHAR(30), PRIMARY KEY (id)) ENGINE = MEMORY";
		
		$this->queryAction($sql,'C');//Create new tabel bro...							 
		$nama=$this->nmPenghargaan->Text;
		$tgl=$this->ConvertDate($this->DPtglPenghargaan->Text,'2');
		$no=$this->noPenghargaan->Text;
		$instansi=$this->instanPenghargaan->Text;
		
		
		$sql="INSERT INTO $tempTblPenghargaan (nama,tgl,no,instansi) VALUES ('$nama','$tgl','$no','$instansi')";
		$this->queryAction($sql,'C');//Insert new row in tabel bro...
		
		$sql="SELECT * FROM $tempTblPenghargaan ORDER BY id";
		$this->DGpenghargaan->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
		$this->DGpenghargaan->dataBind();						
		}
		else
		{
			$tempTblPenghargaan = $this->getViewState('tempTblPenghargaan');
			$nama=$this->nmPenghargaan->Text;
			$tgl=$this->ConvertDate($this->DPtglPenghargaan->Text,'2');
			$no=$this->noPenghargaan->Text;
			$instansi=$this->instanPenghargaan->Text;
			
			
			$sql="INSERT INTO $tempTblPenghargaan (nama,tgl,no,instansi) VALUES ('$nama','$tgl','$no','$instansi')";
			$this->queryAction($sql,'C');//Insert new row in tabel bro...
			
			$sql="SELECT * FROM $tempTblPenghargaan ORDER BY id";
			$this->DGpenghargaan->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->DGpenghargaan->dataBind();					
		}	
		$this->nmPenghargaan->focus();
   	} 
	
	
	public function delAnak($sender,$param)
    {
        $item=$param->Item;
		$ID=$this->DGanak->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key
		$tempTblAnak = $this->getViewState('tempTblAnak');
		$sql = "DELETE FROM $tempTblAnak WHERE id='$ID'";
		$this->queryAction($sql,'C');								
				
		$sql="SELECT * FROM $tempTblAnak ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DGanak->DataSource=$arrData;
		$this->DGanak->dataBind();									
		$this->nmAnak->focus();
    }	
	
	public function delPangkat($sender,$param)
    {
        $item=$param->Item;
		$ID=$this->DGpangkat->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key
		$tempTblPangkat = $this->getViewState('tempTblPangkat');
		$sql = "DELETE FROM $tempTblPangkat WHERE id='$ID'";
		$this->queryAction($sql,'C');								
				
		$sql="SELECT * FROM $tempTblPangkat ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DGpangkat->DataSource=$arrData;
		$this->DGpangkat->dataBind();									
		$this->nmPangkat->focus();
    }	
	
	public function delJabatan($sender,$param)
    {
        $item=$param->Item;
		$ID=$this->DGjabatan->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key
		$tempTblJabatan = $this->getViewState('tempTblJabatan');
		$sql = "DELETE FROM $tempTblJabatan WHERE id='$ID'";
		$this->queryAction($sql,'C');								
				
		$sql="SELECT * FROM $tempTblJabatan ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DGjabatan->DataSource=$arrData;
		$this->DGjabatan->dataBind();									
		$this->nmJabatan->focus();
    }	
	
	public function delPendidikan($sender,$param)
    {
        $item=$param->Item;
		$ID=$this->DGpendidikan->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key
		$tempTblPendidikan = $this->getViewState('tempTblPendidikan');
		$sql = "DELETE FROM $tempTblPendidikan WHERE id='$ID'";
		$this->queryAction($sql,'C');								
				
		$sql="SELECT * FROM $tempTblPendidikan ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DGpendidikan->DataSource=$arrData;
		$this->DGpendidikan->dataBind();									
		$this->nmPendidikan->focus();
    }	
	
	public function delPelatihan($sender,$param)
    {
        $item=$param->Item;
		$ID=$this->DGpelatihan->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key
		$tempTblPelatihan = $this->getViewState('tempTblPelatihan');
		$sql = "DELETE FROM $tempTblPelatihan WHERE id='$ID'";
		$this->queryAction($sql,'C');								
				
		$sql="SELECT * FROM $tempTblPelatihan ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DGpelatihan->DataSource=$arrData;
		$this->DGpelatihan->dataBind();									
		$this->nmPelatihan->focus();
    }	
	
	public function delPelatihanTek($sender,$param)
    {
        $item=$param->Item;
		$ID=$this->DGpelatihanTek->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key
		$tempTblPelatihanTek = $this->getViewState('tempTblPelatihanTek');
		$sql = "DELETE FROM $tempTblPelatihanTek WHERE id='$ID'";
		$this->queryAction($sql,'C');								
				
		$sql="SELECT * FROM $tempTblPelatihanTek ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DGpelatihanTek->DataSource=$arrData;
		$this->DGpelatihanTek->dataBind();									
		$this->nmPelatihanTek->focus();
    }	
	
	public function delPenghargaan($sender,$param)
    {
        $item=$param->Item;
		$ID=$this->DGpenghargaan->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key
		$tempTblPenghargaan = $this->getViewState('tempTblPenghargaan');
		$sql = "DELETE FROM $tempTblPenghargaan WHERE id='$ID'";
		$this->queryAction($sql,'C');								
				
		$sql="SELECT * FROM $tempTblPenghargaan ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DGpenghargaan->DataSource=$arrData;
		$this->DGpenghargaan->dataBind();									
		$this->nmPenghargaan->focus();
    }	
	
	protected function statKwnPegChanged()
	{		
		if(TPropertyValue::ensureString($this->collectSelectionListResult($this->statKwnPeg))=="Belum Kawin")
		{
			$this->plihPasangan->Enabled=false;
			$this->nmSuamiIstriPeg->Enabled=false;
			$this->tmpLhrSuamiIstriPeg->Enabled=false;
			$this->DPtglLhrSuamiIstriPeg->Enabled=false;
			$this->DPtglKawin->Enabled=false;
			$this->noSrKrtu->Enabled=false;
			
			$this->nmAnak->Enabled=false;
			$this->DPtglLhrAnak->Enabled=false;
			$this->jkAnak->Enabled=false;
			$this->tbhAnak->Enabled=false;
		}		
		else
		{
			{
			$this->plihPasangan->Enabled=true;
			$this->nmSuamiIstriPeg->Enabled=true;
			$this->tmpLhrSuamiIstriPeg->Enabled=true;
			$this->DPtglLhrSuamiIstriPeg->Enabled=true;
			$this->DPtglKawin->Enabled=true	;
			$this->noSrKrtu->Enabled=true;
			
			$this->nmAnak->Enabled=true;
			$this->DPtglLhrAnak->Enabled=true;
			$this->jkAnak->Enabled=true;
			$this->tbhAnak->Enabled=true;
		}		
		}
	}	
	
	
	protected function collectSelectionListResult($input)
		{
			$indices=$input->SelectedIndices;
			//$result='';
			foreach($indices as $index)
			{
				$item=$input->Items[$index];
				$result ="$item->Text";
			}
			return $output->Text=$result;
		}
	
	public function simpanClicked($sender,$param)
	{	
		$tglLhrPeg = $this->DPtglLhrPeg->Text;
		$mysqltglLhrPeg = $this->ConvertDate($tglLhrPeg,'2');
		
		$tglLhrSuamiIstriPeg = $this->DPtglLhrSuamiIstriPeg->Text;
		$mysqltglLhrSuamiIstriPeg = $this->ConvertDate($tglLhrSuamiIstriPeg,'2');		
		
		$tglKawin = $this->DPtglKawin->Text;
		$mysqltglKawin = $this->ConvertDate($mysqltglKawin,'2');
		
		$tglTmtPeg = $this->DPtglTmtPeg->Text;
		$mysqltglTmtPeg = $this->ConvertDate($tglTmtPeg,'2');
		
		$tglTmt = $this->DPtglTmt->Text;
		$mysqltglTmt = $this->ConvertDate($tglTmt,'2');
		
		$tglTgs = $this->DPtglTgs->Text;
		$mysqltglTgs = $this->ConvertDate($tglTgs,'2');
		
		$NimTmp=$this->nip1->Text.$this->nip2->Text.$this->nip3->Text;
		
		if($this->IsValid)  // when all validations succeed
        {
            // populates a UserRecord object with user inputs
            $SimpegRecord=new SimpegRecord;
			$SimpegRecord->nip=$NimTmp;
			$SimpegRecord->nama=$this->nmPeg->Text;
			$SimpegRecord->jns_kelamin=TPropertyValue::ensureString($this->collectSelectionListResult($this->jkPeg));
			$SimpegRecord->tmp_lahir=$this->tmpLhrPeg->Text;
			$SimpegRecord->tgl_lahir=$mysqltglLhrPeg;
			$SimpegRecord->agama=TPropertyValue::ensureString($this->collectSelectionListResult($this->agamaPeg));
			$SimpegRecord->status_kawin=TPropertyValue::ensureString($this->collectSelectionListResult($this->statKwnPeg));
			$SimpegRecord->jns_pasangan=TPropertyValue::ensureString($this->collectSelectionListResult($this->plihPasangan));
			$SimpegRecord->nm_pasangan=$this->nmSuamiIstriPeg->Text;
			$SimpegRecord->tmp_lahir_pasangan=$this->tmpLhrSuamiIstriPeg->Text;
			$SimpegRecord->tgl_lahir_pasangan=$mysqltglLhrSuamiIstriPeg;
			$SimpegRecord->tgl_kawin=$mysqltglKawin;
			$SimpegRecord->no_kartu_kawin=$this->noSrKrtu->Text;
			$SimpegRecord->tmt_jd_peg=$mysqltglTmtPeg;
			$SimpegRecord->pendidikan=$this->pendidikan->Text;
			$SimpegRecord->status_kepeg=$this->statKepeg->Text;
			$SimpegRecord->jns_kepeg=$this->jnsKepeg->Text;
			$SimpegRecord->no_karpeg=$this->noSrKarpeg->Text;
			$SimpegRecord->gol_akhir=$this->golTerakhir->Text;
			$SimpegRecord->tmt=$mysqltglTmt;
			$SimpegRecord->no_sk_penempatan=$this->noSKpenempatan->Text;
			$SimpegRecord->tgl_tgs_unit=$mysqltglTgs;
			$SimpegRecord->nm_unit=$this->nmUnitKerja->Text;
			$SimpegRecord->nm_satgas=$this->nmSatgas->Text;
			$SimpegRecord->kab_kota=$this->collectSelectionListResult($this->DDKabKota);
			$SimpegRecord->prop=$this->collectSelectionListResult($this->DDprop);           
		
			// saves to the database via Active Record mechanism
            $SimpegRecord->save(); 		
			
			//Simpan DataGrid Anak 
			if($this->getViewState('tempTblAnak'))
			{
				$tempTblAnak = $this->getViewState('tempTblAnak');
				$sql="SELECT * FROM $tempTblAnak ORDER BY id";
				$arrData=$this->queryAction($sql,'R');		
				
				foreach($arrData as $row) 
				{ 
					$newRec=new SimpegAnakRecord();
					$newRec->nip=$NipTmp;				
					$newRec->nm_anak=$row['nama'];			
					$newRec->tgl_lahir_anak=$row['tgl_lahir'];	
					$newRec->jns_kel_anak=$row['jns_kelamin'];					
					$newRec->save();
				}
				$this->queryAction($this->getViewState('tempTblAnak'),'D');//Droped the table
				$this->clearViewState('tempTblAnak');//Clear the view state
			}	
				
			//Simpan DataGrid Pangkat 
			if($this->getViewState('tempTblPangkat'))
			{
				$tempTblPangkat = $this->getViewState('tempTblPangkat');
				$sql="SELECT * FROM $tempTblPangkat ORDER BY id";
				$arrData=$this->queryAction($sql,'R');		
				
				foreach($arrData as $row) 
				{ 
					$newRec=new SimpegPangkatRecord();
					$newRec->nip=$NipTmp;				
					$newRec->nm_pangkat=$row['nama'];			
					$newRec->gol_pangkat=$row['gol'];	
					$newRec->sk_tmt_pangkat=$row['sk'];
					$newRec->nm_pejabat=$row['pjbt'];					
					$newRec->save();
				}
				$this->queryAction($this->getViewState('tempTblPangkat'),'D');//Droped the table
				$this->clearViewState('tempTblPangkat');//Clear the view state
			}	
			
			//Simpan DataGrid Jabatan
			if($this->getViewState('tempTblJabatan'))
			{
				$tempTblJabatan = $this->getViewState('tempTblJabatan');
				$sql="SELECT * FROM $tempTblJabatan ORDER BY id";
				$arrData=$this->queryAction($sql,'R');		
				
				foreach($arrData as $row) 
				{ 
					$newRec=new SimpegJabatanRecord();
					$newRec->nip=$NipTmp;				
					$newRec->nm_jabatan=$row['nama'];			
					$newRec->tgl_mulai=$row['tgl_mulai'];	
					$newRec->tgl_akhir=$row['tgl_akhir'];
					$newRec->sf=$row['sf'];
					$newRec->eselon=$row['eselon'];					
					$newRec->save();
				}
				$this->queryAction($this->getViewState('tempTblJabatan'),'D');//Droped the table
				$this->clearViewState('tempTblJabatan');//Clear the view state
			}	
			
			
			//Simpan DataGrid Pendidikan
			if($this->getViewState('tempTblPendidikan'))
			{
				$tempTblPendidikan = $this->getViewState('tempTblPendidikan');
				$sql="SELECT * FROM $tempTblPendidikan ORDER BY id";
				$arrData=$this->queryAction($sql,'R');		
				
				foreach($arrData as $row) 
				{ 
					$newRec=new SimpegPendidikanRecord();
					$newRec->nip=$NipTmp;				
					$newRec->nm_pendidikan=$row['nama'];			
					$newRec->thn_lulus=$row['thn_lulus'];	
					$newRec->jur_pendidikan=$row['jurusan'];
					$newRec->instansi_pendidikan=$row['instansi'];								
					$newRec->save();
				}
				$this->queryAction($this->getViewState('tempTblPendidikan'),'D');//Droped the table
				$this->clearViewState('tempTblPendidikan');//Clear the view state
			}	
			
			
			//Simpan DataGrid Pelatihan Penjenjangan
			if($this->getViewState('tempTblPelatihan'))
			{
				$tempTblPelatihan = $this->getViewState('tempTblPelatihan');
				$sql="SELECT * FROM $tempTblPelatihan ORDER BY id";
				$arrData=$this->queryAction($sql,'R');		
				
				foreach($arrData as $row) 
				{ 
					$newRec=new SimpegLatihJenjangRecord();
					$newRec->nip=$NipTmp;				
					$newRec->nm_pelatihan=$row['nama'];			
					$newRec->thn_pelatihan=$row['thn'];	
					$newRec->lama_pelatihan=$row['lama'];
					$newRec->penyelenggara=$row['instansi'];								
					$newRec->save();
				}
				$this->queryAction($this->getViewState('tempTblPelatihan'),'D');//Droped the table
				$this->clearViewState('tempTblPelatihan');//Clear the view state
			}	
			
			
			//Simpan DataGrid Pelatihan Teknis
			if($this->getViewState('tempTblPelatihanTek'))
			{
				$tempTblPelatihanTek = $this->getViewState('tempTblPelatihanTek');
				$sql="SELECT * FROM $tempTblPelatihanTek ORDER BY id";
				$arrData=$this->queryAction($sql,'R');		
				
				foreach($arrData as $row) 
				{ 
					$newRec=new SimpegLatihTeknisRecord();
					$newRec->nip=$NipTmp;				
					$newRec->nm_pelatihan=$row['nama'];			
					$newRec->thn_pelatihan=$row['thn'];	
					$newRec->lama_pelatihan=$row['lama'];
					$newRec->penyelenggara=$row['instansi'];								
					$newRec->save();
				}
				$this->queryAction($this->getViewState('tempTblPelatihanTek'),'D');//Droped the table
				$this->clearViewState('tempTblPelatihanTek');//Clear the view state
			}	
			
			//Simpan DataGrid Penghargaan
			if($this->getViewState('tempTblPenghargaan'))
			{
				$tempTblPenghargaan = $this->getViewState('tempTblPenghargaan');
				$sql="SELECT * FROM $tempTblPenghargaan ORDER BY id";
				$arrData=$this->queryAction($sql,'R');		
				
				foreach($arrData as $row) 
				{ 
					$newRec=new SimpegPenghargaanRecord();
					$newRec->nip=$NipTmp;				
					$newRec->nm_penghargaan=$row['nama'];			
					$newRec->tgl_penghargaan=$row['tgl'];	
					$newRec->no_penghargaan=$row['no'];
					$newRec->instansi=$row['instansi'];								
					$newRec->save();
				}
				$this->queryAction($this->getViewState('tempTblPenghargaan'),'D');//Droped the table
				$this->clearViewState('tempTblPenghargaan');//Clear the view state
			}	
				
					
				
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Simpeg.mainSimpeg'));
        }			
	}
	
    public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simpeg.menuSimpeg'));		
	}
}
?>
