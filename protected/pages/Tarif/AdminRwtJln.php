<?php
class AdminRwtJln extends SimakConf
{   
	public function onInit($param)
	{		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	}	
	 
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)
		{					
			$this->RBjns->Visible=false;
			$this->RBjns->focus();
			$this->RBjns->SelectedValue=1;
			$this->RBjnsChanged($sender,$param);
			
			$this->karcisCtrl->Visible=false;
			
			$this->tdkPanel->Visible=false;
			$this->pdftrPanel->Visible=false;
			
			$this->showSecond->Visible=false;
			$this->showSecondPdftr->Visible=false;
			
			$this->showBayar->Visible=false;
			$this->dtGridCtrl->Display='None';
			$this->showBayarPdftr->Visible=false;
			
			$this->cetakBtn->Enabled=false;
			//$this->cetakBtn->Visible=false;
			 
			//$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			//$this->DDKlinik->dataBind();
			$sql = "SELECT * FROM tbm_bhp_tindakan WHERE st = '0' ORDER BY nama ";			
			$this->DDbhp->DataSource = BhpTindakanRecord::finder()->findAllBySql($sql);
			$this->DDbhp->dataBind();
			
			$sql = "SELECT * FROM tbm_bhp_tindakan WHERE st = '1' ORDER BY nama ";			
			$this->DDalat->DataSource = BhpTindakanRecord::finder()->findAllBySql($sql);
			$this->DDalat->dataBind();
			
			$this->nmTindakan->Text='';
			$this->notrans->focus();
			$this->modeUrut->SelectedValue = '2';
		}	
    }
	
	public function cmCallback($sender,$param)
    {
		$this->panel->render($param->getNewWriter());
	}
	
	public function checkRegister($sender,$param)
    {
		$cm=$this->formatCm($this->notrans->Text);
		$dateNow = date('Y-m-d');
		
		$this->jkel->Text = $this->cariJkel($cm);
		
		$sql = "SELECT *
					FROM
					  tbt_rawat_jalan
					WHERE
					  cm = '$cm' AND 
					  flag = '0' AND 
					  st_alih = '0' AND 
					  TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tgl_visit, wkt_visit))) / 3600 <= 24 ";
		
		$sqlAlih = "SELECT *
					FROM
					  tbt_rawat_jalan
					WHERE
					  cm = '$cm' AND 
					  flag = '0' AND 
					  st_alih = '1' AND 
					  TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tgl_visit, wkt_visit))) / 3600 <= 24 ";
					  			  
		//if(RwtjlnRecord::finder()->findAll('cm = ? AND flag=0 AND st_alih=0 AND tgl_visit=? ',$cm,$dateNow)) 			
		if(RwtjlnRecord::finder()->findBySql($sql))
		{			
			$this->poliCtrl->Visible = true;
			
			$sql = "SELECT 
					  tbm_poliklinik.id,
					  tbm_poliklinik.nama
					FROM
					  tbt_rawat_jalan
					  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
					WHERE
					  tbt_rawat_jalan.cm = '$cm' 
					  AND tbt_rawat_jalan.flag = 0
					  AND TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tbt_rawat_jalan.tgl_visit, tbt_rawat_jalan.wkt_visit))) / 3600 <= 24 
					  AND tbt_rawat_jalan.st_alih = 0";
			
			$arr = $this->queryAction($sql,'S');
					  
			$this->DDKlinik->DataSource = $arr;
			$this->DDKlinik->dataBind();
			$this->DDKlinik->focus();			
			$this->errMsg->Text='';
			
			if(count($arr) == '1')
			{
				$idPoli = PoliklinikRecord::finder()->findBySql($sql)->id;				
				$this->DDKlinik->SelectedValue = $idPoli;
				$this->DDKlinikChanged();
			}
			
			
		}
		elseif(RwtjlnRecord::finder()->findBySql($sqlAlih))		
		{
			$this->poliCtrl->Visible=false;
			$this->showFirst->Visible=true;
			$this->tdkPanel->Visible=false;
			$this->showSecond->Visible=false;
			
			$this->errMsg->Text = '    
				<script type="text/javascript">
					alert("Pasien dengan No.Rekam Medis '.$cm.' sudah alih status ke Rawat Inap !");
					document.all.'.$this->notrans->getClientID().'.focus();
				</script>';
				
			$this->notrans->Text = '';
		}
		else
		{
			$this->poliCtrl->Visible=false;
			$this->showFirst->Visible=true;
			$this->tdkPanel->Visible=false;
			$this->showSecond->Visible=false;
			
			$this->errMsg->Text = '    
				<script type="text/javascript">
					alert("Pasien dengan No.Rekam Medis '.$cm.' tidak ada !");
					document.all.'.$this->notrans->getClientID().'.focus();
				</script>';
				
			$this->notrans->Text = '';
		}
	}
	
	public function DDKlinikChanged()
	{
		$this->DDDokter->Enabled = true;
		$this->DDDokter->focus();
		//$noTrans = $this->getViewState('noTransJalan');
		$cm=$this->formatCm($this->notrans->Text);
		$idKlinik = $this->DDKlinik->SelectedValue;
		$dateNow = date('Y-m-d');
				
		if($idKlinik == '07'){
		$sql = "SELECT 
				  tbd_pegawai.id,
				  tbd_pegawai.nama
				FROM
				   tbd_pegawai,	
				  tbt_rawat_jalan
				  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
				WHERE
				  tbt_rawat_jalan.cm = '$cm' AND 
				  tbt_rawat_jalan.flag = 0 AND 
				  TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tbt_rawat_jalan.tgl_visit, tbt_rawat_jalan.wkt_visit))) / 3600 <= 24 AND 
				  tbd_pegawai.kelompok = 1 AND
				  tbt_rawat_jalan.dokter = tbd_pegawai.id AND
				  tbt_rawat_jalan.id_klinik = '07'
				  AND tbt_rawat_jalan.st_alih = 0 ";
		}
		else
		{
			$sql = "SELECT 
				  tbd_pegawai.id,
				  tbd_pegawai.nama
				FROM
				  tbt_rawat_jalan
				  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
				  INNER JOIN tbd_pegawai ON (tbm_poliklinik.id = tbd_pegawai.poliklinik)
				  AND (tbt_rawat_jalan.dokter = tbd_pegawai.id)
				WHERE
				  tbt_rawat_jalan.cm = '$cm' AND 
				  tbt_rawat_jalan.flag = 0 AND 
				  TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tbt_rawat_jalan.tgl_visit, tbt_rawat_jalan.wkt_visit))) / 3600 <= 24 AND
				  tbd_pegawai.kelompok = 1 AND 
				  tbm_poliklinik.id = '$idKlinik'
				  AND tbt_rawat_jalan.st_alih = 0";
		}
		
		$arr = $this->queryAction($sql,'S');	
		$this->DDDokter->DataSource = $arr;
		$this->DDDokter->dataBind();
		
		if(count($arr) == '1')
		{
			$idDokter = PegawaiRecord::finder()->findBySql($sql)->id;
			$this->DDDokter->SelectedValue = $idDokter;
			$this->DDdokterChanged();
		}
		
		$umur = $this->cariUmur('0',$this->formatCm($this->notrans->Text),$idKlinik);								
		$this->umur->Text = $umur['years'];
	}
	
	public function DDdokterChanged()
    {
		$tglSkrg = date('Y-m-d');
		$cek=$this->formatCm($this->notrans->Text);
		$idKlinik = $this->DDKlinik->SelectedValue;
		
		if($this->RBjns->SelectedValue==1) //jika RadioButton Tindakan yang dipilih
		{
			
			$this->tdkPanel->Visible=true;
			$this->pdftrPanel->Visible=false;
			$this->cetakBtn->Visible=true;
				$tmp = $this->formatCm($this->notrans->Text);
				$dateNow = date('Y-m-d');
				$sql = "SELECT b.nama AS cm,						   
							   b.cm AS cr_masuk, 
							   a.no_trans AS no_trans,
							   a.penanggung_jawab AS penanggung_jawab,
							   a.st_asuransi AS st_asuransi,
							   a.dokter AS dokter,
								c.nama AS wkt_visit,
								d.nama AS tgl_visit,
							   a.flag
							   FROM tbt_rawat_jalan a, 
									tbd_pasien b,
									tbd_pegawai c,
									tbm_poliklinik d
							   WHERE a.cm='$tmp'
									 AND a.cm=b.cm
									AND a.dokter=c.id
									AND a.id_klinik=d.id
									 AND a.flag='0'
									 AND a.id_klinik='$idKlinik'
									 AND a.st_alih='0'
									 AND TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', a.tgl_visit, a.wkt_visit))) / 3600 <= 24 ";		 		 
				$tmpPasien = RwtjlnRecord::finder()->findBySql($sql);
				$this->nama->Text= $tmpPasien->cm;			
				$this->dokter->Text= $tmpPasien->wkt_visit;
				$this->klinik->Text= $tmpPasien->tgl_visit;
				$this->pjPasien->Text= $tmpPasien->penanggung_jawab;
				
				$cm = $tmpPasien->cr_masuk;
				$dokter = $tmpPasien->wkt_visit;
				$klinik = $tmpPasien->tgl_visit;
				
				$idDokter = RwtjlnRecord::finder()->findBySql($sql)->dokter;
				$stAsuransi = RwtjlnRecord::finder()->findBySql($sql)->st_asuransi;
				
				$this->setViewState('cm',$tmpPasien->cr_masuk);
				$this->setViewState('nama',$tmpPasien->cm);
				$this->setViewState('dokter',$tmpPasien->wkt_visit);
				$this->setViewState('klinik',$tmpPasien->tgl_visit);
				$this->setViewState('transaksi',$tmpPasien->no_trans);
				$this->setViewState('notrans',$this->formatCm($this->notrans->Text));
				$this->setViewState('stAsuransi',$stAsuransi);
				
				$this->showSecond->Visible=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';			
				//$this->id_tindakan->Enabled=true;				
				$this->DDTindakan->Enabled=true;
				
				$this->bindTindakan();
				
				$this->tambahBtn->Enabled=true;
				//$this->id_tindakan->focus();
				$this->DDTindakan->focus();
				$this->DDKlinik->Enabled=false;
				$this->DDDokter->Enabled=false;
				$this->RBjns->Enabled=false;
				
				//cek apakah pasein baru atau bukan
				$sql = "SELECT cm FROM tbd_pasien WHERE cm = '$cm' AND st_cetak_kartupasien = '0' ";
				$arr = $this->queryAction($sql,'S');
				
				if(count($arr) > 0)
				{
					//jika pasien baru => masukan tindakan2 wajib untuk pasien baru
					$sql = "SELECT id_tindakan FROM tbt_tindakan_wajib_rwtjln WHERE st = '2' ";
					$arr = $this->queryAction($sql,'S');	
					
					foreach($arr as $row)
					{
						$this->makeTmpTbl($row['id_tindakan']);
					}
				}
				
				//masukan tindakan2 wajib yang sesuai poli masing
				if($stAsuransi == '0')
				{
					$sql = "SELECT id_tindakan FROM tbt_tindakan_wajib_rwtjln WHERE id_poli = '$idKlinik' AND st='0' AND st_asuransi='0' AND dokter='$idDokter' ";
					$arr = $this->queryAction($sql,'S');	
					if(count($arr) > 0)
					{
						foreach($arr as $row)
						{
							$this->makeTmpTbl($row['id_tindakan']);
						}
					}
					else
					{
						$sql = "SELECT id_tindakan FROM tbt_tindakan_wajib_rwtjln WHERE id_poli = '$idKlinik' AND st='0' AND st_asuransi='0' AND dokter IS NULL ";
						$arr = $this->queryAction($sql,'S');	
						foreach($arr as $row)
						{
							$this->makeTmpTbl($row['id_tindakan']);
						}
					}
					
					$sql = "SELECT id_tindakan FROM tbt_tindakan_wajib_rwtjln WHERE id_poli = '$idKlinik' AND st='1' AND st_asuransi='0' ";
				}	
				elseif($stAsuransi == '1')
				{
					$sql = "SELECT id_tindakan FROM tbt_tindakan_wajib_rwtjln WHERE id_poli = '$idKlinik' AND st='0' AND st_asuransi='0' AND dokter='$idDokter' ";
					$arr = $this->queryAction($sql,'S');	
					if(count($arr) > 0)
					{
						foreach($arr as $row)
						{
							$this->makeTmpTbl($row['id_tindakan']);
						}
					}
					else
					{
						$sql = "SELECT id_tindakan FROM tbt_tindakan_wajib_rwtjln WHERE id_poli = '$idKlinik' AND st='0' AND st_asuransi='0' AND dokter IS NULL ";
						$arr = $this->queryAction($sql,'S');	
						foreach($arr as $row)
						{
							$this->makeTmpTbl($row['id_tindakan']);
						}
					}
					
					$sql = "SELECT id_tindakan FROM tbt_tindakan_wajib_rwtjln WHERE id_poli = '$idKlinik' AND st='1' AND st_asuransi='1' ";
					//$sql = "SELECT id_tindakan FROM tbt_tindakan_wajib_rwtjln WHERE id_poli = ('$idKlinik' AND st='4') OR id_poli = ('$idKlinik' AND st='0') ";
				}
				
				
				$arr = $this->queryAction($sql,'S');	
				
				foreach($arr as $row)
				{
					$this->makeTmpTbl($row['id_tindakan']);
				}
				
				$umur = $this->cariUmur('0',$this->formatCm($this->notrans->Text),$idKlinik);								
				$this->umur->Text = $umur['years'];
		}
		else //jika RadioButton Pendaftaran yang dipilih
		{
			$this->tdkPanel->Visible=false;
			$this->pdftrPanel->Visible=true;
			$this->cetakBtn->Visible=true;
			if(PasienRecord::finder()->findAll('cm = ?',$cek)) //jika pasien ditemukan
			{			
				$tmp = $this->formatCm($this->notrans->Text);
				$dateNow = date('Y-m-d');
				$sql = "SELECT b.nama AS cm,						   
					   b.cm AS cr_masuk, 
					   a.no_trans AS no_trans,
					   a.flag
					   FROM tbt_rawat_jalan a, 
							tbd_pasien b							
					   WHERE a.cm='$tmp'
						 AND a.cm=b.cm
						 AND a.flag='0'
						 AND a.st_alih='0'
						  AND TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', a.tgl_visit, a.wkt_visit))) / 3600 <= 24 ";					 
				$tmpPasien = RwtjlnRecord::finder()->findBySql($sql);
				$this->namaPdftr->Text= $tmpPasien->cm;			
				$this->setViewState('cm',$tmpPasien->cr_masuk);
				$this->setViewState('nama',$tmpPasien->cm);
				$this->setViewState('transaksi',$tmpPasien->no_trans);			
				$this->setViewState('notrans',$this->formatCm($this->notrans->Text));
				$this->showSecondPdftr->Visible=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';			
				$this->DDKlinik->Enabled=false;
				$this->DDDokter->Enabled=false;
				$this->noKarcisPdftr->Enabled=false;
				$this->RBjns->Enabled=false;
				$this->DDjnsPdftr->focus();
			}
			else //jika pasien tidak ditemukan
			{
				$this->showFirst->Visible=true;
				$this->pdftrPanel->Visible=false;
				$this->showSecondPdftr->Visible=false;
				$this->errMsg->Text='No. Register tidak ada!!';
				$this->notrans->focus();
			}
		}
    }	
	
	
	public function bindTindakan()
	{
		$modeUrut = $this->modeUrut->SelectedValue;
		$idKlinik = $this->DDKlinik->SelectedValue;
		  
		$sql="SELECT 
				  tbm_nama_tindakan.id,
				  tbm_nama_tindakan.nama,
				  tbm_tarif_tindakan.biaya1,
				  tbm_tarif_tindakan.biaya2,
				  tbm_tarif_tindakan.biaya3,
				  tbm_tarif_tindakan.biaya4
				FROM
				  tbm_tarif_tindakan
				  INNER JOIN tbm_nama_tindakan ON (tbm_tarif_tindakan.id = tbm_nama_tindakan.id)
				WHERE
				  (tbm_tarif_tindakan.biaya1) > 0
				  AND tbm_nama_tindakan.id_klinik = '$idKlinik'  ";
		
		if($modeUrut == '1') //berdasarkan id
			$sql .= "ORDER BY tbm_nama_tindakan.id ASC";
		else //berdasarkan nama
			$sql .= "ORDER BY tbm_nama_tindakan.nama ASC";
		
		
		//$this->DDTindakan->DataSource=NamaTindakanRecord::finder()->findAll();
		$arr=$this->queryAction($sql,'S');//Select row in tabel bro...
		foreach($arr as $row)
		{
			$id = $row['id'];
			$nama = $row['nama'];
			$tarif = $row['biaya1'];
			
			$sql2 = "SELECT * FROM tbt_tindakan_wajib_rwtjln WHERE id_tindakan = '$id'";
			
			if($modeUrut == '1') //berdasarkan id
				{
					$namaTxt = $id.' - '.$nama.' - '.number_format($tarif,0,',','.');
				}
				else //berdasarkan nama
				{
					$namaTxt = $nama.' - '.$id.' - '.number_format($tarif,0,',','.');
				}	
				
				$data[]=array('id'=>$id,'nama'=>$namaTxt);
		}
	
		//$this->DDTindakan->DataSource = NamaTindakanRecord::finder()->findAllBySql($sql);
		$this->DDTindakan->DataSource =  $data;
		$this->DDTindakan->dataBind();
	}
	
	
	public function modeUrutChanged($sender,$param)
	{
	
	}
	
	
	public function makeTmpTbl($item)
    {
		$cm = $this->formatCm($this->notrans->Text);
		$dateNow = date('Y-m-d');
		$idKlinik = $this->DDKlinik->SelectedValue;	
		
		$sql = "SELECT *
					FROM
					  tbt_rawat_jalan
					WHERE
					  cm = '$cm' AND 
					  id_klinik = '$idKlinik' AND 
					  flag = '0' AND 
					  st_alih = '0' AND 
					  TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tgl_visit, wkt_visit))) / 3600 <= 24 ";
		
		$noTransJalan = RwtjlnRecord::finder()->findBySql($sql)->no_trans;
		$stAsuransi = RwtjlnRecord::finder()->findBySql($sql)->st_asuransi;
		//$noTransJalan = RwtjlnRecord::finder()->find('cm = ? AND tgl_visit=? AND id_klinik=?',$cm,$dateNow,$idKlinik)->no_trans;		
		$this->setViewState('noTransJalan',$noTransJalan);
		$this->setViewState('stAsuransi',$stAsuransi);
		
		//cek sudah mendapatkan tindakan rwt jalan sebelumnya
		$sql = "SELECT id_tindakan FROM tbt_kasir_rwtjln WHERE no_trans_rwtjln = '$noTransJalan' AND klinik = '$idKlinik' AND id_tindakan='$item'";
		$arr = $this->queryAction($sql,'S');
		
		if(count($arr) == 0)
		{
			if (!$this->getViewState('nmTable'))
			{
				$nmTable = $this->setNameTable('nmTable');
				$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
											 nama VARCHAR(30) NOT NULL,
											 id_tdk VARCHAR(4) NOT NULL,
											 bhp FLOAT NOT NULL,
											 alat FLOAT NOT NULL,
											 total float NOT NULL,
											 jml float NOT NULL, 									 
											 PRIMARY KEY (id)) ENGINE = MEMORY";
				
				$this->queryAction($sql,'C');//Create new tabel bro...
			}
			else
			{
				$nmTable = $this->getViewState('nmTable');
			}	
			
			
			$sql="SELECT * FROM $nmTable WHERE id_tdk = '$item'";
			$arr = $this->queryAction($sql,'S');			 
			
			if(count($arr) == '0')
			{
				$sql="SELECT 
					  tbm_nama_tindakan.id,
					  tbm_nama_tindakan.nama,
					  (tbm_tarif_tindakan.biaya1) AS total
					FROM
					  tbt_tindakan_wajib_rwtjln
					  INNER JOIN tbm_nama_tindakan ON (tbt_tindakan_wajib_rwtjln.id_tindakan = tbm_nama_tindakan.id)
					  INNER JOIN tbm_tarif_tindakan ON (tbm_nama_tindakan.id = tbm_tarif_tindakan.id) 
							 WHERE tbm_nama_tindakan.id='$item' ";
				
				$arr = $this->queryAction($sql,'S');			 
				foreach($arr as $row)
				{	
					$nama = $row['nama'];
					
					//JIKA PASIEN ASURANSI/JAMPER => CEK APAKAH TINDAKAN YANG DIKENAKAN TERDAPAT DALAM TINDAKAN2 YANG DICOVER ASURANSI
					//JIKA YA => TARIF YANG DIPAKAI ADALAH TARIF YANG DICOVER ASURANSI TERSEBUT
					if($stAsuransi == '1')
					{
						$idPenjamin = RwtjlnRecord::finder()->findByPk($noTransJalan)->penjamin;
						$idPerusAsuransi = RwtjlnRecord::finder()->findByPk($noTransJalan)->perus_asuransi;
						
						$sql = "SELECT * FROM tbm_provider_detail_tindakan WHERE id_provider='$idPerusAsuransi' AND id_tindakan='$item' AND id_poli='$idKlinik'";
						if(ProviderDetailTindakanRecord::finder()->findBySql($sql))
						{
							$total = ProviderDetailTindakanRecord::finder()->findBySql($sql)->tarif;	
						}
						else
						{
							$total = $row['total'];
						}
					}
					else
					{
						$total = $row['total'];
					}
				}
				
				$bhp = BhpTindakanRecord::finder()->findByPk($this->DDbhp->SelectedValue)->tarif;
				$alat = BhpTindakanRecord::finder()->findByPk($this->DDalat->SelectedValue)->tarif;
				
				$jml = $total + $bhp + $alat;
				
				$sql="INSERT INTO $nmTable (nama,bhp,alat,total,jml,id_tdk) VALUES ('$nama','$bhp','$alat','$total','$jml','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...					
			}
			
			
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->UserGrid->DataSource=$arrData;
			$this->UserGrid->dataBind();								
			
			if($this->getViewState('tmpJml')){
				$t = (int)$this->getViewState('tmpJml') + $jml;
				$this->setViewState('tmpJml',$t);
			}else{
				$this->setViewState('tmpJml',$jml+ $bhp + $alat + $total);
			}	
			
			//$this->jmlShow->Text='Rp '.number_format($this->getViewState('tmpJml'),2,',','.');
			$this->showBayar->Visible=true;
			$this->dtGridCtrl->Display='Dynamic';
			$this->DDTindakan->focus();
			$this->cetakBtn->Enabled=true;
		}	
	}
	
	public function prosesClicked()
    {	
		if($this->Page->IsValid)
		{
			$this->nmTindakan->Text='';
			$cm=$this->formatCm($this->notrans->Text);
			$dateNow = date('Y-m-d');
			$idKlinik = $this->DDKlinik->SelectedValue;	
			$noTransJalan = $this->getViewState('noTransJalan');
			$item = $this->DDTindakan->SelectedValue;
			$nmTdk = $this->ambilTxt($this->DDTindakan);
			
			//cek sudah mendapatkan tindakan rwt jalan sebelumnya
			$sql = "SELECT id_tindakan FROM tbt_kasir_rwtjln WHERE no_trans_rwtjln = '$noTransJalan' AND klinik = '$idKlinik' AND id_tindakan='$item'";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > 0)
			{
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question"><b>'.$nmTdk.'</b> sudah diberikan sebelumnya. <br/> Apakah akan melanjutkan penambahan tindakan ?</p>\',timeout: 600000,dialog:{
						modal: true,
						buttons: {
							"Ya": function() {
								jQuery( this ).dialog( "close" );
								maskContent();
								konfirmasi(\'ya\');
							},
							Tidak: function() {
								jQuery( this ).dialog( "close" );
								konfirmasi(\'tidak\');
							}
						}
					}});');	
			}
			else
			{
				$this->prosesClickedTindakan();
			}
		}
	}
	
	public function prosesKonfirmasi($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
		
		//$this->getPage()->getClientScript()->registerEndScript('','alert('.$mode.')');	
				
		if($mode == 'ya')
		{	
			$this->dtGridCtrl->Display='Dynamic';
			$this->prosesClickedTindakan();	
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
		}
		else
		{
			$this->Page->CallbackClient->focus($this->DDTindakan);
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		}
	}
	
	public function prosesClickedCallback($sender,$param)
    {
		//$this->dtGridCtrl->render($param->getNewWriter());
		$this->btnPanel->render($param->getNewWriter());
	}
	
	public function prosesClickedTindakan()
    {	
		$noTransJalan = $this->getViewState('noTransJalan');
		$idKlinik = $this->DDKlinik->SelectedValue;	
		
		$item=$this->DDTindakan->SelectedValue;
			
		if (!$this->getViewState('nmTable'))
		{
			$nmTable = $this->setNameTable('nmTable');
				$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
											 nama VARCHAR(30) NOT NULL,
											 id_tdk VARCHAR(4) NOT NULL,
											 bhp FLOAT NOT NULL,
											 alat FLOAT NOT NULL,
											 total float NOT NULL,
											 jml float NOT NULL, 									 
											 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
			/*
			$klinik=$this->getViewState('id_klinik');
			if ($klinik=='05')
			{
				$jml1=5000;								
				$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('Pendaftaran','0','$jml1','$jml1','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...	
				$jml2=20000;							
				$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('Jasa Dokter Umum','0','$jml2','$jml2','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...				
			}else{
				$jml1=5000;								
				$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('Pendaftaran','0','$jml1','$jml1','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...	
				$jml2=50000;							
				$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('Jasa Dokter Spesialis','0','$jml2','$jml2','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...				
				$jml3=10000;							
				$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('Jasa Rumah Sakit','0','$jml3','$jml3','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...				
				$jml4=10000;							
				$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('USG','0','$jml3','$jml3','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...				
			}
			*/			
			//$item=$this->id_tindakan->Text;
		}
		else
		{
			$nmTable = $this->getViewState('nmTable');
		}	
		
		$sql="SELECT b.nama AS nama, 
					 (a.biaya1) AS total,
					 a.bhp 
					 FROM tbm_tarif_tindakan a, 
						  tbm_nama_tindakan b 
					 WHERE a.id='$item' AND a.id=b.id";
		
		$tmpTarif = TarifTindakanRecord::finder()->findBySql($sql);					 
		
		//$bhp = $this->getViewState('jmlBhp');						
		$nama=$tmpTarif->nama;
		
		//JIKA PASIEN ASURANSI/JAMPER => CEK APAKAH TINDAKAN YANG DIKENAKAN TERDAPAT DALAM TINDAKAN2 YANG DICOVER ASURANSI
		//JIKA YA => TARIF YANG DIPAKAI ADALAH TARIF YANG DICOVER ASURANSI TERSEBUT
		$stAsuransi = RwtjlnRecord::finder()->findByPk($noTransJalan)->st_asuransi;
		if($stAsuransi == '1')
		{
			$idPenjamin = RwtjlnRecord::finder()->findByPk($noTransJalan)->penjamin;
			$idPerusAsuransi = RwtjlnRecord::finder()->findByPk($noTransJalan)->perus_asuransi;
			
			$sql = "SELECT * FROM tbm_provider_detail_tindakan WHERE id_provider='$idPerusAsuransi' AND id_tindakan='$item' AND id_poli='$idKlinik'";
			if(ProviderDetailTindakanRecord::finder()->findBySql($sql))
			{
				$total = ProviderDetailTindakanRecord::finder()->findBySql($sql)->tarif;	
			}
			else
			{
				$total = $tmpTarif->total;
			}
		}
		else
		{
			$total = $tmpTarif->total;
		}							
		
		
		$bhp = BhpTindakanRecord::finder()->findByPk($this->DDbhp->SelectedValue)->tarif;
		$alat = BhpTindakanRecord::finder()->findByPk($this->DDalat->SelectedValue)->tarif;
		
		$jml = $total + $bhp + $alat;	
		
		$sql="INSERT INTO $nmTable (nama,bhp,alat,total,jml,id_tdk) VALUES ('$nama','$bhp','$alat','$total','$jml','$item')";
		$this->queryAction($sql,'C');//Insert new row in tabel bro...
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
		$this->UserGrid->dataBind();
		$this->bayar->Enabled=true;
		$this->bayarBtn->Enabled=true;		
		
		if($this->getViewState('tmpJml')){
			$t = (int)$this->getViewState('tmpJml') + $jml;
			$this->setViewState('tmpJml',$t);
		}else{
			$this->setViewState('tmpJml',$jml + $total + $bhp + $alat);
		}	
		
		$this->jmlShow->Text='Rp '.number_format($this->getViewState('tmpJml'),2,',','.');
		//$this->id_tindakan->Text='';
		$this->DDTindakan->SelectedValue=NULL;
		
		$this->showBayar->Visible=true;
		$this->dtGridCtrl->Display='Dynamic';
		//$this->id_tindakan->focus();
		$this->DDTindakan->focus();	
		$this->DDTindakan->SelectedValue = 'empty';
		$this->DDbhp->SelectedValue = 'empty';
		$this->DDalat->SelectedValue = 'empty';
		
		$this->cetakBtn->Enabled=true;
	}
	
	 public function deleteClicked($sender,$param)
    {
        //if ($this->User->IsAdmin)
		//{
			//if ($this->getViewState('stQuery') == '1')
			//{
				// obtains the datagrid item that contains the clicked delete button
				$item=$param->Item;				
				// obtains the primary key corresponding to the datagrid item
				$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
				// deletes the user record with the specified username primary key				
				$nmTable = $this->getViewState('nmTable');
				
				$sql="SELECT jml FROM $nmTable WHERE id='$ID'";
				$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
				foreach($arrData as $row)
				{
					$n=$row['jml'];					
					$t = ($this->getViewState('tmpJml') - $n);						
					$this->jmlShow->Text='Rp '.number_format($t,2,',','.');
					$this->setViewState('tmpJml',$t);
				}
				
				$sql = "DELETE FROM $nmTable WHERE id='$ID'";
				$this->queryAction($sql,'C');	
				
				$sql="SELECT * FROM $nmTable ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				$this->UserGrid->DataSource=$arrData;
				$this->UserGrid->dataBind();		
				$this->bayar->Text='';											
				//$this->id_tindakan->focus();
				$this->DDTindakan->focus();
				
				if(count($arrData)==0)
				{
					$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
					$this->clearViewState('nmTable');
					$this->clearViewState('tmpJml');
					$this->cetakBtn->Enabled=false;
					$this->showBayar->Visible=false;
					$this->dtGridCtrl->Display='None';
					//$this->id_tindakan->Enabled=true;
					$this->DDTindakan->Enabled=true;
					
					$this->sisaByr->Text='';
				}
			//}	
			
		//}	
    }
	
	public function bayarClicked($sender,$param)
    {
		//$this->showSql->text='-'.$this->getViewState('transaksi');	
		if($this->bayar->Text >= $this->getViewState('tmpJml'))
		{
			$hitung = TPropertyValue::ensureFloat($this->bayar->Text)-TPropertyValue::ensureFloat($this->getViewState('tmpJml'));
			$this->sisaByr->Text='Rp ' . $hitung;
			$this->setViewState('sisa',$hitung);
			//$this->id_tindakan->Enabled=false;
			$this->DDTindakan->Enabled=false;
			
			$this->tambahBtn->Enabled=false;
			$this->cetakBtn->Enabled=true;	
			$this->cetakBtn->focus();	
			$this->errByr->Text='';		
			$this->setViewState('stDelete','1');	
		}
		else
		{
			$this->errByr->Text='Jumlah pembayaran kurang';	
			$this->bayar->focus();
			$this->cetakBtn->Enabled=false;
		}
	}
	
	public function bayarClickedPdftr($sender,$param)
    {
		if($this->bayarBtnPdftr->Text=='Refresh')
		{
			$newRecord= new KasirPendaftaranRecord();
			$newRecord->no_trans=$this->getViewState('transaksi');
			$newRecord->klinik=$this->getViewState('id_klinik');
			$newRecord->dokter=$this->getViewState('id_dokter');
			$newRecord->no_karcis=$this->noKarcisPdftr->Text;
			$newRecord->id_tindakan=$this->DDjnsPdftr->SelectedValue;
			$newRecord->tgl=date('y-m-d');
			$newRecord->waktu=date('G:i:s');
			$newRecord->operator=$this->User->IsUserNip;
			$newRecord->tarif=$this->getViewState('jmlBayarPdftr');
			$newRecord->st_flag='0';
			$newRecord->Save();			
						
			$rwtJlnTmp=RwtjlnRecord::finder()->find('cm = ?',$this->formatCm($this->notrans->Text));
			$rwtJlnTmp->flag='1';
			$rwtJlnTmp->Save();	
			
			$this->clearViewState('tmpJml');
			$this->clearViewState('jmlBayarPdftr');
			$this->clearViewState('cm');
			$this->clearViewState('notrans');
			$this->clearViewState('nama');
			$this->clearViewState('id_klinik');
			$this->clearViewState('id_dokter');
			$this->clearViewState('dokter');
			$this->clearViewState('klinik');
				
			$this->batalClicked();
		}
		else
		{			
			if($this->bayarPdftr->Text >= $this->getViewState('jmlBayarPdftr'))
			{
				$hitung = TPropertyValue::ensureFloat($this->bayarPdftr->Text)-TPropertyValue::ensureFloat($this->getViewState('jmlBayarPdftr'));
				$this->sisaByrPdftr->Text='Rp ' . number_format($hitung,2,',','.');
				$this->setViewState('sisa',$hitung);
				
				$this->DDjnsPdftr->Enabled=true;			
				$this->errByrPdftr->Text='';		
			

				$this->cetakBtn->Enabled=true;
				
				$this->DDjnsPdftr->Enabled=false;
				$this->bayarPdftr->Enabled=false;
				$this->bayarBtnPdftr->Text='Refresh';
			}
			else
			{
				$this->errByrPdftr->Text='Jumlah pembayaran kurang';	
				$this->bayarPdftr->focus();
			}
		}		
	}

	
	public function showNotrans($sender,$param)
	{					
		$this->setViewState('dokter',PegawaiRecord::finder()->findByPk($this->DDDokter->SelectedValue)->nama);
		$this->setViewState('id_dokter',PegawaiRecord::finder()->findByPk($this->DDDokter->SelectedValue)->id);		
		$this->notrans->Enabled=true;
		$this->notrans->focus();		
	}
	
	
	public function RBjnsChanged($sender,$param)
    {
				
		if($this->RBjns->SelectedValue==1)
		{
			$this->noKarcisPdftr->Text='';
			$this->karcisCtrl->Visible=false;
			
			$this->DDKlinik->Enabled=true;
			$this->DDKlinik->focus();
		}
		elseif($this->RBjns->SelectedValue==2)
		{
			$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDKlinik->dataBind();
			$this->DDKlinik->Enabled=false;
		
			$this->karcisCtrl->Visible=true;
			$this->noKarcisPdftr->Enabled=true;
			$this->noKarcisPdftr->focus();
			
			//$this->DDKlinik->Enabled=false;
		}
		else
		{
			$this->karcisCtrl->Visible=false;
			$this->DDKlinik->Enabled=false;
		}
	}
	
	public function noKarcisPdftrChanged($sender,$param)
    {   
		if(strlen($this->noKarcisPdftr->Text)!=8)
		{
			$this->errMsgNoKarcis->Text='No. karcis harus 8 digit!';	
		}
		else
		{
			if(KasirPendaftaranRecord::finder()->findAll('no_karcis = ?',$this->noKarcisPdftr->Text)) //jika no_karcis ditemukan
			{	
				$this->errMsgNoKarcis->Text='No. karcis sudah ada!!';
				$this->noKarcisPdftr->focus();
			}
			else //jika no_karcis tidak ditemukan
			{
				$this->errMsgNoKarcis->Text='';
				$this->DDKlinik->Enabled=true;
				$this->DDKlinik->focus();
			}
		}
    }
	
	public function checkRM($sender,$param)
    {   
		if($this->DDTindakan->SelectedValue!='')
		{
			//$param->IsValid=TarifTindakanRecord::finder()->findByPk($this->id_tindakan->Text);		
			//$nmTdk=TarifTindakanRecord::finder()->findByPk($this->id_tindakan->Text);
			$this->nmTindakan->Text = '';
			$this->test->Text= $this->DDTindakan->SelectedValue;
			$nmTdk=TarifTindakanRecord::finder()->findByPk($this->DDTindakan->SelectedValue);
			if(count($nmTdk)<1)
			{
				$this->nmTindakan->Text='Kode tindakan tidak ada!';
				$this->nmTindakan->ForeColor="#FF0000";
				//$this->id_tindakan->focus();
				$this->DDTindakan->focus();
				
				$this->tambahBtn->Enabled=false;
				$this->nmTindakan->focus();
			}
			else
			{
				//$this->nmTindakan->Text='Nama Tindakan : '.NamaTindakanRecord::finder()->findByPk($this->id_tindakan->Text)->nama;
				//$this->nmTindakan->Text='Nama Tindakan : '.NamaTindakanRecord::finder()->findByPk($this->DDTindakan->SelectedValue)->nama;
				$this->nmTindakan->ForeColor="#000000";
				$this->tambahBtn->Enabled=true;
				$this->DDbhp->focus();
			}
		}
		else
		{
			$this->nmTindakan->Text='';
		}		
		
    }
	
	public function DDjnsPdftrChanged($sender,$param)
    {   		
		if($this->DDjnsPdftr->SelectedValue==1)
		{
			$this->showBayarPdftr->Visible=true;
			$jmlBayarPdftr='12000';
			$this->setViewState('jmlBayarPdftr',$jmlBayarPdftr);
			$this->jmlShowPdftr->Text='Rp. '.number_format($jmlBayarPdftr,2,',','.');
		}
		elseif($this->DDjnsPdftr->SelectedValue==2)
		{
			$this->showBayarPdftr->Visible=true;
			$jmlBayarPdftr='6000';
			$this->setViewState('jmlBayarPdftr',$jmlBayarPdftr);
			$this->jmlShowPdftr->Text='Rp. '.number_format($jmlBayarPdftr,2,',','.');
		}
		elseif($this->DDjnsPdftr->SelectedValue==3)
		{
			$this->showBayarPdftr->Visible=true;
			$jmlBayarPdftr='10000';
			$this->setViewState('jmlBayarPdftr',$jmlBayarPdftr);
			$this->jmlShowPdftr->Text='Rp. '.number_format($jmlBayarPdftr,2,',','.');
		}
		else
		{
			$this->showBayarPdftr->Visible=false;
			$this->jmlShowPdftr->Text='';
			$this->clearViewState('jmlBayarPdftr');
		}		
    }
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}		
	
	public function cetakClicked($sender,$param)
    {		
		$sisaByr=$this->getViewState('sisa');
		$jmlTagihan=$this->getViewState('tmpJml');
		$table=$this->getViewState('nmTable');
		$cm=$this->formatCm($this->notrans->Text);
		
		$nama=$this->getViewState('nama');
		$dokter=$this->getViewState('dokter');
		$klinik=$this->getViewState('klinik');
		$id_dokter=$this->DDDokter->SelectedValue;
		$id_klinik=$this->DDKlinik->SelectedValue;
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;
		
		if($this->RBjns->SelectedValue==1) //jika RadioButton Tindakan yang dipilih
		{
			$jmlBayar=$this->bayar->Text;
			$pjPasien=$this->pjPasien->Text;
			
			
			
			$sql="SELECT * FROM $table ORDER BY id";
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			foreach($arrData as $row)
			{
				$notrans = $this->numCounter('tbt_kasir_rwtjln',KasirRwtJlnRecord::finder(),'08');//key '08' adalah konstanta modul untuk Kasir Rawat Jalan
				
				$transRwtJln= new KasirRwtJlnRecord();
				$transRwtJln->no_trans=$notrans;
				$transRwtJln->no_trans_rwtjln=$this->getViewState('transaksi');
				$transRwtJln->cm=$cm;
				$transRwtJln->klinik=$id_klinik;
				$transRwtJln->dokter=$id_dokter;
				$transRwtJln->id_tindakan=$row['id_tdk'];
				$transRwtJln->tgl=date('y-m-d');
				$transRwtJln->waktu=date('G:i:s');
				$transRwtJln->operator=$nipTmp;
				$transRwtJln->bhp=$row['bhp'];
				$transRwtJln->sewa_alat=$row['alat'];
				$transRwtJln->tarif=$row['total'];
				$transRwtJln->total=$row['jml'];
				$transRwtJln->st_flag='0';
				$transRwtJln->disc='0';
				$transRwtJln->st_kredit='0';
				$transRwtJln->Save();			
			}	
			
			//update st_cetak_kartupasien di tbd_pasien
			$sql = "UPDATE tbd_pasien SET st_cetak_kartupasien = '3' WHERE cm = '$cm' AND st_cetak_kartupasien = '1' ";
			$this->queryAction($sql,'C');
			
			//Update status tbt_rawat_jalan
			/*
			$sql="UPDATE 
					tbt_rawat_jalan 
				SET 
					flag='1' 
				WHERE 
					cm='$cm' 
					AND id_klinik='$id_klinik'
					AND dokter='$id_dokter'
					AND flag='0' 
					AND st_alih='0' ";		
			$this->queryAction($sql,'C');			
			*/
			//$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRwtJln',array('tipe'=>$this->RBjns->SelectedValue,'notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'klinik'=>$klinik,'jmlTagihan'=>$jmlTagihan,'jmlBayar'=>$jmlBayar,'sisa'=>$sisaByr,'table'=>$table,'pjPasien'=>$pjPasien)));
			
			//$this->batalClicked();	
			//$this->Response->redirect($this->Service->constructUrl('Pendaftaran.AdminRwtJlnSukses'));
			
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
			$this->clearViewState('nmTable');
			
			$this->errMsg->Text = '    
			<script type="text/javascript">
				alert("Data Rekam Billing Tindakan Rawat Jalan Telah Masuk Dalam Database.");
				window.location="index.php?page=Pendaftaran.AdminRwtJln"; 
			</script>';
		}
		else //jika RadioButton Pendaftaran yang dipilih
		{	
			$jmlBayar=$this->bayarPdftr->Text;
			$pjPasien=$this->pjPasienPdftr->Text;
			$notrans=$this->numCounter('tbt_kasir_pendaftaran',KasirPendaftaranRecord::finder(),'09');//key '09' adalah konstanta modul untuk Kasir Pendaftaran
						
			$newRecord= new KasirPendaftaranRecord();
			$newRecord->no_trans=$notrans;
			$newRecord->no_trans_pdftr=$this->getViewState('transaksi');			
			$newRecord->klinik=$this->getViewState('id_klinik');
			$newRecord->dokter=$this->getViewState('id_dokter');
			$newRecord->no_karcis=$this->noKarcisPdftr->Text;
			$newRecord->id_tindakan=$this->DDjnsPdftr->SelectedValue;
			$newRecord->tgl=date('y-m-d');
			$newRecord->waktu=date('G:i:s');
			$newRecord->operator=$this->User->IsUserNip;
			$newRecord->tarif=$this->getViewState('jmlBayarPdftr');
			$newRecord->st_flag='0';
			$newRecord->Save();			
						
			$rwtJlnTmp=RwtjlnRecord::finder()->find('cm = ?',$this->formatCm($this->notrans->Text));
			$rwtJlnTmp->flag='1';
			$rwtJlnTmp->Save();	
			
			$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRwtJln',array('tipe'=>$this->RBjns->SelectedValue,'notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'klinik'=>$klinik,'jmlTagihan'=>$this->getViewState('jmlBayarPdftr'),'jmlBayar'=>$jmlBayar,'sisa'=>$sisaByr,'jnsPdftr'=>$this->DDjnsPdftr->SelectedValue,'pjPasien'=>$pjPasien)));
		}
	}
	
	public function batalClicked()
    {		
		$this->bayarBtnPdftr->Text='Bayar';
		$this->RBjns->Enabled=true;		
		$this->DDjnsPdftr->Enabled=true;
		$this->DDjnsPdftr->SelectedIndex=-1;
		$this->bayarPdftr->Enabled=true;
		$this->jmlShow->Text='';
		$this->jmlShowPdftr->Text='';
		$this->sisaByr->Text='';
		$this->sisaByrPdftr->Text='';
		$this->nmTindakan->Text='';
		
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
			$this->UserGrid->DataSource='';
            $this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state				
		}
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('jmlBayarPdftr');
		$this->notrans->Text ='';
					
		
		$this->errByr->Text='';
		$this->errMsg->Text='';
		$this->bayar->Text='';
		$this->bayarPdftr->Text='';
		
		//$this->id_tindakan->Text='';
		$this->DDTindakan->SelectedValue=NULL;
		$this->noKarcisPdftr->Text='';
		$this->pjPasien->Text='';
		$this->pjPasienPdftr->Text='';	
	
		$this->tdkPanel->Visible=false;
		$this->pdftrPanel->Visible=false;
		$this->showSecond->Visible=false;
		$this->showSecondPdftr->Visible=false;
		$this->showBayar->Visible=false;
		$this->dtGridCtrl->Display='None';
		$this->showBayarPdftr->Visible=false;
		
		$this->karcisCtrl->Visible=false;
		$this->noKarcisPdftr->Text='';
		
		$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
		$this->DDKlinik->dataBind();
		$this->DDKlinik->Enabled=false;
		
		$this->DDDokter->Enabled=false;
		$this->DDDokter->SelectedIndex=-1;
		
		$this->RBjns->SelectedValue=1;
		$this->RBjnsChanged($sender,$param);
		
		$this->poliCtrl->Visible = false;
		$this->notrans->Enabled = true;	
		$this->notrans->focus();	
		
		$this->Response->reload();
	}
	
	public function keluarClicked($sender,$param)
	{		
		if($this->getViewState('nmTable')){		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			$this->clearViewState('nmTable');//Clear the view state	
			$this->clearViewState('tmpJml');
			$this->clearViewState('jmlBayarPdftr');
		}
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}	
}
?>
