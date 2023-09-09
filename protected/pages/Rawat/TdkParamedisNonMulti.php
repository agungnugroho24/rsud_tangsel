<?php
class TdkParamedisNonMulti extends SimakConf
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
			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='2' ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDmedis->DataSource=$arrData;	
			$this->DDmedis->dataBind();
			
			$sql="SELECT * FROM tbm_nama_tindakan ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDtindakan->DataSource=$arrData;	
			$this->DDtindakan->dataBind();
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
			$this->DDmedis->Enabled=true;		
			$this->errMsg->Text='';
		}
		else
		{			
			$this->errMsg->Text='Data tidak ada!!';
			$this->catatan->Text='';
			$this->cm->Focus();
		
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='2' ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDmedis->DataSource=$arrData;	
			$this->DDmedis->dataBind();
				
			$sql="SELECT * FROM tbm_nama_tindakan ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDtindakan->DataSource=$arrData;	
			$this->DDtindakan->dataBind();
			
			$this->simpanBtn->Enabled=false;	
			$this->DDmedis->Enabled=false;	
		}
	}
	
	public function chDDmedis($sender,$param)
	{		
		$this->DDtindakan->Enabled=true;	
	}
	
	public function lapTrans()
    {	
		$this->mainPanel->Visible = false;
		$this->konfPanel->Visible = true;
		$this->konfTgl->Text = $this->convertDate(date('Y-m-d'),'3');
		
		$this->nmParamedis->Text = $this->ambilTxt($this->DDmedis);
		$this->nmTdk->Text = $this->ambilTxt($this->DDtindakan);
		
		$cm = $this->cm->Text;
		$tgl = date('Y-m-d');
		$noTransRwtInap = RwtInapRecord::finder()->find('cm = ? AND status = ?', $cm,'0')->no_trans;
		$idParamedis = $this->DDmedis->SelectedValue;
		$idTdk = $this->DDtindakan->SelectedValue;
		
		$this->konfNoCm->Text = $cm;
		$this->konfNmPas->Text = PasienRecord::finder()->findByPk($cm)->nama;
		$this->konfJnsPas->Text = "Pasien Rawat Inap";	
		
		//penghitungan jumlah transaksi yg telah terjadi sebelumnya pd hari ini berdasarkan dokter dan tindakan yg dipilih
		$sql="SELECT 
			  tbt_inap_tdk_medis.wkt,
			  tbd_pegawai.nama,
			  tbm_nama_tindakan.nama AS nm_tdk
			FROM
			  tbt_inap_tdk_medis
			  INNER JOIN tbd_pegawai ON (tbt_inap_tdk_medis.medis = tbd_pegawai.id)
			  INNER JOIN tbm_nama_tindakan ON (tbt_inap_tdk_medis.id_tdk = tbm_nama_tindakan.id)
			WHERE
			  tbt_inap_tdk_medis.no_trans = '$noTransRwtInap' 
			  AND tbt_inap_tdk_medis.medis = '$idParamedis' 
			  AND tbt_inap_tdk_medis.id_tdk = '$idTdk' 
			  AND tbt_inap_tdk_medis.tgl = '$tgl'
			ORDER BY			  
			  tbt_inap_tdk_medis.wkt";
			  
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
		$this->jmlTdk->Text = count($arrData);
		
		//Menampilakan seluruh transaksi tindakan dokter hari ini di datagrid
		$sql="SELECT 
			  tbt_inap_tdk_medis.wkt,
			  tbd_pegawai.nama,
			  tbm_nama_tindakan.nama AS nm_tdk
			FROM
			  tbt_inap_tdk_medis
			  INNER JOIN tbd_pegawai ON (tbt_inap_tdk_medis.medis = tbd_pegawai.id)
			  INNER JOIN tbm_nama_tindakan ON (tbt_inap_tdk_medis.id_tdk = tbm_nama_tindakan.id)
			WHERE
			  tbt_inap_tdk_medis.no_trans = '$noTransRwtInap' 
			  AND tbt_inap_tdk_medis.tgl = '$tgl'
			ORDER BY			  
			  tbt_inap_tdk_medis.wkt";
			  
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
			$idParamedis = $this->DDmedis->SelectedValue;
			$idTdk = $this->DDtindakan->SelectedValue;
			$noTransRwtInap = RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->cm->Text,'0')->no_trans;
			
			//jika ditemukan tindakan yg sama dengan dokter penindak yg sama dalam 1 hari 
			if($idTdk == 'IG02')
			{
				if(InapTdkMedisRecord::finder()->find('no_trans=? AND id_tdk=? AND tgl=?',$noTransRwtInap,$idTdk,date('Y-m-d'))) //konfirmasi muncul
				{
					$this->lapTrans();
				}
				else
				{
					$this->prosesClicked();
				}
			}
			else
			{
				if(InapTdkMedisRecord::finder()->find('no_trans=? AND medis=? AND id_tdk=? AND tgl=?',$noTransRwtInap,$idParamedis,$idTdk,date('Y-m-d'))) //konfirmasi muncul
				{
					$this->lapTrans();
				}
				else
				{
					$this->prosesClicked();
				}
			}	
		}	
	}
	
	public function prosesClicked()
    {		
		$newInapTdkMedis= new InapTdkMedisRecord();
		$newInapTdkMedis->no_trans=$this->getViewState('notrans');
		$newInapTdkMedis->cm=$this->cm->Text;
		$newInapTdkMedis->medis=$this->DDmedis->SelectedValue;
		$newInapTdkMedis->id_tdk=$this->DDtindakan->SelectedValue;
		$newInapTdkMedis->tgl=date('y-m-d');
		$newInapTdkMedis->wkt=date('G:i:s');
		$newInapTdkMedis->operator=$this->User->IsUserNip;
		$newInapTdkMedis->catatan=$this->catatan->Text;
		$newInapTdkMedis->Save();			
		
		$updateRwtInap=RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->cm->Text,'0');
		//$updateRwtInap->status='1';
		$updateRwtInap->Save();	
		
		$this->clearViewState('notrans');
		$this->errMsg->Text='';	
		
		$this->Response->redirect($this->Service->constructUrl('Rawat.TdkParamedis'));
	}
	
	public function batalClicked($sender,$param)
    {				
		$this->clearViewState('notrans');
		$this->Response->reload();
		/*
		$this->cm->Text='';
		$this->errMsg->Text='';
		$this->catatan->Text='';
		$this->cm->Focus();
		
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='2'";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDmedis->DataSource=$arrData;	
		$this->DDmedis->dataBind();
			
		$sql="SELECT * FROM tbm_nama_tindakan ";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDtindakan->DataSource=$arrData;	
		$this->DDtindakan->dataBind();
		
		$this->simpanBtn->Enabled=false;	
		$this->DDmedis->Enabled=false;
		$this->DDtindakan->Enabled=false;	
		*/
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->clearViewState('notrans');
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}	
}
?>
