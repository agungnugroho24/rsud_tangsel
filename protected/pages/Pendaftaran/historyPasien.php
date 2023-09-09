<?php
class historyPasien extends SimakConf
{

	public function onLoad($param)
	{		
		parent::onLoad($param);
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.kunjPas'));	
		if(!$this->IsPostBack)  // if the page is initially requested
		{
		$cm=$this->Request['cm'];
		$this->setViewState('cm',$cm);
		$notrans=$this->Request['notrans'];
		$this->setViewState('notrans',$notrans);
		
		$sql = "SELECT a.cm, 
					   a.nama, 
					   a.tmp_lahir, 
					   a.tgl_lahir,
					   a.kelompok,
					   a.perusahaan,
					   a.umur,
					   a.jkel,
					   b.nama AS agama,
					   a.alamat,
					   a.rt,
					   a.rw,
					   a.kabupaten,
					   a.kecamatan,
					   a.kelurahan,
					   a.status,
					   c.keluhan,
					   c.catatan,
					   d.nama AS dokter,
					   e.nama AS klinik,
					   c.tgl_visit,
					   c.wkt_visit,
					   f.dtd,
					   c.icd,
					   f.indonesia
					   
				FROM tbd_pasien a,
					 tbm_agama b,
					 tbt_rawat_jalan c,
					 tbd_pegawai d,
					 tbm_poliklinik e,
					 tbm_icd f
				WHERE a.cm='$cm'
					  AND c.no_trans='$notrans'
					  AND c.cm=a.cm
					  AND a.agama=b.id
					  AND c.dokter=d.id
					  AND c.id_klinik=e.id
					  AND c.icd=f.kode ";
		
		$item=$this->queryAction($sql,'R');	
		//$this->sqlShow->Text=$sql;
		// Retrieves the existing user data. This is equivalent to:
		// $userRecord=$this->getUserRecord();
		$pasienRecord=$this->PasienRecord;
		//$lkRecord=$this->LuarKotaRecord;
		foreach($item as $data){
			/*
			if ($data['kabupaten'] == '01'){//Bila kota bandung!
				$tmp = KecamatanRecord::finder()->findByPk($pasienRecord->kecamatan);		 
				$this->kec->Text=$tmp->getColumnValue('nama');			
				$tmp = KelurahanRecord::finder()->findByPk($pasienRecord->kelurahan);		 
				$this->kel->Text=$tmp->getColumnValue('nama');
			}else{
				$cm_tmp=$pasienRecord->cm;	
				$luarkotaRecord=LuarkotaRecord::finder()->findByPk($cm_tmp);			
				$this->kec->Text=$luarkotaRecord->kecamatan;
				$this->kel->Text=$luarkotaRecord->kelurahan;			
			}*/	
				$cm_tmp=$pasienRecord->cm;	
				$luarkotaRecord=LuarkotaRecord::finder()->findByPk($cm_tmp);			
				$this->kec->Text=$luarkotaRecord->kecamatan;
				$this->kel->Text=$luarkotaRecord->kelurahan;			
			// Populates the input controls with the existing user data
				$this->cm->Text=$data['cm'];				
				$this->nama->Text=$data['nama'];
				$this->tmp_lahir->Text=$data['tmp_lahir'];
				$this->tgl_lahir->Text=$this->ConvertDate($data['tgl_lahir'],'1');
				$tmp = KelompokRecord::finder()->findByPk($data['kelompok']);		 
				$this->kelompok->Text=$tmp->getColumnValue('nama');
				if($data['kelompok'] == '05')
				{
					$tmp=PerusahaanRecord::finder()->findByPk($data['perusahaan']);
					$this->kontrak->Text=$tmp->getColumnValue('nama');
				}else{
					$this->kontrak->Text='-';
				}			
				$this->umur->Text=$data['umur'];				
				$this->agama->Text=$data['agama'];
				if($data['jkel'] == '0'){
					$this->jkel->Text='Laki-laki';
				}else{	
					$this->jkel->Text='Perempuan';
				}	
				$this->alamat->Text=$data['alamat'];
				$this->rt->Text=$data['rt'];
				$this->rw->Text=$data['rw'];
				$tmp = KabupatenRecord::finder()->findByPk($data['kabupaten']);
				$this->kab->Text=$tmp->getColumnValue('nama');			
				$this->stKawin->Text=$this->stKawin($data['status']);
				$this->tgl->Text=$this->ConvertDate($data['tgl_visit'],'1');
				$this->jam->Text=$data['wkt_visit'];
				$this->klinik->Text=$data['klinik'];
				$this->dokter->Text=$data['dokter'];
				$this->keluhan->Text=$data['keluhan'];
				$this->dtd->Text=$data['dtd'];
				$this->icd->Text=$data['icd'];
				$this->nmIcd->Text=$data['indonesia'];			
				$this->catatan->Text=$data['catatan'];				
			}
		}
	}  		
	
	protected function originPath ()
	{
		$mode=$this->Request['mode'];
		if($mode == '01'){
			$balik='Pendaftaran.DaftarCariData';
		}else if($mode == '02'){
			$balik='Pendaftaran.DaftarCariRwtJln';
		}else if($mode == '03'){
			$balik='Pendaftaran.DaftarCariRwtInap';
		}else if($mode == '04'){
			$balik='Pendaftaran.DaftarCariRwtIgd';
		}else if($mode == '05'){
			$balik='Pendaftaran.kunjPas';
		}else if($mode == '06'){
			$balik='Pendaftaran.kunjPasIgd';
		}
		return $balik;
	}
	
	protected function getPasienRecord()
	{
		// use Active Record to look for the specified username
		$cm=$this->Request['cm'];
		$this->setViewState('cm',$cm);
		$pasienRecord=PasienRecord::finder()->findByPk($cm);		
		if(!($pasienRecord instanceof PasienRecord))
			throw new THttpException(500,'id tidak benar.');
		return $pasienRecord;
	}	
	
    public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl($this->originPath(),array('cm'=>$this->getViewState('cm'),'notrans'=>$this->getViewState('notrans'))));		
	}
}
?>