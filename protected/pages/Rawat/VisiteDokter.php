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
			
			$this->dataPanel->Enabled = false;
			$this->dataPanel->Display = 'None';
			
			$this->konfPanel->Display = 'None';
			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1' ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDdokter->DataSource=$arrData;	
			$this->DDdokter->dataBind();
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
			$this->DDdokter->Enabled=true;
			//$this->catatan->Enabled=true;
			$this->errMsg->Text='';
			
			$this->cm->Enabled = false;
			
			$this->dataPanel->Enabled = true;
			$this->dataPanel->Display = 'Dynamic';
			
			$this->nama->Text = PasienRecord::finder()->findByPk($cm)->nama;
			$umur = $this->cariUmur('1',$cm,'');
			$this->umur->Text = $umur['years'];
			$this->jkel->Text = $this->cariJkel($cm);
			
			//$this->Page->CallbackClient->focus($this->DDdokter);
			$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDdokter->getClientID().'.focus();');	
		}
		else
		{			
			//$this->errMsg->Text='Data tidak ada!!';
			$this->catatan->Text='';
			$this->cm->Focus();
		
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1' ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDdokter->DataSource=$arrData;	
			$this->DDdokter->dataBind();
			
			$this->DDdokter->Enabled=false;
			
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
			$idDokter = $this->DDdokter->SelectedValue;
			$noTransRwtInap = RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->formatCm($this->cm->Text),'0')->no_trans;
			
			//jika ditemukan visite dengan dokter yg sama dalam 1 hari 
			if(InapVisiteRecord::finder()->find('no_trans=? AND dokter=? AND tgl=?',$noTransRwtInap,$idDokter,date('Y-m-d'))) //konfirmasi muncul
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
		
			$id_dokter = $this->DDdokter->SelectedValue;
			$nama = $this->ambilTxt($this->DDdokter);
			$catatan = $this->catatan->Text;
			
			if (!$this->getViewState('nmTable'))
			{			
				$nmTable = $this->setNameTable('nmTable');
				$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
											 id_dokter VARCHAR(7) DEFAULT NULL,
											 nama VARCHAR(100) DEFAULT NULL,
											 catatan VARCHAR(255) DEFAULT NULL,
											 PRIMARY KEY (id)) ENGINE = MEMORY";
				
				$this->queryAction($sql,'C');	
			}
			else
			{
				$nmTable = $this->getViewState('nmTable');							
			}	
			
			$sql="SELECT * FROM $nmTable WHERE id_dokter = '$id_dokter'";
			$arrData = $this->queryAction($sql,'S');
			
			if(count($arrData) > 0)//data sudah ada
			{
				$this->errMsg->Text = '    
				<script type="text/javascript">
					jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Rekam Visite oleh '.$nama.' sudah ditambahkan sebelumnya !</p>\',timeout: 3000,dialog:{modal: true}});
				</script>';
					
				//$this->getPage()->getClientScript()->registerEndScript('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Rekam Visite oleh '.$nama.' sudah ditambahkan sebelumnya !</p>\',timeout: 3000,dialog:{modal: true}});');		
			}
			else
			{
				$sql="INSERT INTO $nmTable
					(id_dokter,nama,catatan) 
				VALUES 
					('$id_dokter','$nama','$catatan')";
					
				$this->queryAction($sql,'C');//Insert new row in tabel bro...			
			}
			
			$sql="SELECT * FROM $nmTable ORDER BY id";
				
			$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->UserGrid->dataBind();
			
			$this->DDdokter->SelectedValue = 'empty';
			$this->catatan->Text = '';
			
			$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDdokter->getClientID().'.focus();');	
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
		
		$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDdokter->getClientID().'.focus();');	
    }		
	
	public function lapTrans()
    {	
		$this->mainPanel->Display = 'None';
		$this->konfPanel->Display = 'Dynamic';
		
		$this->konfTgl->Text = $this->convertDate(date('Y-m-d'),'3');
		
		$this->nmDokter->Text = $this->ambilTxt($this->DDdokter);
		
		$cm = $this->formatCm($this->cm->Text);
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
		
		$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->tidakBtn->getClientID().'.focus();');	
	}
	
	public function tidakBtnClicked()
    {
		$this->mainPanel->Display = 'Dynamic';
		$this->konfPanel->Display = 'None';
		$this->DDdokter->SelectedValue = 'empty';
		$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDdokter->getClientID().'.focus();');	
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
			$idDokter = $this->DDdokter->SelectedValue;
			$noTransRwtInap = RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->formatCm($this->cm->Text),'0')->no_trans;
			
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
			$idDokter = $row['id_dokter'];
			$catatan = $row['catatan'];
			
			$sql = "SELECT spesialis, st_sub_spesialis FROM tbd_pegawai WHERE id = '$idDokter' ";		
			$spesialis = PegawaiRecord::finder()->findBySql($sql)->spesialis;
			$subSpesialis = PegawaiRecord::finder()->findBySql($sql)->st_sub_spesialis;
			
			if($spesialis == ''|| $spesialis == NULL){
				$sql = "SELECT tarif FROM tbm_inap_visite WHERE kel_dokter = '0'";
				//$tarif_visite = TarifVisiteRwtInapRecord::finder()->find('kel_dokter = ? AND kelas = ?', '0',$kls_pasien)->tarif;
			}
			/*
			elseif($spesialis == '20')//kelompok dokter sub spesialis
			{
				$sql = "SELECT tarif FROM tbm_inap_visite WHERE kel_dokter = '2' AND kelas = '$kls_pasien'";
				//$tarif_visite = TarifVisiteRwtInapRecord::finder()->find('kel_dokter = ? AND kelas = ?', '0',$kls_pasien)->tarif;
			}
			*/
			else
			{ 
				if($subSpesialis == '0')
					$sql = "SELECT tarif FROM tbm_inap_visite WHERE kel_dokter = '1'";
				elseif($subSpesialis == '1')
					$sql = "SELECT tarif FROM tbm_inap_visite WHERE kel_dokter = '2'";
				//$tarif_visite = TarifVisiteRwtInapRecord::finder()->find('kel_dokter = ? AND kelas = ?', '1',$kls_pasien)->tarif;
			} 
			
			$tarifStandar = TarifVisiteRwtInapRecord::finder()->findBySql($sql)->tarif;	
			$persetaseKelas = KelasKamarRecord::finder()->findByPk($kls_pasien)->persentase;
			
			$persetaseKelas = KelasKamarRecord::finder()->findByPk($kls_pasien)->persentase;
			$newInapVisite= new InapVisiteRecord();
			$newInapVisite->no_trans = $this->getViewState('notrans');
			$newInapVisite->cm = $cm;
			$newInapVisite->dokter = $idDokter;
			$newInapVisite->tgl = $tgl;
			$newInapVisite->wkt = $wkt;
			$newInapVisite->operator = $this->User->IsUserNip;
			$newInapVisite->catatan = $catatan;
			$newInapVisite->tarif = $tarifStandar + ($tarifStandar * $persetaseKelas / 100);
			$newInapVisite->Save();		
			
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
					jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Rekam Visite Dokter Sukses.</p>\',timeout: 200000,dialog:{
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
		$this->Response->redirect($this->Service->constructUrl('Rawat.VisiteDokter'));
	}
	
	public function batalClicked($sender,$param)
    {				
		$this->clearViewState('notrans');
		$this->cm->Text ='';
		$this->errMsg->Text='';
		$this->catatan->Text='';
		$this->cm->Focus();
		
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1' ORDER BY nama";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDdokter->DataSource=$arrData;	
		$this->DDdokter->dataBind();
					
		$this->simpanBtn->Enabled=false;	
		$this->DDdokter->Enabled=false;		
		
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table		
			$this->clearViewState('nmTable');//Clear the view state	
		}
			
		$this->Response->Reload();
	}
}
?>
