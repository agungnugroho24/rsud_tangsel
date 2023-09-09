<?php
class KamarBayi extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('1');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	

	public function onPreRender($param)
	{				
		parent::onPreRender($param);		
			
		if(!$this->IsPostBack)
		{					
			$this->cm->Focus();
			
			$this->dataPanel->Enabled = false;
			$this->dataPanel->Display = 'None';
			
			$sql="SELECT * FROM tbm_kamar_nama ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDjnsKamar->DataSource = $arrData;	
			$this->DDjnsKamar->dataBind();
			
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
			//$this->DDjnsKamar->Enabled=true;
			//$this->catatan->Enabled=true;
			$this->errMsg->Text='';
			
			$this->cm->Enabled = false;
			
			$this->dataPanel->Enabled = true;
			$this->dataPanel->Display = 'Dynamic';
			
			$this->nama->Text = PasienRecord::finder()->findByPk($cm)->nama;
			$umur = $this->cariUmur('1',$cm,'');
			$this->umur->Text = $umur['years'];
			$this->jkel->Text = $this->cariJkel($cm);
			
			//$this->Page->CallbackClient->focus($this->DDjnsKamar);
			$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDjnsKamar->getClientID().'.focus();');	
		}
		else
		{			
			//$this->errMsg->Text='Data tidak ada!!';
			$this->catatan->Text='';
			$this->cm->Focus();
			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1' ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDjnsKamar->DataSource=$arrData;	
			$this->DDjnsKamar->dataBind();
			
			//$this->DDjnsKamar->Enabled=false;
			
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
	
	public function tambahClicked()
    {
		if($this->Page->IsValid)
        {
			$idDokter = $this->DDjnsKamar->SelectedValue;
			$idTdk = $this->DDtindakan->SelectedValue;
			$noTransRwtInap = RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->formatCm($this->cm->Text),'0')->no_trans;
			
			//jika ditemukan tindakan yg sama dengan dokter penindak yg sama dalam 1 hari 
			if(InapBiayaLainRecord::finder()->find('no_trans=? AND id_tdk=? AND tgl=?',$noTransRwtInap,$idTdk,date('Y-m-d'))) //konfirmasi muncul
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
		
			$id_dokter = $this->DDjnsKamar->SelectedValue;
			$nama = $this->ambilTxt($this->DDjnsKamar);
			
			$id_tdk = $this->DDtindakan->SelectedValue;
			$nama_tdk = $this->ambilTxt($this->DDtindakan);			
			$catatan = $this->catatan->Text;
			$tarif = BiayaLainRecord::finder()->findByPk($id_tdk)->tarif;
			
			if (!$this->getViewState('nmTable'))
			{			
				$nmTable = $this->setNameTable('nmTable');
				$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
											 id_dokter VARCHAR(7) DEFAULT NULL,
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
			
			$sql="SELECT * FROM $nmTable WHERE id_tdk = '$id_tdk' ";
			$arrData = $this->queryAction($sql,'S');
			
			if(count($arrData) > 0)//data sudah ada
			{
				$this->errMsg->Text = '    
				<script type="text/javascript">
					jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Biaya '.$nama_tdk.' sudah ditambahkan sebelumnya !</p>\',timeout: 3000,dialog:{modal: true}});
				</script>';
					
				//$this->getPage()->getClientScript()->registerEndScript('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Rekam Visite oleh '.$nama.' sudah ditambahkan sebelumnya !</p>\',timeout: 3000,dialog:{modal: true}});');		
			}
			else
			{
				$sql="INSERT INTO $nmTable
					(id_dokter,nama,id_tdk,nama_tdk,catatan,tarif) 
				VALUES 
					('$id_dokter','$nama','$id_tdk','$nama_tdk','$catatan','$tarif')";
					
				$this->queryAction($sql,'C');//Insert new row in tabel bro...			
			}
			
			$sql="SELECT * FROM $nmTable ORDER BY id";
				
			$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->UserGrid->dataBind();
			
			//$this->DDjnsKamar->SelectedValue = 'empty';
			$this->DDtindakan->SelectedValue = 'empty';
			$this->catatan->Text = '';
			
			$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDtindakan->getClientID().'.focus();');	
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
		
		$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDtindakan->getClientID().'.focus();');	
    }		
	
	public function lapTrans()
    {	
		$this->mainPanel->Display = 'None';
		
		$this->konfTgl->Text = $this->convertDate(date('Y-m-d'),'3');
		
		$this->nmDokter->Text = $this->ambilTxt($this->DDjnsKamar);
		$this->nmTdk->Text = $this->ambilTxt($this->DDtindakan);
		
		$cm = $this->formatCm($this->cm->Text);
		$tgl = date('Y-m-d');
		$noTransRwtInap = RwtInapRecord::finder()->find('cm = ? AND status = ?', $cm,'0')->no_trans;
		$idDokter = $this->DDjnsKamar->SelectedValue;
		$idTdk = $this->DDtindakan->SelectedValue;
		
		$this->konfNoCm->Text = $cm;
		$this->konfNmPas->Text = PasienRecord::finder()->findByPk($cm)->nama;
		$this->konfJnsPas->Text = "Pasien Rawat Inap";	
		
		//penghitungan jumlah transaksi yg telah terjadi sebelumnya pd hari ini berdasarkan dokter dan tindakan yg dipilih
		$sql="SELECT 
			  tbt_inap_biaya_lain.wkt,
			  tbm_biaya_lain.nama AS nm_tdk
			FROM
			  tbt_inap_biaya_lain
			  INNER JOIN tbm_biaya_lain ON (tbt_inap_biaya_lain.id_tdk = tbm_biaya_lain.id)
			WHERE
			  tbt_inap_biaya_lain.no_trans = '$noTransRwtInap'
			  AND tbt_inap_biaya_lain.id_tdk = '$idTdk' 
			  AND tbt_inap_biaya_lain.tgl = '$tgl'
			ORDER BY			  
			  tbt_inap_biaya_lain.wkt";
			  
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->jmlVisite->Text = count($arrData);
		
		//Menampilakan seluruh transaksi tindakan dokter hari ini di datagrid
		$sql="SELECT 
			  tbt_inap_biaya_lain.wkt,
			  tbm_biaya_lain.nama AS nm_tdk
			FROM
			  tbt_inap_biaya_lain
			  INNER JOIN tbm_biaya_lain ON (tbt_inap_biaya_lain.id_tdk = tbm_biaya_lain.id)
			WHERE
			  tbt_inap_biaya_lain.no_trans = '$noTransRwtInap'
			  AND tbt_inap_biaya_lain.tgl = '$tgl'
			ORDER BY			  
			  tbt_inap_biaya_lain.wkt";
			  
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->UserGrid2->DataSource=$arrData;
		$this->UserGrid2->dataBind();
		
		$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->tidakBtn->getClientID().'.focus();');	
	}
	
	public function tidakBtnClicked()
    {
		$this->mainPanel->Display = 'Dynamic';
		$this->DDjnsKamar->SelectedValue = 'empty';
		$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDtindakan->getClientID().'.focus();');	
	}
	
	public function yaBtnClicked()
    {
		$this->makeTmpTbl();
		$this->mainPanel->Display = 'Dynamic';
	}
	
	public function simpanClicked()
    {	
		if($this->Page->IsValid)
		{
			$tgl = date('y-m-d');	
			$wkt = date('G:i:s');
			
			$cm = $this->formatCm($this->cm->Text);
			$idKamar = $this->DDjnsKamar->SelectedValue;
			$nmKamar = $this->ambilTxt($this->DDjnsKamar);
			
			$sql = "SELECT kelas FROM tbt_rawat_inap WHERE cm = '$cm' AND status = '0' ";
			$kls_pasien = RwtInapRecord::finder()->findBySql($sql)->kelas;
			
			$tarif = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array($idKamar, $kls_pasien))->tarif;
			
			$data= new InapKamarBayiRecord();
			$data->no_trans_inap = $this->getViewState('notrans');
			$data->cm=$this->formatCm($this->cm->Text);
			$data->id_kamar = $idKamar;
			$data->nama_kamar = $nmKamar;
			$data->lama_inap = intval($this->jml->Text);
			$data->tarif = intval($this->jml->Text) * $tarif;
			$data->tgl = $tgl;
			$data->wkt = $wkt;
			$data->operator = $this->User->IsUserNip;
			
			$data->Save();	
			
			$this->clearViewState('notrans');
			
			$this->getPage()->getClientScript()->registerEndScript
			('','unmaskContent();
				jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Rekam Kamar Bayi Sukses.</p>\',timeout: 200000,dialog:{
							modal: true,closeOnEscape:false,
							buttons: {
								"OK": function() {
									okClicked();
								}
							}
						}});
						jQuery( \'a.ui-dialog-titlebar-close\' ).remove();
			');	
		}
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
		}
	}
	
	public function dialogClicked($sender,$param)
    {
		$this->Response->redirect($this->Service->constructUrl('Rawat.KamarBayi'));
	}
	
	public function batalClicked($sender,$param)
    {				
		$this->Response->Reload();
	}
}
?>
