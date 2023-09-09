<?php
/**
 * DaftarBaru class file
 *
 * @author Anton <anton@intergo.co.id>
 * @link http://www.intergo.co.id/
 * @copyright Copyright &copy; 2008 Intergo Telematics
 * @license http://www.intergo.co.id/license/
 * @version $Id: DaftarBaru.php 1001 2008-02-01 09:11:23Z xue $
 */

/**
 * DaftarBaru class
 *
 * @author Anton <anton@intergo.co.id>
 * @link http://www.intergo.co.id/
 * @copyright Copyright &copy; 2008 Intergo Telematics
 * @license http://www.intergo.co.id/license/
 */
class DaftarBaru extends SimakConf
{
 /*
 	Autentifikasi Priviledge user!
 */
 public function onInit($param)
 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
 }	
 
 public function onLoad($param)
	{
		parent::onLoad($param);		
		$kab = $this->getViewState('idKab');		
			
		if(!$this->IsPostBack){	
			$sql = "SELECT cm FROM tbd_pasien order by cm desc";
			$no = PasienRecord::finder()->findBySql($sql);
			
			if($no==NULL)//jika kosong bikin ndiri
			{				
				//$urut='010000';
				$urut='000001';
			}else
			{
				/*
				$urut=intval(substr($no->getColumnValue('cm'),-6,6));
				$no1=intval(substr($no->getColumnValue('cm'),0,2));
				$no2=intval(substr($no->getColumnValue('cm'),2,2));
				$no3=intval(substr($no->getColumnValue('cm'),5,2));
				*/
				if ($urut==999999)
				{
					$urut='000001';					
				}else
				{/*
					if ($no1==99)
					{
						$urut1='99';
					}else
					{
						$urut1=$no1+1;
						$urut1=substr('00',0,0-strlen($urut1)).$urut1;	
					}
					
					if ($no2==99)
					{
						$urut2='99';
					}elseif (($no1==98)  OR ($no1<>99))
					{
						$urut2='00';
					}else
					{
						$urut2=$no2+1;
						$urut2=substr('00',0,0-strlen($urut2)).$urut2;	
					}
					
					if ($no3==99)
					{
						$urut3='99';
					}elseif (($no2==98) OR ($no2<>99)) 
					{
						$urut3='00';
					}else
					{
						$urut3=$no3+1;
						$urut3=substr('00',0,0-strlen($urut3)).$urut3;	
					}
					
					$urut=$urut1.$urut2.$urut3;*/
					$urut=intval(substr($no->getColumnValue('cm'),-6,6));
					$urut=$urut+1;
					$urut=substr('000000',-6,6-strlen($urut)).$urut;					
				}
			}
			$this->cm->Text=$urut;		
			$this->setViewState('cm',$urut);
			$this->nama->focus();
			if($this->Request['cm'] && $this->Request['mode']=='09' )
			{
				$this->cm->Text=$this->Request['cm'];
				$mode=$this->Request['mode'];
				$this->setViewState('mode',$mode);
				$this->nama->focus();	
				//$this->nama->Text=$mode;
			}
			else
			{		
				$this->cm->focus();	
			}
			
			$this->tmp_lahir->Text="Kota Tangerang Selatan";
			$criteria = new TActiveRecordCriteria;
			$criteria->OrdersBy['nama'] = 'asc';
			
			$this->DDKerja->DataSource=PekerjaanRecord::finder()->findAll($criteria);
			$this->DDKerja->dataBind(); 
			$this->DDPdk->DataSource=PendidikanRecord::finder()->findAll($criteria);
			$this->DDPdk->dataBind(); 			
			$this->DDKelompok->DataSource=KelompokRecord::finder()->findAll($criteria);
			$this->DDKelompok->dataBind();
			$this->DDKelompok->SelectedValue='01';
			//$this->DDKontrak->DataSource=PerusahaanRecord::finder()->findAll($criteria);
			//$this->DDKontrak->dataBind();
			$this->DDKab->DataSource=KabupatenRecord::finder()->findAll($criteria);
			$this->DDKab->dataBind();
			$this->DDKab->SelectedValue='01';
			/*
			$this->DDKec->DataSource=KecamatanRecord::finder()->findAll($criteria);
			$this->DDKec->dataBind(); 
			$this->DDKel->DataSource=KelurahanRecord::finder()->findAll($criteria);
			$this->DDKel->dataBind();
			*/
			$this->DDAgama->DataSource=AgamaRecord::finder()->findAll($criteria);
			$this->DDAgama->dataBind();
			$this->DDAgama->SelectedValue='1';
			/*
			$this->DDKontrak->Enabled=false;
			$this->DDKec->Enabled=false;	
			$this->KecLuar->Enabled=false;
			$this->DDKel->Enabled=false;	
			$this->KelurahanLuar->Enabled=false;
			*/
		}
					
	}
	
	public function selectionChangedKelompok($sender,$param)
	{
		if($this->DDKelompok->SelectedValue == '05')
		{
			$this->DDKontrak->Enabled=true;
			$this->DDKontrak->focus();
		}
		else
		{
			$this->DDKontrak->Enabled=false;
			$this->umur->focus();
		}
		
	}	
	
	public function selectionChangedKab($sender,$param)
	{		
		$kab = $this->DDKab->SelectedValue;	
		$this->setViewState('idKab',$kab,'');	
		if ($kab == '01'){ //Bila kota bandung				
			//$this->KecLuar->Enabled=false;
			//$this->KelurahanLuar->Enabled=false;

			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = 'id_kab = :idkab';
			$criteria->Parameters[':idkab'] = $kab;
			$criteria->OrdersBy['nama'] = 'asc';

			$this->DDKec->DataSource=KecamatanRecord::finder()->findAll($criteria);
			$this->DDKec->dataBind(); 
			
			$this->DDKec->Enabled=true;
			$this->DDKec->focus();				
			
		}elseif ($kab=='02'){
			//$this->KecLuar->Enabled=false;
			//$this->KelurahanLuar->Enabled=false;

			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = 'id_kab = :idkab';
			$criteria->Parameters[':idkab'] = $kab;
			$criteria->OrdersBy['nama'] = 'asc';

			$this->DDKec->DataSource=KecamatanRecord::finder()->findAll($criteria);
			$this->DDKec->dataBind(); 
			
			$this->DDKec->Enabled=true;
			$this->DDKec->focus();
		}elseif($kab==''){
			$criteria = new TActiveRecordCriteria;
			$criteria->OrdersBy['nama'] = 'asc';
			$this->DDKec->SelectedIndex=-1;
			$this->DDKel->SelectedIndex=-1;
			$this->DDKec->DataSource=KecamatanRecord::finder()->findAll($criteria);
			$this->DDKec->dataBind(); 
			$this->DDKel->DataSource=KelurahanRecord::finder()->findAll($criteria);
			$this->DDKel->dataBind();  
			//$this->DDKec->Text ="--Silakan Pilih--";
			//$this->DDKel->Text ="--Silakan Pilih--";
			
			$this->DDKec->Enabled=false;				
			$this->DDKel->Enabled=false;				
		}
		else{
			$this->KecLuar->Enabled=true;
			$this->KelurahanLuar->Enabled=true;
			$this->KecLuar->focus();
			$this->DDKec->Enabled=false;
			$this->DDKel->Enabled=false;
		}	
	} 
	
	public function selectionChangedKec($sender,$param)
	{		
		$kec = $this->DDKec->SelectedValue;	
		$this->setViewState('idKec',$kec,'');
		
		$kec = $this->DDKec->SelectedValue;
		$kab = $this->getViewState('idKab');
		
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = 'id_kab = :idkab AND id_kec = :idkec';
		$criteria->Parameters[':idkab'] = $kab;
		$criteria->Parameters[':idkec'] = $kec;
		$criteria->OrdersBy['nama'] = 'asc';

		$this->DDKel->DataSource=KelurahanRecord::finder()->findAll($criteria);
		$this->DDKel->dataBind(); 	
		$this->DDKel->Enabled=true;
		$this->DDKel->focus();			
	}
	
	public function selectionChangedKel($sender,$param)
	{		
		$this->suku->focus();			
	}		
	
	protected function collectSelectionListResult($input)
	{
		$indices=$input->SelectedIndices;		
		foreach($indices as $index)
		{
			$item=$input->Items[$index];
			return $index;
		}		
	}	
	
	public function checkRM($sender,$param)
    {/*
		//$param->IsValid=PasienRecord::finder()->findByPk($this->cm->Text)===null;		
		$checkRM=$this->cm->Text;
		$this->setViewState('cm',$checkRM);
		$sql="SELECT * FROM du_pasien WHERE cm='$checkRM'";		
		$data=$this->queryAction($sql,'S');		
		$dummy=PasienRecord::finder()->findByPk($this->cm->Text);
		//$id=DUPasienRecord::finder()->finderByPK($checkRM);
		if($data === null && $dummy === null)
		{			
			$this->errMsg->text='';					
			$this->nama->focus();			
		}
		else if($dummy===null)
		{					
			if($data)
			{			
				$this->errMsg->text="Harap diedit data lama telah ditemukan!";
				$cm=$this->getViewState('cm');
				foreach($data as $row)
				{
					$nama= $row['nama'];
					$tmp_lahir= $row['tmt_lahir'];
					$tgl_lahir= $row['tgl_lahir'];
					$tgl_lahir = $this->ConvertDate($tgl_lahir,'1');
					$alamat=$row['alamat'];
					$suku=$row['suku'];
					$status=$row['status_kawin'] - 1;
					$jkel=$row['jkel'] - 1;
					$agama=$row['agama'];
					$rt=$row['rt'];
					$rw=$row['rw'];
					$catatan=$row['note'];
				}
				$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarEdit',array('cm'=>$cm,'nama'=>$nama,'tmp_lahir'=>$tmp_lahir,'tgl_lahir'=>$tgl_lahir,'alamat'=>$alamat,'suku'=>$suku,'rt'=>$rt,'rw'=>$rw,'catatan'=>$catatan,'jkel'=>$jkel,'agama'=>$agama,'status'=>$status,'mode'=>'07')));
			}else{
				$this->errMsg->text='';					
				$this->nama->focus();					
					}	
			
		}else if ($dummy){
			$this->errMsg->Text='No. CM tersebut sudah ada!';
			$cm=$this->getViewState('cm');
			foreach($dummy as $row)
				{
					$nama= $row['nama'];
					$tmp_lahir= $row['tmt_lahir'];
					$tgl_lahir= $row['tgl_lahir'];
					$tgl_lahir = $this->ConvertDate($tgl_lahir,'1');
					$alamat=$row['alamat'];
					$suku=$row['suku'];
					$status=$row['status_kawin'] - 1;
					$jkel=$row['jkel'] - 1;
					$agama=$row['agama'];
					$rt=$row['rt'];
					$rw=$row['rw'];
					$catatan=$row['note'];
				}
				$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarEdit',array('cm'=>$cm,'nama'=>$nama,'tmp_lahir'=>$tmp_lahir,'tgl_lahir'=>$tgl_lahir,'alamat'=>$alamat,'suku'=>$suku,'rt'=>$rt,'rw'=>$rw,'catatan'=>$catatan,'jkel'=>$jkel,'agama'=>$agama,'status'=>$status)));
		}*/
		
    }
	
	public function checkUmur($sender,$param)
	{
		$temp=$this->tgl_lahir->text;
		if ($temp <> NULL)
		{
			$this->umur->text='';
			$thn=date('Y')-intval(substr($temp,strlen($temp)-4,4));
			$bln=date('m')-intval(substr($temp,3,2));
			$hari=date('d')-intval(substr($temp,0,2));	
			if ($hari<0){
				$bln=$bln-1;
				$hari=date('t')+$hari;
			}
			$this->umur->text=$thn;
			$this->bln->text=$bln;	
			$this->hari->text=$hari;	
			$this->DDKelompok->focus();
		}elseif($this->umur->text <> NULL)
			{
				$this->tgl_lahir->text='';
				//$tahun = date('Y') - $this->umur->Text . '-01-01';
				$tahun = date('Y') - $this->umur->Text;
				$bulan = date('m') - $this->bln->Text;
				$hari = date('d') - $this->hari->Text;
				if (strlen($bulan)==1){
					$bulan='0'.$bulan;
				}
				if (strlen($hari)==1){
					$hari='0'.$hari;
				}
				//$thn = $this->ConvertDate($tahun,'1');
				$this->tgl_lahir->text=$hari.'-'.$bulan.'-'.$tahun;
				//$this->tgl_lahir->text=$bulan;
				$this->test->text=$hari.'-'.$bulan.'-'.$tahun;
				$this->DDAgama->focus();
			}
		} 
		
	public function checkNM($sender,$param)
	{
		/*
		$nama=ucwords($this->nama->Text);				
		$nama .= '%';
		$chNama=PasienRecord::finder()->find('nama LIKE ?', $nama);
		if($chNama)
		{
			$this->cm->Text=$chNama->cm;
			$this->nama->Text=$chNama->nama;
			$this->tmp_lahir->Text=$chNama->tmp_lahir;
			$this->tgl_lahir->Text=$this->ConvertDate($chNama->tgl_lahir,'1');
			$this->DDKelompok->SelectedValue=$chNama->kelompok;
			if($chNama->perusahaan)
				$this->DDKontrak->SelectedValue=$chNama->perusahaan;
			$this->DDAgama->SelectedValue=$chNama->agama;
			$this->jkel->SelectedIndex=$chNama->jkel;
			$this->alamat->Text=$chNama->alamat;
			$this->alamat->Text=$chNama->alamat;
			$this->rt->Text=$chNama->rt;
			$this->rw->Text=$chNama->rw;
			$this->DDKab->SelectedValue=$chNama->kabupaten;
			$this->DDKec->SelectedValue=$chNama->kecamatan;
			$this->DDKel->SelectedValue=$chNama->kelurahan;
			$this->suku->Text=$chNama->suku;
			$this->status->SelectedIndex=$chNama->status;
			$this->wni->Text=$chNama->wni;
			$this->DDKerja->SelectedValue=$chNama->pekerjaan;
			$this->DDPdk->SelectedValue=$chNama->pendidikan;
			$this->catatan->Text=$chNama->catatan;		
		}
	*/
	}

	public function checkAll($sender,$param)
	{/*
		$valIgnore=$this->ignore->Value;
		//$checkRM=$this->cm->Text;
		//$this->setViewState('cm',$checkRM);		
		$checkNM=$this->nama->text;
		$checkAL=$this->alamat->text;
		$checkTgl=$this->tgl_lahir->text;
		$date=$this->ConvertDate($checkTgl,'2');
		$checkUmr=$this->umur->text;
		$this->setViewState('nm',$checkNM);
		$this->setViewState('alm',$checkAL);		
		$this->setViewState('tgl',$date);
		$this->setViewState('umr',$checkUmr);
		if($valIgnore == "1")
		{
			if ($checkTgl==''){			
			$sql="SELECT * FROM du_pasien WHERE nama = '$checkNM' AND alamat like '%$checkAL%' AND umur='$checkUmr'";
			}elseif ($checkUmr==''){
			$sql="SELECT * FROM du_pasien WHERE nama = '$checkNM' AND alamat like '%$checkAL%' AND tgl_lahir='$date'";	
			}else{
			$sql="SELECT * FROM du_pasien WHERE nama = '$checkNM' AND alamat like '%$checkAL%' AND umur='$checkUmr' AND tgl_lahir='$date'";
			}
		}else{
			if ($checkTgl==''){			
			$sql="SELECT * FROM du_pasien WHERE nama like '$checkNM%' AND alamat like '%$checkAL%' AND umur='$checkUmr'";
			}elseif ($checkUmr==''){
			$sql="SELECT * FROM du_pasien WHERE nama like '$checkNM%' AND alamat like '%$checkAL%' AND tgl_lahir='$date'";	
			}else{
			$sql="SELECT * FROM du_pasien WHERE nama like '$checkNM%' AND alamat like '%$checkAL%' AND umur='$checkUmr' AND tgl_lahir='$date'";
			}
		}
		
		$data=$this->queryAction($sql,'S');
		foreach($data as $row)
		{
			$cek= $row['cm'];
		}
		$sql="SELECT * FROM tbd_pasien WHERE nama like '$checkNM%' AND alamat like '%$checkAL%'";		
		$dummy=$this->queryAction($sql,'S');
		foreach($dummy as $row)
		{
			$cek= $row['cm'];
		}
		if ($cek){
			$this->cm->text=$cek;
			$checkRM=$this->cm->Text;
		}else{
			$checkRM=$this->cm->Text;
			$this->setViewState('cm',$checkRM);
		}
		
		$this->setViewState('cm',$checkRM);
		$sql="SELECT * FROM du_pasien WHERE cm='$cek'";		
		$data=$this->queryAction($sql,'S');
		$dummy=PasienRecord::finder()->findByPk($cek);
		//$id=DUPasienRecord::finder()->finderByPK($checkRM);
		if($data === null && $dummy === null)
		{			
			$this->errMsg->text='';					
			$this->rt->focus();			
		}
		else if($dummy===null)
		{					
			if($data)
			{			
				$this->errMsg->text="Harap diedit data lama telah ditemukan!";
				$cm=$this->getViewState('cm');
				//$cm=$cek;
				foreach($data as $row)
				{
					$nama= $row['nama'];
					$tmp_lahir= $row['tmt_lahir'];
					$tgl_lahir= $row['tgl_lahir'];
					$tgl_lahir = $this->ConvertDate($tgl_lahir,'1');
					$alamat=$row['alamat'];
					$suku=$row['suku'];
					$status=$row['status_kawin'] - 1;
					$jkel=$row['jkel'] - 1;
					$agama=$row['agama'];
					$rt=$row['rt'];
					$rw=$row['rw'];
					$catatan=$row['note'];
				}
				$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarEdit',array('cm'=>$cm,'nama'=>$nama,'tmp_lahir'=>$tmp_lahir,'tgl_lahir'=>$tgl_lahir,'alamat'=>$alamat,'suku'=>$suku,'rt'=>$rt,'rw'=>$rw,'catatan'=>$catatan,'jkel'=>$jkel,'agama'=>$agama,'status'=>$status,'mode'=>'07')));
			}else{
				$this->errMsg->text='';					
				$this->rt->focus();					
				//$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarEdit',array('cm'=>$cm,'nama'=>$nama,'tmp_lahir'=>$tmp_lahir,'tgl_lahir'=>$tgl_lahir,'alamat'=>$alamat,'suku'=>$suku,'rt'=>$rt,'rw'=>$rw,'catatan'=>$catatan,'jkel'=>$jkel,'agama'=>$agama,'status'=>$status)));
			}	
			
		}else if ($dummy){
			$this->errMsg->Text='No. CM tersebut sudah ada!';
			$cm=$this->getViewState('cm');
			foreach($dummy as $row)
				{
					$nama= $row['nama'];
					$tmp_lahir= $row['tmt_lahir'];
					$tgl_lahir= $row['tgl_lahir'];
					$tgl_lahir = $this->ConvertDate($tgl_lahir,'1');
					$alamat=$row['alamat'];
					$suku=$row['suku'];
					$status=$row['status_kawin'] - 1;
					$jkel=$row['jkel'] - 1;
					$agama=$row['agama'];
					$rt=$row['rt'];
					$rw=$row['rw'];
					$catatan=$row['note'];
				}
				$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarEdit',array('cm'=>$cm,'nama'=>$nama,'tmp_lahir'=>$tmp_lahir,'tgl_lahir'=>$tgl_lahir,'alamat'=>$alamat,'suku'=>$suku,'rt'=>$rt,'rw'=>$rw,'catatan'=>$catatan,'jkel'=>$jkel,'agama'=>$agama,'status'=>$status)));
		}*/
	}
	
	public function simpanClicked($sender,$param)
	{			
		if($this->tgl_lahir->Text){
			$dateTmp = $this->tgl_lahir->Text;
			$mysqlDate = $this->ConvertDate($dateTmp,'2');
		}else if($this->umur->Text){
			$mysqlDate = date('Y') - $this->umur->Text . '-01-01';
		}	
		if($this->IsValid)  // when all validations succeed
        {      
			$nama=$this->nama->Text . ', ' . $this->embel->SelectedValue;
			// populates a UserRecord object with user inputs
            $pasienRecord=new PasienRecord;
            $pasienRecord->cm=$this->cm->Text;
            $pasienRecord->nama=ucwords($nama);
			$pasienRecord->tmp_lahir=ucwords($this->tmp_lahir->Text);
            $pasienRecord->tgl_lahir=(string)$mysqlDate;		
  			$pasienRecord->kelompok=(string)$this->DDKelompok->SelectedValue;
            //$pasienRecord->perusahaan=(string)$this->DDKontrak->Value;
						$pasienRecord->perusahaan='00';				
			$this->collectSelectionListResult($this->status);
 			$pasienRecord->status=(string)$this->collectSelectionListResult($this->status);
            $pasienRecord->agama=(string)$this->DDAgama->SelectedValue;						
 			$pasienRecord->jkel=(string)$this->collectSelectionListResult($this->jkel);
            $pasienRecord->alamat=ucwords($this->alamat->Text);
 			$pasienRecord->rt=$this->rt->Text;
            $pasienRecord->rw=$this->rw->Text;
			$pasienRecord->kabupaten=(string)$this->DDKab->SelectedValue;
            $pasienRecord->kecamatan=(string)$this->DDKec->SelectedValue;
			$pasienRecord->kelurahan=(string)$this->DDKel->SelectedValue;
            $pasienRecord->suku=ucwords($this->suku->Text);
            $pasienRecord->umur=$this->umur->Text;
            $pasienRecord->wni=$this->wni->Text;
			$pasienRecord->pekerjaan=(string)$this->DDKerja->SelectedValue;
			$pasienRecord->pendidikan=(string)$this->DDPdk->SelectedValue;
			$pasienRecord->catatan=ucwords($this->catatan->Text);            
			$kab = $this->getViewState('idKab');
			if ( $kab != '01'){
				$lkRecord=new LuarkotaRecord;
				$lkRecord->cm=$this->cm->Text;
				$lkRecord->id_kab=$this->DDKab->SelectedValue;
				$lkRecord->kecamatan=ucwords($this->KecLuar->Text);
				$lkRecord->kelurahan=ucwords($this->KelurahanLuar->Text);
				$lkRecord->save();
			}
			// saves to the database via Active Record mechanism
            $pasienRecord->save(); 			
            // redirects the browser to the homepage
			
			if($this->getViewState('mode')=='09')
			{

				$this->Response->redirect($this->Service->constructUrl('Pendaftaran.cetakKartuPas',array('cm'=>$this->cm->Text,'nama'=>ucwords($this->nama->Text),'embel'=>ucwords($this->embel->text),'tglLahir'=>$this->tgl_lahir->Text,'jkel'=>$this->collectSelectionListResult($this->jkel),'alamat'=>ucwords($this->alamat->Text),'rt'=>$this->rt->Text,'rw'=>$this->rt->Text,'mode'=>$this->getViewState('mode'))));
			}
			else
			{		
				$this->Response->redirect($this->Service->constructUrl('Pendaftaran.cetakKartuPas',array('cm'=>$this->cm->Text,'nama'=>ucwords($this->nama->Text),'embel'=>ucwords($this->embel->text),'tglLahir'=>$this->tgl_lahir->Text,'jkel'=>$this->collectSelectionListResult($this->jkel),'alamat'=>ucwords($this->alamat->Text),'rt'=>$this->rt->Text,'rw'=>$this->rt->Text)));
			}
			
        }			
	}
	
	
	
    public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	public function batalClicked($sender,$param)
	{		
		$this->Response->reload();
	}

}


?>
