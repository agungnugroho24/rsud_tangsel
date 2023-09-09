<?php
class TdkKhusus extends SimakConf
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
			
			$this->dataPanel->Enabled = false;
			$this->dataPanel->Display = 'None';
			
			$this->konfPanel->Display = 'None';
			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='2' OR kelompok='1' ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDmedis->DataSource=$arrData;	
			$this->DDmedis->dataBind();
			
			$sql="SELECT id, nama FROM tbm_tindakan_khusus ORDER BY nama";
			$data=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDtindakan->DataSource=$data;	
			$this->DDtindakan->dataBind();
		}	
		
		if($this->getViewState('nmTable'))
		{
			$this->gridPanel->Display = 'Dynamic';	
			$this->simpanBtn->Enabled = true;
		}
		else
		{
			$this->gridPanel->Display = 'None';		
			$this->simpanBtn->Enabled = false;	
		}	
    }
	
	public function checkCM($sender,$param)
    {
		$cm = $this->formatCm($this->cm->Text);
        // valid if the cm is not found in the database
		$data=RwtInapRecord::finder()->find('cm = ? AND status = ?', $cm,'0');
        if($data)
		{
			$this->setViewState('notrans',$data->no_trans);
			//$this->DDmedis->Enabled=true;
			//$this->catatan->Enabled=true;
			$this->errMsg->Text='';
			
			$this->cm->Enabled = false;
			
			$this->dataPanel->Enabled = true;
			$this->dataPanel->Display = 'Dynamic';
			
			$this->nama->Text = PasienRecord::finder()->findByPk($cm)->nama;
			$umur = $this->cariUmur('1',$cm,'');
$this->umur->Text = $umur['years'];
			$this->jkel->Text = $this->cariJkel($cm);
			
			//$this->Page->CallbackClient->focus($this->DDmedis);
			$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDmedis->getClientID().'.focus();');	
		}
		else
		{			
			//$this->errMsg->Text='Data tidak ada!!';
			$this->catatan->Text='';
			$this->cm->Focus();
			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='2' OR kelompok='1' ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDmedis->DataSource=$arrData;	
			$this->DDmedis->dataBind();
			
			//$this->DDmedis->Enabled=false;
			
			$this->cm->Text = '';
			$this->Page->CallbackClient->focus($this->cm);
			
			$this->getPage()->getClientScript()->registerEndScript
						('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>No. Rekam Medis '.$cm.' belum masuk rawat inap !</p>\',timeout: 3000,dialog:{
							modal: true}});');	
		}
	}
	
	public function cmCallBack($sender,$param)
   	{
		$this->dataPanel->render($param->getNewWriter());
	}
	
	public function tambahCallBack($sender,$param)
   	{
		$this->konfPanel->render($param->getNewWriter());
	}
	
	public function tambahClicked()
    {
		if($this->Page->IsValid)
        {
			$idParamedis = $this->DDmedis->SelectedValue;
			$idTdk = $this->DDtindakan->SelectedValue;
			$noTransRwtInap = RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->formatCm($this->cm->Text),'0')->no_trans;
			
			//jika ditemukan tindakan yg sama dengan dokter penindak yg sama dalam 1 hari 
			if(InapTdkKhususRecord::finder()->find('no_trans=? AND id_tdk=? AND tgl=?',$noTransRwtInap,$idTdk,date('Y-m-d'))) //konfirmasi muncul
			{
				$this->lapTrans();
			}
			else
			{
				$this->makeTmpTbl();
			}
		}
	}
			
	public function makeTmpTbl()
    {
		
			$id_paramedis = $this->DDmedis->SelectedValue;
			$nama = $this->ambilTxt($this->DDmedis);
			
			$id_tdk = $this->DDtindakan->SelectedValue;
			$nama_tdk = $this->ambilTxt($this->DDtindakan);
			$catatan = $this->catatan->Text;
			$tarif = TindakanKhususRecord::finder()->findByPk($id_tdk)->tarif;
			
			if (!$this->getViewState('nmTable'))
			{			
				$nmTable = $this->setNameTable('nmTable');
				$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
											 id_paramedis VARCHAR(7) DEFAULT NULL,
											 nama VARCHAR(100) DEFAULT NULL,
											 id_tdk VARCHAR(7) DEFAULT NULL,
											 nama_tdk VARCHAR(150) DEFAULT NULL,
											 catatan VARCHAR(255) DEFAULT NULL,
											 tarif FLOAT DEFAULT 0,
											 PRIMARY KEY (id)) ENGINE = MEMORY";
				
				$this->queryAction($sql,'C');	
			}
			else
			{
				$nmTable = $this->getViewState('nmTable');							
			}	
			
			$sql="SELECT * FROM $nmTable WHERE id_paramedis = '$id_paramedis' AND id_tdk = '$id_tdk' ";
			$arrData = $this->queryAction($sql,'S');
			
			if(count($arrData) > 0)//data sudah ada
			{
				$this->errMsg->Text = '    
				<script type="text/javascript">
					jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Tindakan '.$nama_tdk.' oleh '.$nama.' sudah ditambahkan sebelumnya !</p>\',timeout: 3000,dialog:{modal: true}});
				</script>';
					
				//$this->getPage()->getClientScript()->registerEndScript('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Rekam Visite oleh '.$nama.' sudah ditambahkan sebelumnya !</p>\',timeout: 3000,dialog:{modal: true}});');		
			}
			else
			{
				$sql="INSERT INTO $nmTable
					(id_paramedis,nama,id_tdk,nama_tdk,catatan,tarif) 
				VALUES 
					('$id_paramedis','$nama','$id_tdk','$nama_tdk','$catatan','$tarif')";
					
				$this->queryAction($sql,'C');//Insert new row in tabel bro...			
			}
			
			$sql="SELECT * FROM $nmTable ORDER BY id";
				
			$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->UserGrid->dataBind();
			
			$this->DDmedis->SelectedValue = 'empty';
			$this->catatan->Text = '';
			
			$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDmedis->getClientID().'.focus();');	
	}
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}		
	
	public function deleteClicked($sender,$param)
    {	
		$item=$param->Item;		
		$ID=$this->UserGrid->DataKeys[$item->ItemIndex];			
		$nmTable = $this->getViewState('nmTable');
		
		$sql = "DELETE FROM $nmTable WHERE id='$ID'";						
		$this->queryAction($sql,'C');
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			
		$jmlData=0;
		foreach($arrData as $row)
		{
			$jmlData++;
		}
		
		if($jmlData==0)
		{
			$this->simpanBtn->Enabled = false;
			
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table		
			$this->clearViewState('nmTable');//Clear the view state	
		}
		
		$this->UserGrid->DataSource=$arrData;
		$this->UserGrid->dataBind();
		
		$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDmedis->getClientID().'.focus();');	
    }		
	
	public function lapTrans()
    {	
		$this->mainPanel->Display = 'None';
		$this->konfPanel->Display = 'Dynamic';
		
		$this->konfTgl->Text = $this->convertDate(date('Y-m-d'),'3');
		
		$this->nmParamedis->Text = $this->ambilTxt($this->DDmedis);
		$this->nmTdk->Text = $this->ambilTxt($this->DDtindakan);
		
		$cm = $this->formatCm($this->cm->Text);
		$tgl = date('Y-m-d');
		$noTransRwtInap = RwtInapRecord::finder()->find('cm = ? AND status = ?', $cm,'0')->no_trans;
		$idParamedis = $this->DDmedis->SelectedValue;
		$idTdk = $this->DDtindakan->SelectedValue;
		
		$this->konfNoCm->Text = $cm;
		$this->konfNmPas->Text = PasienRecord::finder()->findByPk($cm)->nama;
		$this->konfJnsPas->Text = "Pasien Rawat Inap";	
		
		//penghitungan jumlah transaksi yg telah terjadi sebelumnya pd hari ini berdasarkan dokter dan tindakan yg dipilih
		$sql="SELECT 
			  tbt_inap_tdk_khusus.wkt,
			  tbm_tindakan_khusus.nama AS nm_tdk
			FROM
			  tbt_inap_tdk_khusus
			  INNER JOIN tbm_tindakan_khusus ON (tbt_inap_tdk_khusus.id_tdk = tbm_tindakan_khusus.id)
			WHERE
			  tbt_inap_tdk_khusus.no_trans = '$noTransRwtInap' 
			  AND tbt_inap_tdk_khusus.id_tdk = '$idTdk' 
			  AND tbt_inap_tdk_khusus.tgl = '$tgl'
			ORDER BY			  
			  tbt_inap_tdk_khusus.wkt";
			  
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->jmlVisite->Text = count($arrData);
		
		//Menampilakan seluruh transaksi tindakan dokter hari ini di datagrid
		$sql="SELECT 
			  tbt_inap_tdk_khusus.wkt,
			  tbd_pegawai.nama,
			  tbm_tindakan_khusus.nama AS nm_tdk
			FROM
			  tbt_inap_tdk_khusus
			  INNER JOIN tbd_pegawai ON (tbt_inap_tdk_khusus.medis = tbd_pegawai.id)
			  INNER JOIN tbm_tindakan_khusus ON (tbt_inap_tdk_khusus.id_tdk = tbm_tindakan_khusus.id)
			WHERE
			  tbt_inap_tdk_khusus.no_trans = '$noTransRwtInap' 
			  AND tbt_inap_tdk_khusus.tgl = '$tgl'
			ORDER BY			  
			  tbt_inap_tdk_khusus.wkt";
			  
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->UserGrid2->DataSource=$arrData;
		$this->UserGrid2->dataBind();
		
		$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->tidakBtn->getClientID().'.focus();');	
	}
	
	public function tidakBtnClicked()
    {
		$this->mainPanel->Display = 'Dynamic';
		$this->konfPanel->Display = 'None';
		$this->DDmedis->SelectedValue = 'empty';
		$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDmedis->getClientID().'.focus();');	
	}
	
	public function yaBtnClicked()
    {
		$this->makeTmpTbl();
		$this->mainPanel->Display = 'Dynamic';
		$this->konfPanel->Display = 'None';
	}
	
	public function simpanClicked2()
    {
		if($this->Page->IsValid)
		{
			$idParamedis = $this->DDmedis->SelectedValue;
			$idTdk = $this->DDtindakan->SelectedValue;
			$noTransRwtInap = RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->formatCm($this->cm->Text),'0')->no_trans;
			
			//jika ditemukan tindakan yg sama dengan dokter penindak yg sama dalam 1 hari 
			if($idTdk == 'IG02')
			{
				if(InapTdkKhususRecord::finder()->find('no_trans=? AND id_tdk=? AND tgl=?',$noTransRwtInap,$idTdk,date('Y-m-d'))) //konfirmasi muncul
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
				if(InapTdkKhususRecord::finder()->find('no_trans=? AND medis=? AND id_tdk=? AND tgl=?',$noTransRwtInap,$idParamedis,$idTdk,date('Y-m-d'))) //konfirmasi muncul
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
	
	public function simpanClicked()
    {	
		$nmTable = $this->getViewState('nmTable');
		$tgl = date('y-m-d');	
		$wkt = date('G:i:s');
		
		$cm = $this->formatCm($this->cm->Text);
			
		$sql = "SELECT kelas FROM tbt_rawat_inap WHERE cm = '$cm' AND status = '0' ";
		$kls_pasien = RwtInapRecord::finder()->findBySql($sql)->kelas;
		
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		foreach($arrData as $row)
		{
			$idParamedis = $row['id_paramedis'];
			$idTdk = $row['id_tdk'];
			
			$catatan = $row['catatan'];
			
			$tarifStandar = $row['tarif'];
			$persetaseKelas = KelasKamarRecord::finder()->findByPk($kls_pasien)->persentase;
			
			$newInapTdkKhusus= new InapTdkKhususRecord();
			$newInapTdkKhusus->no_trans = $this->getViewState('notrans');
			$newInapTdkKhusus->cm=$this->formatCm($this->cm->Text);
			$newInapTdkKhusus->medis = $idParamedis;
			$newInapTdkKhusus->id_tdk = $idTdk;
			$newInapTdkKhusus->tgl = $tgl;
			$newInapTdkKhusus->wkt = $wkt;
			$newInapTdkKhusus->operator = $this->User->IsUserNip;
			$newInapTdkKhusus->catatan = $row['catatan'];
			$newInapTdkKhusus->tarif = $tarifStandar + ($tarifStandar * $persetaseKelas / 100);
			$newInapTdkKhusus->Save();	
			
			//$updateRwtInap=RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->formatCm($this->cm->Text),'0');
			//$updateRwtInap->status='1';
			//$updateRwtInap->Save();	
		}
		
		$this->clearViewState('notrans');
		
		$this->queryAction($this->getViewState('nmTable'),'D');
		$this->clearViewState('nmTable');
		
		$this->errMsg->Text = ' 
			<script type="text/javascript">
					unmaskContent();
					jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Rekam Tindakan Khusus Sukses.</p>\',timeout: 200000,dialog:{
						modal: true,closeOnEscape:false,
						buttons: {
							"OK": function() {
								okClicked();
							}
						}
					}});
					jQuery( \'a.ui-dialog-titlebar-close\' ).remove();
				</script>';
		
	}
	
	public function dialogClicked($sender,$param)
    {
		$this->Response->redirect($this->Service->constructUrl('Rawat.TdkKhusus'));
	}
	
	public function batalClicked($sender,$param)
    {				
		$this->clearViewState('notrans');
		$this->cm->Text ='';
		$this->errMsg->Text='';
		$this->catatan->Text='';
		$this->cm->Focus();
		
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='2' OR kelompok='1' ORDER BY nama";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDmedis->DataSource=$arrData;	
		$this->DDmedis->dataBind();
					
		$this->simpanBtn->Enabled=false;	
		//$this->DDmedis->Enabled=false;		
		
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table		
			$this->clearViewState('nmTable');//Clear the view state	
		}
			
		$this->Response->Reload();
	}
}
?>
