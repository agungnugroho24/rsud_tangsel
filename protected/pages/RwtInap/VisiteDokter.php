<?php
class VisiteDokter extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('1');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	

	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{					
			$this->cm->Focus();
			$this->simpanBtn->Enabled=false;
			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1'";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDdokter->DataSource=$arrData;	
			$this->DDdokter->dataBind();
		}		
    }
	
	public function checkCM($sender,$param)
    {
        // valid if the cm is not found in the database
		$data=RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->cm->Text,'0');
        if($data)
		{
			$this->setViewState('notrans',$data->no_trans);
			$this->simpanBtn->Enabled=true;	
			$this->DDdokter->Enabled=true;
			$this->errMsg->Text='';
		}
		else
		{			
			$this->errMsg->Text='Data tidak ada!!';
			$this->catatan->Text='';
			$this->cm->Focus();
		
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1'";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDdokter->DataSource=$arrData;	
			$this->DDdokter->dataBind();
			
			$this->simpanBtn->Enabled=false;	
			$this->DDdokter->Enabled=false;
		}
	}
	
	public function lapTrans()
    {	
		$this->mainPanel->Visible = false;
		$this->konfPanel->Visible = true;
		$this->konfTgl->Text = $this->convertDate(date('Y-m-d'),'3');
		
		$this->nmDokter->Text = $this->ambilTxt($this->DDdokter);
		
		$cm = $this->cm->Text;
		$tgl = date('Y-m-d');
		$noTransRwtInap = RwtInapRecord::finder()->find('cm = ? AND status = ?', $cm,'0')->no_trans;
		$idDokter = $this->DDdokter->SelectedValue;
		
		$this->konfNoCm->Text = $cm;
		$this->konfNmPas->Text = PasienRecord::finder()->findByPk($cm)->nama;
		$this->konfJnsPas->Text = "Pasien Rawat Inap";	
		
		//penghitungan jumlah transaksi yg telah terjadi sebelumnya pd hari ini berdasarkan dokter yg dipilih
		$sql="SELECT 
			  tbt_inap_visite.wkt,
			  tbd_pegawai.nama
			FROM
			  tbt_inap_visite
			  INNER JOIN tbd_pegawai ON (tbt_inap_visite.dokter = tbd_pegawai.id)
			WHERE
			  tbt_inap_visite.no_trans = '$noTransRwtInap' 
			  AND tbt_inap_visite.dokter = '$idDokter' 
			  AND tbt_inap_visite.tgl = '$tgl'
			ORDER BY			  
			  tbt_inap_visite.wkt";
			  
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->jmlVisite->Text = count($arrData);
		
		//Menampilakan seluruh transaksi visite dokter hari ini di datagrid
		$sql="SELECT 
			  tbt_inap_visite.wkt,
			  tbd_pegawai.nama
			FROM
			  tbt_inap_visite
			  INNER JOIN tbd_pegawai ON (tbt_inap_visite.dokter = tbd_pegawai.id)
			WHERE
			  tbt_inap_visite.no_trans = '$noTransRwtInap' 
			  AND tbt_inap_visite.tgl = '$tgl'
			ORDER BY			  
			  tbt_inap_visite.wkt";
			  
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->UserGrid2->DataSource=$arrData;
		$this->UserGrid2->dataBind();
	}
	
	public function tidakBtnClicked()
    {
		$this->mainPanel->Visible = true;
		$this->konfPanel->Visible = false;
		$this->DDdokter->SelectedIndex = -1;
	}
	
	public function yaBtnClicked()
    {
		$this->prosesClicked();
		$this->mainPanel->Visible = true;
		$this->konfPanel->Visible = false;
	}
	
	public function simpanClicked()
    {
		$idDokter = $this->DDdokter->SelectedValue;
		$noTransRwtInap = RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->cm->Text,'0')->no_trans;
		
		//jika ditemukan visite dengan dokter yg sama dalam 1 hari 
		if(InapVisiteRecord::finder()->find('no_trans=? AND dokter=? AND tgl=?',$noTransRwtInap,$idDokter,date('Y-m-d'))) //konfirmasi muncul
		{
			$this->lapTrans();
		}
		else
		{
			$this->prosesClicked();
		}
	}
	
	public function prosesClicked()
    {	
		$idDokter = $this->DDdokter->SelectedValue;
		
		$sql = "SELECT spesialis FROM tbd_pegawai WHERE id = '$idDokter' ";		
		$spesialis = PegawaiRecord::finder()->findBySql($sql)->spesialis;
		
		$cm = $this->cm->Text;
		$sql = "SELECT kelas FROM tbt_rawat_inap WHERE cm = '$cm' AND status = '0' ";
		$kls_pasien = RwtInapRecord::finder()->findBySql($sql)->kelas;
		/*
		if($spesialis == ''){
			$tarif_visite = TarifVisiteRwtInapRecord::finder()->find('kel_dokter = ? AND kelas = ?', '0',$kls_pasien)->tarif;
		}
		else
		{	
			$tarif_visite = TarifVisiteRwtInapRecord::finder()->find('kel_dokter = ? AND kelas = ?', '1',$kls_pasien)->tarif;
		}*/
		if($spesialis == ''|| $spesialis == NULL){
			$sql = "SELECT tarif FROM tbm_inap_visite WHERE kel_dokter = '0' AND kelas = '$kls_pasien'";
			//$tarif_visite = TarifVisiteRwtInapRecord::finder()->find('kel_dokter = ? AND kelas = ?', '0',$kls_pasien)->tarif;
		}
		else
		{ 
			$sql = "SELECT tarif FROM tbm_inap_visite WHERE kel_dokter = '1' AND kelas = '$kls_pasien'";
			//$tarif_visite = TarifVisiteRwtInapRecord::finder()->find('kel_dokter = ? AND kelas = ?', '1',$kls_pasien)->tarif;
		} 
		
		$tarif_visite = TarifVisiteRwtInapRecord::finder()->findBySql($sql)->tarif;	
		
		$newInapVisite= new InapVisiteRecord();
		$newInapVisite->no_trans=$this->getViewState('notrans');
		$newInapVisite->cm=$this->cm->Text;
		$newInapVisite->dokter=$this->DDdokter->SelectedValue;
		$newInapVisite->tgl=date('y-m-d');
		$newInapVisite->wkt=date('G:i:s');
		$newInapVisite->operator=$this->User->IsUserNip;
		$newInapVisite->catatan=$this->catatan->Text;
		$newInapVisite->tarif=$tarif_visite;
		$newInapVisite->Save();			
		
		$updateRwtInap=RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->cm->Text,'0');
		//$updateRwtInap->status='1';
		$updateRwtInap->Save();	
		
		$this->clearViewState('notrans');
		$this->errMsg->Text='';	
		
		$this->Response->redirect($this->Service->constructUrl('Rawat.VisiteDokter'));
	}
	
	public function batalClicked($sender,$param)
    {				
		$this->clearViewState('notrans');
		$this->cm->Text='';
		$this->errMsg->Text='';
		$this->catatan->Text='';
		$this->cm->Focus();
		
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1'";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDdokter->DataSource=$arrData;	
		$this->DDdokter->dataBind();
					
		$this->simpanBtn->Enabled=false;	
		$this->DDdokter->Enabled=false;		
	}
}
?>
