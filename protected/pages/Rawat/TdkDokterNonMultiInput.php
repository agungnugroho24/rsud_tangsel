<?php
class TdkDokterNonMultiInput extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('1');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
	public function onLoad($param)
	{
		parent::onLoad($param);
		
		if(!$this->IsPostBack)
		{					
			$this->cm->Focus();
			$this->simpanBtn->Enabled=false;
			
			$sql="SELECT id, nama FROM tbd_pegawai WHERE kelompok='1' ORDER BY nama";
			$data=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDmedis->DataSource=$data;	
			$this->DDmedis->dataBind();
			
			$sql="SELECT id, nama FROM tbm_nama_tindakan ORDER BY nama";
			$data=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDtindakan->DataSource=$data;	
			$this->DDtindakan->dataBind();
			
		}	
    }

	public function cekCM()
	{
		if(RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->cm->Text,'0'))
			{
				$this->setViewState('notrans',RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->cm->Text,'0')->no_trans);
				$this->DDmedis->Enabled=true;
				$this->DDtindakan->Enabled=true;
				$this->simpanBtn->Enabled=true;
			}else
			{
				$this->batalClicked();
			}
	}
	
	public function lapTrans()
    {	
		$this->mainPanel->Visible = false;
		$this->konfPanel->Visible = true;
		$this->konfTgl->Text = $this->convertDate(date('Y-m-d'),'3');
		
		$this->nmDokter->Text = $this->ambilTxt($this->DDmedis);
		$this->nmTdk->Text = $this->ambilTxt($this->DDtindakan);
		
		$cm = $this->cm->Text;
		$tgl = date('Y-m-d');
		$noTransRwtInap = RwtInapRecord::finder()->find('cm = ? AND status = ?', $cm,'0')->no_trans;
		$idDokter = $this->DDmedis->SelectedValue;
		$idTdk = $this->DDtindakan->SelectedValue;
		
		$this->konfNoCm->Text = $cm;
		$this->konfNmPas->Text = PasienRecord::finder()->findByPk($cm)->nama;
		$this->konfJnsPas->Text = "Pasien Rawat Inap";	
		
		//penghitungan jumlah transaksi yg telah terjadi sebelumnya pd hari ini berdasarkan dokter dan tindakan yg dipilih
		$sql="SELECT 
			  tbt_inap_tdk_dokter.wkt,
			  tbd_pegawai.nama,
			  tbm_nama_tindakan.nama AS nm_tdk
			FROM
			  tbt_inap_tdk_dokter
			  INNER JOIN tbd_pegawai ON (tbt_inap_tdk_dokter.dokter = tbd_pegawai.id)
			  INNER JOIN tbm_nama_tindakan ON (tbt_inap_tdk_dokter.id_tdk = tbm_nama_tindakan.id)
			WHERE
			  tbt_inap_tdk_dokter.no_trans = '$noTransRwtInap' 
			  AND tbt_inap_tdk_dokter.dokter = '$idDokter' 
			  AND tbt_inap_tdk_dokter.id_tdk = '$idTdk' 
			  AND tbt_inap_tdk_dokter.tgl = '$tgl'
			ORDER BY			  
			  tbt_inap_tdk_dokter.wkt";
			  
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
		$this->jmlTdk->Text = count($arrData);
		
		//Menampilakan seluruh transaksi tindakan dokter hari ini di datagrid
		$sql="SELECT 
			  tbt_inap_tdk_dokter.wkt,
			  tbd_pegawai.nama,
			  tbm_nama_tindakan.nama AS nm_tdk
			FROM
			  tbt_inap_tdk_dokter
			  INNER JOIN tbd_pegawai ON (tbt_inap_tdk_dokter.dokter = tbd_pegawai.id)
			  INNER JOIN tbm_nama_tindakan ON (tbt_inap_tdk_dokter.id_tdk = tbm_nama_tindakan.id)
			WHERE
			  tbt_inap_tdk_dokter.no_trans = '$noTransRwtInap' 
			  AND tbt_inap_tdk_dokter.tgl = '$tgl'
			ORDER BY			  
			  tbt_inap_tdk_dokter.wkt";
			  
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->UserGrid2->DataSource=$arrData;
		$this->UserGrid2->dataBind();
	}
	
	public function tidakBtnClicked()
    {
		$this->mainPanel->Visible = true;
		$this->konfPanel->Visible = false;
		$this->DDmedis->SelectedIndex = -1;
		$this->DDtindakan->SelectedIndex = -1;
	}
	
	public function yaBtnClicked()
    {
		$this->prosesClicked();
		$this->mainPanel->Visible = true;
		$this->konfPanel->Visible = false;
	}
	
	public function simpanClicked()
    {
		if($this->Page->IsValid)
		{
			$idDokter = $this->DDmedis->SelectedValue;
			$idTdk = $this->DDtindakan->SelectedValue;
			$noTransRwtInap = RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->cm->Text,'0')->no_trans;
			
			//jika ditemukan tindakan yg sama dengan dokter penindak yg sama dalam 1 hari 
			if(InapTdkDokterRecord::finder()->find('no_trans=? AND dokter=? AND id_tdk=? AND tgl=?',$noTransRwtInap,$idDokter,$idTdk,date('Y-m-d'))) //konfirmasi muncul
			{
				$this->lapTrans();
			}
			else
			{
				$this->prosesClicked();
			}
		}
	}
	
	public function prosesClicked()
    {		
		$newInapTdkDokter= new InapTdkDokterRecord();
		$newInapTdkDokter->no_trans=$this->getViewState('notrans');
		$newInapTdkDokter->cm=$this->cm->Text;
		$newInapTdkDokter->dokter=$this->DDmedis->SelectedValue;
		$newInapTdkDokter->id_tdk=$this->DDtindakan->SelectedValue;
		$newInapTdkDokter->tgl=date('y-m-d');
		$newInapTdkDokter->wkt=date('G:i:s');
		$newInapTdkDokter->operator=$this->User->IsUserNip;
		$newInapTdkDokter->catatan=$this->catatan->text;
		$newInapTdkDokter->Save();		
		
		$updateRwtInap=RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->cm->Text,'0');
		//$updateRwtInap->status='1';
		$updateRwtInap->Save();	
		
		$this->clearViewState('notrans');
		
		$this->Response->redirect($this->Service->constructUrl('Rawat.TdkDokter'));
		
	}
	
	public function batalClicked()
    {		
		$this->clearViewState('notrans');
		$this->Response->reload();
	}
	
	public function keluarClicked($sender,$param)
    {		
		$this->Response->redirect($this->Service->constructUrl('Rawat.TdkDokter'));
	}

}
?>
