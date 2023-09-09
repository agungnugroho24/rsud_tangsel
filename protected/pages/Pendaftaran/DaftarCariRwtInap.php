<?php
class DaftarCariRwtInap extends SimakConf
{   
	private $sortExp = "a.cm ";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	
		
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
		if(!$this->IsPostBack && !$this->IsCallBack)
        {   		
			$this->DDBulan->DataSource=BulanRecord::finder()->findAll();
			$this->DDBulan->dataBind();
			$this->DDKelas->DataSource=KelasKamarRecord::finder()->findAll();
			$this->DDKelas->dataBind();	
			
			$this->bindGrid();									
			$this->cariCM->focus();					
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
			
		}		
    }		
	
	// ---------- datagrid helper functions --------
    
    public function getSortExpression() {
        if ($this->getViewState('sortExpression',null)!==null) {
            return $this->getViewState('sortExpression');
        }
        // set default in case there's no 'sortExpression' key in viewstate
        $this->setViewState('sortExpression', $this->sortExp);
        return $this->sortExp;
    }

    public function setSortExpression($sort) {
        $this->setViewState('sortExpression',$sort);
    }

    public function getSortDirection() {
        if ($this->getViewState('sortDirection',null)!==null) {
            return $this->getViewState('sortDirection');
        }
        // set default in case there's no 'sortDirection' key in viewstate
        $this->setViewState('sortDirection', $this->sortDir);
        return $this->sortDir;
    }

    public function setSortDirection($sort) {
        $this->setViewState('sortDirection',$sort);
    }


    // get data and bind it to datagrid
    private function bindGrid()
    {
        $this->pageSize = $this->dtgSomeData->PageSize;
        $this->offset = $this->pageSize * $this->dtgSomeData->CurrentPageIndex;
        
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
            $sql = "SELECT a.cm,
						   a.nama, 
						   a.jkel,						  
						   g.nama AS dokter, 
						   e.tgl_masuk AS tgl_masuk,						   					   
						   f.nama AS kelas_nama,
						   e.kelas AS kelas_id,
						   e.st_rujukan AS rujukan_id,
						   h.nama AS rujukan_nama,
						   e.no_trans AS trans	
						   FROM tbd_pasien a,								
								tbm_kabupaten c,								
								tbt_rawat_inap e,								
								tbm_kamar_kelas f,
								tbd_pegawai g,
							    tbm_rujukan_nama h
						   WHERE
								a.cm=e.cm
								AND e.status = '0'								
								AND f.id=e.kelas
								AND e.st_rujukan=h.id
								AND g.id=e.dokter ";			
			/*					
			$sql = "SELECT a.cm,
								   a.nama, 
								   a.jkel,						  
								   g.nama AS dokter, 
								   e.tgl_masuk AS tgl_masuk,						   					   
								   f.nama AS kelas_nama,
								   i.id_kmr_skrg AS kelas_id,
								   e.st_rujukan AS rujukan_id,
								   h.nama AS rujukan_nama,
								   e.no_trans AS trans	
					   FROM tbd_pasien a,								
								tbm_kabupaten c,								
								tbt_rawat_inap e,								
								tbm_kamar_kelas f,
								tbd_pegawai g,
								tbm_rujukan_nama h,
								tbt_inap_kamar i
					   WHERE
								a.cm=e.cm
								AND e.status = '0'								
								AND f.id=e.kelas
								AND e.st_rujukan=h.id
								AND g.id=e.dokter 
								AND i.id_kmr_skrg=f.id ";*/			
			if($this->getViewState('cariByNama') <> '')	
			{
				$nama = $this->getViewState('cariByNama');		
				if($this->getViewState('elemenBy') === true){
					$sql .= "AND a.nama LIKE '%$nama%' ";
				}
				else
				{	
					$sql .= "AND a.nama LIKE '$nama%' ";
				}
			}
						
			if($this->getViewState('cariByAlamat') <> '')
			{
				$alamat = $this->getViewState('cariByAlamat');				
				if($this->getViewState('elemenBy') === true){
					$sql .= "AND a.alamat LIKE '%$alamat%' ";
				}
				else
				{	
					$sql .= "AND a.alamat LIKE '$alamat%' ";
				}
			}
				
			if($this->getViewState('cariByCM') <> '')
			{
				$cm = $this->getViewState('cariByCM');
				$sql .= "AND a.cm = '$cm' ";			
			}	
			
			if($this->getViewState('cariByDokter') <> '')
			{
				$dok = $this->getViewState('cariByDokter');
				$sql .= "AND e.dokter = '$dok' ";			
			}
			
			if($this->getViewState('cariByKlinik') <> '')
			{
				$kln = $this->getViewState('cariByKlinik');
				$sql .= "AND e.id_klinik = '$kln' ";			
			}
							
			if($this->getViewState('urutBy') <> '')
			{
				$kel=$this->getViewState('urutBy');
				$sql .= "AND a.kelompok = '$kel' ";	
			}					
			
			if($this->getViewState('cariByTgl') <> '')
			{
				$cariTgl = $this->getViewState('cariByTgl');
				$tgl = $this->ConvertDate($cariTgl,'2');//Convert date to mysql
				$sql .= "AND e.tgl_masuk = '$tgl' ";
			}	
			
			if($this->getViewState('cariByBulan') <> '')
			{
				$cariBln = $this->getViewState('cariByBulan');
				$blnAkhir = $this->akhirBln($cariBln);
				$blnAwal = date('Y') . '-' . $cariBln . '-01';
				$sql .= "AND e.tgl_masuk BETWEEN '$blnAwal' AND '$blnAkhir' ";		
			}	
			
			if($this->getViewState('cariByKelas') <> '')
			{
				$cariByKelas = $this->getViewState('cariByKelas');
				$sql .= "AND i.id_kmr_skrg = '$cariByKelas' ";
			}
			
			if($this->getViewState('cariByKamar') <> '')
			{
				$cariByKamar = $this->getViewState('cariByKamar');
				$sql .= "AND e.kamar = '$cariByKamar' ";	
			}	
				
			$sql .= " GROUP BY a.cm ";
			 
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlDataPas->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
			//$this->showSql->Text=$sql;
        }
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->dtgSomeData->DataSource = $session["SomeData"];
            $this->dtgSomeData->DataBind();
        }
    }
    
    // ---------- datagrid page and sort events ---------------
    
    protected function dtgSomeData_PageIndexChanged($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGrid();
    }

    protected function dtgSomeData_SortCommand($sender,$param)
    {
        if ($this->SortExpression !== $param->SortExpression)
        {
            $this->SortExpression = $param->SortExpression;
            $this->SortDirection = "ASC";
        }
        else {
            if ($this->SortDirection === "ASC")
                $this->SortDirection = "DESC";
            else
                $this->SortDirection = "ASC";
        }
        
        // many people won't set this to the first page. this can lead to usability problems.
        // think in what happens if the user is on the 3rd page and changes the sorting field. 
        // you will sort the items on that page if you are using cached data (either in session or "true" cache). 
        // imagine now that the user moves on to page 4. the data on page 4 will be sorted out but it will be 
        // sorted disregarding the other items in other pages. other pages could have items that are "lower" or 
        // "bigger" than the ones displayed. You could have items with the sorting field starting with letter "C" 
        // on page 3 and on page 4 items with the sorting field starting with letter "A". 
        // you could sort all the cached data to solve this but then what page you will show to the user? stick with page 3?
        // I find it better to refresh the data and allways move on to the first page.
        
        // clear data in session because we need to refresh it from db
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
        $this->bindGrid();

    }
	/*
	protected function dtgSomeData_ItemCreated($sender,$param)
    {
        $item=$param->Item;	
		
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes
           	   
        } 
    }*/
	
	public function dtgSomeData_ItemCreated($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='DeleteItem')
        {
            if($this->User->IsAdmin){
				// add an aleart dialog to delete buttons
				$item->Hapus->Button->Attributes->onclick='if(!confirm(\'Apakah anda yakin ingin menghapus data ini..?\')) return false;';		
			}else{
				$item->Hapus->Button->Visible='0';
			}	
        }
    }
	
	public function deleteButtonClicked($sender,$param)
    {
        if ($this->User->IsAdmin)
		{
			$tmpVar=$this->authApp('2');
			if($tmpVar == "True")
			{
				// obtains the datagrid item that contains the clicked delete button
				$item=$param->Item;
				// obtains the primary key corresponding to the datagrid item
				$ID=$this->dtgSomeData->DataKeys[$item->ItemIndex];
				// deletes the user record with the specified username primary key
				
				//PasienRecord::finder()->deleteByPk($ID);	
				
				$sql="DELETE FROM tbt_rawat_inap WHERE cm='$ID' and status='0' ";
				$arr=$this->queryAction($sql,'C');
				
				$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarCariRwtInap'));
			}
			
		}	
    }	
	
	protected function dtgSomeData_EditCommand($sender,$param)
    {
        $this->dtgSomeData->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGrid();
    }
	
	/*
	public function hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar)
	{
		/* ---------------- RULES --------------------------
			jam masuk > 0 detik = 1 hari
			jam masuk > 24 jam = 2 hari
			jam masuk > 1 menit && jam masuk <= 24 jam = 1 hari
		*/
	/*	       
        //convert to unix timestamp		
		list($G,$i,$s) = explode(":",$wktMasuk);
		list($Y,$m,$d) = explode("-",$tglMasuk);
		$wktAwal = mktime($G,$i,$s,$m,$d,$Y);
		
		list($G,$i,$s) = explode(":",$wktKeluar);
		list($Y,$m,$d) = explode("-",$tglKeluar);
		$wktAkhir = mktime($G,$i,$s,$m,$d,$Y);

        $offset = $wktAkhir-$wktAwal;
		
		$jmlHari = $offset/60/60/24; //pembulatan ke atas
		
		if($jmlHari < 1)
		{
			$jmlHari = ceil($jmlHari); //pembulatan ke atas
		}
		else
		{
			$jmlHari = floor($jmlHari); //pembulatan ke atas
		}
        
        return $jmlHari;
	}
	*/
	
    protected function dtgSomeData_UpdateCommand($sender,$param)
    {
        $item = $param->Item;
		
        // implement doSomeValidation() if you need to check the modified data before you commit the changes
        $rujukan = $item->rujukan->DropDownList->SelectedValue;
		$kelas = $item->kelas->DropDownList->SelectedValue;
        if ($this->User->IsAdmin)
		{
			
            // i'm using here TActiveRecord for simplicity
            //$oSomeData = SomeDataList::getSomeData('SomeData',$this->dtgSomeData->DataKeys[$item->ItemIndex]);
			$oSomeData = RwtInapRecord::finder()->findByPk($this->dtgSomeData->DataKeys[$item->ItemIndex]);
                        
            // do some changes to your database item/object and then save it
            $oSomeData->st_rujukan = $rujukan;
			$oSomeData->kelas = $kelas;
            
			$notrans=$this->dtgSomeData->DataKeys[$item->ItemIndex];
			$sql="SELECT * FROM tbt_inap_kamar WHERE  no_trans_inap='$notrans' ORDER BY id DESC";
			//$kmrInap=InapKamarRecord::finder()->find('no_trans_inap=? AND st_rubah=?',$this->dtgSomeData->DataKeys[$item->ItemIndex],'0');
			$kmrInap=InapKamarRecord::finder()->findBySql($sql);
			$kmrInap->tgl_kmr_ubah=date('Y-m-d');
			$kmrInap->wkt_rubah=date('G:i:s');
			$kmrInap->id_kmr_ubah=$kelas;
			
			$tglMasuk= $kmrInap->tgl_awal;
			$tglKeluar= date('Y-m-d');	
			$wktMasuk= $kmrInap->wkt_masuk;
			$wktKeluar= date('G:i:s');		
			$lamaInap = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
			$kmrInap->lama_inap=$lamaInap;
			$kmrInap->tgl_keluar=$tglKeluar;
			$kmrInap->wkt_keluar=$wktKeluar;
			
			$kamar=new InapKamarRecord();
			$kamar->cm=$kmrInap->cm;
			$kamar->no_trans_inap=$kmrInap->no_trans_inap;
			$kamar->tgl_awal=date('Y-m-d');
			$kamar->wkt_masuk=date('G:i:s');
			$kamar->id_kmr_awal=$kelas;
			$kamar->id_kmr_skrg=$kelas;
			
			if ($kmrInap->tgl_awal != date('Y-m-d'))
			{
				$kamar->save();
				$oSomeData->save();           
				$kmrInap->save();
				
				//UPDATE kelas di tbt_rawat_inap
				$sql="SELECT * FROM tbt_rawat_inap WHERE  no_trans='$notrans' AND status='0'";
				$RwtInap = RwtInapRecord::finder()->findBySql($sql);
				$RwtInap->kelas = $kelas;
				$RwtInap->save();
				
			}
            // clear data in session because we need to refresh it from db
            // you could also modify the data in session and not clear the data from session!
            $session = $this->getSession();
            $session->remove("SomeData");        
        }

        $this->dtgSomeData->EditItemIndex=-1;
        $this->bindGrid();
    }

    protected function dtgSomeData_CancelCommand($sender,$param)
    {
        $this->dtgSomeData->EditItemIndex=-1;
        $this->bindGrid();
    }
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Home'));		
	}
		
	public function cariClicked($sender,$param)
	{		
		$session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
		
		if($this->CBpdf->Checked==false)
		{				
			
			if($this->cariNama->Text){
				$this->setViewState('cariByNama', $this->cariNama->Text);
			}else{
				$this->clearViewState('cariByNama');	
			}
			
			if($this->cariAlamat->Text){
				$this->setViewState('cariByAlamat', $this->cariAlamat->Text);
			}else{
				$this->clearViewState('cariByAlamat');	
			}
				
			if($this->cariCM->Text){ 
				$this->setViewState('cariByCM', $this->cariCM->Text);	
			}else{
				$this->clearViewState('cariByCM');	
			}
			
			if($this->Advance->Checked){
				$this->setViewState('elemenBy',$this->Advance->Checked);	
			}else{
				$this->clearViewState('elemenBy');	
			}
			
			if($this->tglMsk->Text){
				$this->setViewState('cariByTgl', $this->tglMsk->Text);
			}else{
				$this->clearViewState('cariByTgl');	
			}
			
			if($this->DDBulan->SelectedValue){ 		
				$this->setViewState('cariByBulan', $this->DDBulan->SelectedValue);	
			}else{
				$this->clearViewState('cariByBulan');	
			}
			
			if($this->DDKelas->SelectedValue){ 		
				$this->setViewState('cariByKelas', $this->DDKelas->SelectedValue);	
			}else{
				$this->clearViewState('cariByKelas');	
			}			
			
			$this->bindGrid();			
			
		}
		else
		{
			$cariCM=$this->cariCM->Text;
			$cariNama=$this->cariNama->Text;
			$tipeCari=$this->Advance->Checked;
			$cariAlamat=$this->cariAlamat->Text;
			$urutBy=$this->DDUrut->SelectedValue;
			$Company=$this->DDKontrak->SelectedValue;
			$cariTgl=$this->tglMsk->Text;
			$cariBln=$this->DDBulan->SelectedValue;
			$cariByKelas=$this->DDKelas->SelectedValue;
			$cariByKamar=$this->DDKamar->SelectedValue;
			
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarCariDataPdf',array('cariCM'=>$cariCM,'cariNama'=>$cariNama,'tipeCari'=>$tipeCari,'cariAlamat'=>$cariAlamat,'urutBy'=>$urutBy,'Company'=>$Company)));
		}
	}
	
	public function selectionChangedUrut($sender,$param)
	{
		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='a.cm';
		}	
		
		if($this->cariNama->Text){
			$this->setViewState('cariByNama', $this->cariNama->Text);
		}else{
			$this->clearViewState('cariByNama');	
		}
		
		if($this->cariAlamat->Text){
			$this->setViewState('cariByAlamat', $this->cariAlamat->Text);
		}else{
			$this->clearViewState('cariByAlamat');	
		}
			
		if($this->cariCM->Text){ 
			$this->setViewState('cariByCM', $this->cariCM->Text);	
		}else{
			$this->clearViewState('cariByCM');	
		}	
		
		if($this->Advance->Checked) {
			$this->setViewState('elemenBy',$this->Advance->Checked);
		}else{
			$this->clearViewState('elemenBy');	
		}
		
		if($this->DDKontrak->SelectedValue){ 		
			$this->setViewState('companyBy', $this->DDKontrak->SelectedValue);
		}else{
			$this->clearViewState('companyBy');	
		}
				
		if($this->DDUrut->SelectedValue){
			$this->setViewState('urutBy', $this->DDUrut->SelectedValue);
		}else{
			$this->clearViewState('urutBy');	
		}
			
		if($this->DDUrut->SelectedValue == '05')//Kalo pilihannya adalah pasien kontrak	
		{	
			$this->DDKontrak->Enabled=true;	
		}else{
			$this->DDKontrak->Enabled=false;	
		}
		
		if($this->tglMsk->Text){
			$this->setViewState('cariByTgl', $this->tglMsk->Text);
		}else{
			$this->clearViewState('cariByTgl');	
		}
		
		if($this->DDBulan->SelectedValue){ 		
			$this->setViewState('cariByBulan', $this->DDBulan->SelectedValue);	
		}else{
			$this->clearViewState('cariByBulan');	
		}
		
		if($this->DDKelas->SelectedValue){ 		
			$this->setViewState('cariByKelas', $this->DDKelas->SelectedValue);	
		}else{
			$this->clearViewState('cariByKelas');	
		}
		
		if($this->DDKamar->SelectedValue){ 		
			$this->setViewState('cariByKamar', $this->DDKamar->SelectedValue);	
		}else{
			$this->clearViewState('cariByKamar');	
		}

		$this->bindGrid();			
	
	}

	public function DDKontrakChanged($sender,$param)
	{				
		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='a.cm';
		}	
		
		if($this->cariNama->Text){
			$this->setViewState('cariByNama', $this->cariNama->Text);
		}else{
			$this->clearViewState('cariByNama');	
		}
		
		if($this->cariAlamat->Text){
			$this->setViewState('cariByAlamat', $this->cariAlamat->Text);
		}else{
			$this->clearViewState('cariByAlamat');	
		}
			
		if($this->cariCM->Text){ 
			$this->setViewState('cariByCM', $this->cariCM->Text);	
		}else{
			$this->clearViewState('cariByCM');	
		}	
		
		if($this->Advance->Checked) {
			$this->setViewState('elemenBy',$this->Advance->Checked);
		}else{
			$this->clearViewState('elemenBy');	
		}
		
		if($this->DDKontrak->SelectedValue){ 		
			$this->setViewState('companyBy', $this->DDKontrak->SelectedValue);
		}else{
			$this->clearViewState('companyBy');	
		}
				
		if($this->DDUrut->SelectedValue){
			$this->setViewState('urutBy', $this->DDUrut->SelectedValue);
		}else{
			$this->clearViewState('urutBy');	
		}
			
		if($this->DDUrut->SelectedValue == '05')//Kalo pilihannya adalah pasien kontrak	
		{	
			$this->DDKontrak->Enabled=true;	
		}else{
			$this->DDKontrak->Enabled=false;	
		}
		
		if($this->tglMsk->Text){
			$this->setViewState('cariByTgl', $this->tglMsk->Text);
		}else{
			$this->clearViewState('cariByTgl');	
		}
		
		if($this->DDBulan->SelectedValue){ 		
			$this->setViewState('cariByBulan', $this->DDBulan->SelectedValue);	
		}else{
			$this->clearViewState('cariByBulan');	
		}
		
		if($this->DDKelas->SelectedValue){ 		
			$this->setViewState('cariByKelas', $this->DDKelas->SelectedValue);	
		}else{
			$this->clearViewState('cariByKelas');	
		}
		
		if($this->DDKamar->SelectedValue){ 		
			$this->setViewState('cariByKamar', $this->DDKamar->SelectedValue);	
		}else{
			$this->clearViewState('cariByKamar');	
		}

		$this->bindGrid();		
	}
}

/*
	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariNama,$cariCM,$urutBy,$tipeCari,$Company,$cariAlamat,$cariTgl,$cariBln,$cariByKelas,$cariByKamar)
	{
		if($pil == "1")
		{
			$sql = "SELECT a.cm,
						   a.nama, 
						   a.jkel,
						   f.nama AS alamat, 
						   h.nama AS kelompok,						   
						   b.id AS kontrakID,								
						   g.nama AS kabupaten
						   FROM tbd_pasien a,
						        tbm_kelompok b,						   		
								tbm_kabupaten c,						   	
								tbm_perusahaan d,
								tbt_rawat_inap e,
								tbm_ruang f,
								tbm_kelas_kamar g,
								tbd_pegawai h
						   WHERE
							    a.kelompok=b.id
								AND a.cm=e.cm
								AND e.status = '0'
								AND f.id=e.kamar
								AND g.id=e.kelas
								AND h.id=e.dokter
								AND a.kelompok=b.id									
								AND a.kabupaten=c.id ";
								
			if($cariNama <> '')			
				if($tipeCari === true){
					$sql .= "AND a.nama LIKE '%$cariNama%' ";
				}
				else
				{	
					$sql .= "AND a.nama LIKE '$cariNama%' ";
				}
						
			if($cariAlamat <> '')			
				if($tipeCari === true){
					$sql .= "AND a.alamat LIKE '%$cariAlamat%' ";
				}
				else
				{	
					$sql .= "AND a.alamat LIKE '$cariAlamat%' ";
				}
				
			if($cariCM <> '')
				$sql .= "AND a.cm = '$cariCM' ";
			
			if($cariByKelas <> '')			
				$sql .= "AND e.kelas = '$cariByKelas' ";
			
			if($cariByKamar <> '')			
				$sql .= "AND e.kamar = '$cariByKamar' ";		
			
			if($urutBy <> '')
				$sql .= "AND a.kelompok = '$urutBy' ";	
			
			if($Company <> '')
				$sql .= "AND a.perusahaan = '$Company' AND d.id_kel = '$urutBy'";
			
			if($cariTgl <> '')
			{
				$tgl = $this->ConvertDate($cariTgl,'2');//Convert date to mysql
				$sql .= "AND e.tgl_masuk = '$tgl' ";
			}	
			
			if($cariBln <> '')
			{
				$blnAkhir = $this->akhirBln($cariBln);
				$blnAwal = date('Y') . '-' . $cariBln . '-01';
				$sql .= "AND e.tgl_masuk BETWEEN '$blnAwal' AND '$blnAkhir' ";		
			}	
				
			$sql .= " GROUP BY a.cm ";
			if($order <> '')							
				$sql .= " ORDER BY a." . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
		}else{
			$sql = "SELECT a.cm,
						   a.nama, 
						   a.jkel,
						  f.nama AS alamat, 
						   h.nama AS kelompok, 						   
						   b.id AS kontrakID,						   
						   g.nama AS kabupaten
						   FROM tbd_pasien a,
								tbm_kelompok b,
								tbm_kabupaten c,
								tbm_perusahaan d,
								tbt_rawat_inap e,
								tbm_ruang f,
								tbm_kelas_kamar g,
								tbd_pegawai h 
						   WHERE
								a.kelompok=b.id
								AND a.cm=e.cm
								AND e.status = '0'
								AND f.id=e.kamar
								AND g.id=e.kelas
								AND h.id=e.dokter
								AND a.kelompok=b.id												
								AND a.kabupaten=c.id ";
			if($cariNama <> '')
							
				if($tipeCari === true){
					$sql .= "AND a.nama LIKE '%$cariNama%' ";
				}
				else
				{	
					$sql .= "AND a.nama LIKE '$cariNama%' ";
				}
			
			if($cariAlamat <> '')			
				if($tipeCari === true){
					$sql .= "AND a.alamat LIKE '%$cariAlamat%' ";
				}
				else
				{	
					$sql .= "AND a.alamat LIKE '$cariAlamat%' ";
				}
							
			if($cariCM <> '')			
				$sql .= "AND a.cm = '$cariCM' ";
			
			if($cariByKelas <> '')			
				$sql .= "AND e.kelas = '$cariByKelas' ";
			
			if($cariByKamar <> '')			
				$sql .= "AND e.kamar = '$cariByKamar' ";		
			
			if($cariTgl <> '')
			{
				$tgl = $this->ConvertDate($cariTgl,'2');//Convert date to mysql
				$sql .= "AND e.tgl_masuk = '$tgl' ";
			}	
			
			if($cariBln <> '')
			{
				$blnAkhir = $this->akhirBln($cariBln);
				$blnAwal = date('Y') . '-' . $cariBln . '-01';
				$sql .= "AND e.tgl_masuk BETWEEN '$blnAwal' AND '$blnAkhir' ";		
			}
			
			if($urutBy <> '')
				$sql .= "AND a.kelompok = '$urutBy' ";
					
			if($Company <> '')
				$sql .= "AND a.perusahaan = '$Company' AND d.id_kel = '$urutBy'";						
			
			$sql .= " GROUP BY a.cm ";	
			if($order <> '')			
				$sql .= " ORDER BY a." . $order . ' ' . $sort;

		}					 
		//$page=PasienRecord::finder()->findAllBySql($sql);
		$page = $sql;
		$this->showSql->Text=$sql;//show SQL Expression broer!
		$this->showSql->Visible=false;//disable SQL Expression broer!
		return $page;
		
	} 
		
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{					
			$orderBy=$this->getViewState('orderBy');		
			$cariByNama=$this->getViewState('cariByNama');
			$cariByCM=$this->getViewState('cariByCM');
			$urutBy=$this->getViewState('urutBy');
			$companyBy=$this->getViewState('companyBy');
			$elemenBy=$this->getViewState('elemenBy');
			$cariByAlamat=$this->getViewState('cariByAlamat');
			$cariByTgl=$this->getViewState('cariByTgl');
			$cariByBulan=$this->getViewState('cariByBulan');
			$cariByKelas=$this->getViewState('cariByKelas');
			$cariByKamar=$this->getViewState('cariByKamar');

			$this->DDKontrak->Enabled=false;
			$this->DDUrut->DataSource=KelompokRecord::finder()->findAll();
			$this->DDUrut->dataBind();
			$this->DDKontrak->DataSource=PerusahaanRecord::finder()->findAll();
			$this->DDKontrak->dataBind();
			$this->DDBulan->DataSource=BulanRecord::finder()->findAll();
			$this->DDBulan->dataBind();
			$this->DDKelas->DataSource=KelasKamarRecord::finder()->findAll();
			$this->DDKelas->dataBind();
			$this->DDKamar->DataSource=RuangRecord::finder()->findAll();
			$this->DDKamar->dataBind();

			//$this->UserGrid->VirtualItemCount=RwtInapRecord::finder()->count();
			
			$jmlData=count($this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->cariCM->Text,$this->DDUrut->SelectedValue,$this->Advance->Checked,$this->DDKontrak->SelectedValue,$this->cariAlamat->Text,$this->tglMsk->Text,$this->DDBulan->SelectedValue,$this->DDKelas->SelectedValue,$this->DDKamar->SelectedValue),'S'));
			
			$this->UserGrid->VirtualItemCount=$jmlData;
			$this->jmlDataPas->Text=$jmlData;
			
			// fetches all data account information 
			$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByCM,$urutBy,$elemenBy,$companyBy,$cariByAlamat,$cariByTgl,$cariByBulan,$cariByKelas,$cariByKamar),'S');
			
			// binds the data to interface components
			$this->UserGrid->dataBind();		
			$this->cariNama->focus();		
			
			$position='TopAndBottom';		
			$this->UserGrid->PagerStyle->Position=$position;
			$this->UserGrid->PagerStyle->Visible=true;
			
		}		
    }		
	
	
   
	
	
	public function changePage($sender,$param)
	{		
		$limit=$this->UserGrid->PageSize;
		$orderBy=$this->getViewState('orderBy');
		$cariByNama=$this->getViewState('cariByNama');
		$cariByCM=$this->getViewState('cariByCM');
		$urutBy=$this->getViewState('urutBy');
		$companyBy=$this->getViewState('companyBy');
		$elemenBy=$this->getViewState('elemenBy');
		$cariByAlamat=$this->getViewState('cariByAlamat');
		$cariByTgl=$this->getViewState('cariByTgl');
		$cariByBulan=$this->getViewState('cariByBulan');
		$cariByKelas=$this->getViewState('cariByKelas');
		$cariByKamar=$this->getViewState('cariByKamar');
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		//$offset,$rows,$order,$sort,$pil
		$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByCM,$urutBy,$elemenBy,$companyBy,$cariByAlamat,$cariByTgl,$cariByBulan,$cariByKelas,$cariByKamar),'S');
		$this->UserGrid->dataBind();		
	} 
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}	
	
	public function itemCreated($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='DeleteItem')
        {
            if($this->User->IsAdmin){
				// add an aleart dialog to delete buttons
				$item->Hapus->Button->Attributes->onclick='if(!confirm(\'Apakah anda yakin ingin menghapus data ini..?\')) return false;';		
			}else{
				$item->Hapus->Button->Visible='0';
			}	
        }
    }
	
	 public function deleteButtonClicked($sender,$param)
    {
        if ($this->User->IsAdmin)
		{
			// obtains the datagrid item that contains the clicked delete button
			$item=$param->Item;
			// obtains the primary key corresponding to the datagrid item
			$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
			// deletes the user record with the specified username primary key
			
			PasienRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarCariRwtInap'));
			
		}	
    }	
	
	public function useNumericPager($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');
		$cariByNama=$this->getViewState('cariByNama');
		$cariByCM=$this->getViewState('cariByCM');
		$urutBy=$this->getViewState('urutBy');
		$companyBy=$this->getViewState('companyBy');
		$elemenBy=$this->getViewState('elemenBy');
		$cariByAlamat=$this->getViewState('cariByAlamat');
		$cariByTgl=$this->getViewState('cariByTgl');
		$cariByBulan=$this->getViewState('cariByBulan');
		$cariByKelas=$this->getViewState('cariByKelas');
		$cariByKamar=$this->getViewState('cariByKamar');
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;		
		$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByCM,$urutBy,$elemenBy,$companyBy,$cariByAlamat,$cariByTgl,$cariByBulan,$cariByKelas,$cariByKamar),'S');
		$this->UserGrid->dataBind();
	}	
	
	public function changePageSize($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');
		$cariByNama=$this->getViewState('cariByNama');
		$cariByCM=$this->getViewState('cariByCM');
		$urutBy=$this->getViewState('urutBy');
		$companyBy=$this->getViewState('companyBy');
		$elemenBy=$this->getViewState('elemenBy');
		$cariByAlamat=$this->getViewState('cariByAlamat');
		$cariByTgl=$this->getViewState('cariByTgl');
		$cariByBulan=$this->getViewState('cariByBulan');
		$cariByKelas=$this->getViewState('cariByKelas');
		$cariByKamar=$this->getViewState('cariByKamar');
		$this->UserGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->UserGrid->CurrentPageIndex=0;
		$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByCM,$urutBy,$elemenBy,$companyBy,$cariByAlamat,$cariByTgl,$cariByBulan,$cariByKelas,$cariByKamar),'S');
		$this->UserGrid->dataBind();
	}
	
	
	public function sortGrid($sender,$param)
	{		
		$orderBy = $param->SortExpression;
		$this->setViewState('orderBy',$oderBy);			
		$cariByNama=$this->getViewState('cariByNama');
		$cariByCM=$this->getViewState('cariByCM');
		$urutBy=$this->getViewState('urutBy');
		$companyBy=$this->getViewState('companyBy');
		$elemenBy=$this->getViewState('elemenBy');
		$cariByAlamat=$this->getViewState('cariByAlamat');
		$cariByTgl=$this->getViewState('cariByTgl');
		$cariByBulan=$this->getViewState('cariByBulan');
		$cariByKelas=$this->getViewState('cariByKelas');
		$cariByKamar=$this->getViewState('cariByKamar');
	
		//$this->UserGrid->DataSource=PegawaiRecord::finder()->findAll($criteria);
		$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByCM,$urutBy,$elemenBy,$companyBy,$cariByAlamat,$cariByTgl,$cariByBulan,$cariByKelas,$cariByKamar),'S');	
		$this->UserGrid->dataBind();	
	}
	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Home'));		
	}
	
	
	public function cariClicked()
	{		
		if($this->CBpdf->Checked==false)
		{
			if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
			}else{
				$orderBy='cm';
			}		
			
			if($this->DDUrut->SelectedValue){ 		
				$this->setViewState('urutBy', $this->DDUrut->SelectedValue);
			}else{
				$this->clearViewState('urutBy');	
			}
				
			if($this->DDKontrak->SelectedValue){ 		
				$this->setViewState('companyBy', $this->DDKontrak->SelectedValue);	
			}else{
				$this->clearViewState('companyBy');	
			}
			
			if($this->cariNama->Text){
				$this->setViewState('cariByNama', $this->cariNama->Text);
			}else{
				$this->clearViewState('cariByNama');	
			}
			
			if($this->tglMsk->Text){
				$this->setViewState('cariByTgl', $this->tglMsk->Text);
			}else{
				$this->clearViewState('cariByTgl');	
			}
			
			if($this->DDBulan->SelectedValue){ 		
				$this->setViewState('cariByBulan', $this->DDBulan->SelectedValue);	
			}else{
				$this->clearViewState('cariByBulan');	
			}
			
			if($this->cariAlamat->Text){
				$this->setViewState('cariByAlamat', $this->cariAlamat->Text);
			}else{
				$this->clearViewState('cariByAlamat');	
			}
			
			if($this->DDKelas->SelectedValue){ 		
				$this->setViewState('cariByKelas', $this->DDKelas->SelectedValue);	
			}else{
				$this->clearViewState('cariByKelas');	
			}
			
			if($this->DDKamar->SelectedValue){ 		
				$this->setViewState('cariByKamar', $this->DDKamar->SelectedValue);	
			}else{
				$this->clearViewState('cariByKamar');	
			}
					
			if($this->cariCM->Text){ 
				$this->setViewState('cariByCM', $this->cariCM->Text);	
			}else{
				$this->clearViewState('cariByCM');	
			}
			
			if($this->Advance->Checked){
				$this->setViewState('elemenBy',$this->Advance->Checked);	
			}else{
				$this->clearViewState('elemenBy');	
			}
			
			$jmlData=count($this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->cariCM->Text,$this->DDUrut->SelectedValue,$this->Advance->Checked,$this->DDKontrak->SelectedValue,$this->cariAlamat->Text,$this->tglMsk->Text,$this->DDBulan->SelectedValue,$this->DDKelas->SelectedValue,$this->DDKamar->SelectedValue),'S'));
			
			$this->UserGrid->VirtualItemCount=$jmlData;
			
$this->jmlDataPas->Text=$jmlData;
			
			$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->cariCM->Text,$this->DDUrut->SelectedValue,$this->Advance->Checked,$this->DDKontrak->SelectedValue,$this->cariAlamat->Text,$this->tglMsk->Text,$this->DDBulan->SelectedValue,$this->DDKelas->SelectedValue,$this->DDKamar->SelectedValue),'S');
		
			$this->UserGrid->dataBind();
		}
		else
		{
			$cariCM=$this->cariCM->Text;
			$cariNama=$this->cariNama->Text;
			$tipeCari=$this->Advance->Checked;
			$cariAlamat=$this->cariAlamat->Text;
			$urutBy=$this->DDUrut->SelectedValue;
			$Company=$this->DDKontrak->SelectedValue;
			$cariTgl=$this->tglMsk->Text;
			$cariBln=$this->DDBulan->SelectedValue;
			$cariByKelas=$this->DDKelas->SelectedValue;
			$cariByKamar=$this->DDKamar->SelectedValue;
			
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarCariRwtInapPdf',array('cariCM'=>$cariCM,'cariNama'=>$cariNama,'tipeCari'=>$tipeCari,'cariAlamat'=>$cariAlamat,'urutBy'=>$urutBy,'Company'=>$Company,'cariTgl'=>$cariTgl,'cariBln'=>$cariBln,'cariByKelas'=>$cariByKelas,'cariByKamar'=>$cariByKamar)));
		}	
	}
	
	public function selectionChangedUrut($sender,$param)
	{				
		$this->cariClicked();
	}
}*/
?>
