<?php
class DataPasDetail extends SimakConf
{
	private $sortExp = "tgl_visit";
	private $sortExpObat = "nm_obat";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('0');
		$tmpVar2=$this->authApp('10');
		if($tmpVar == "False" && $tmpVar2 == "False")//Bila tidak ada hak utk modul aplikasi rekam medis & pendaftaran 
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }		  
	
	public function onPreRender($param)
	{
		parent::onPreRender($param);
		if(!$this->Page->IsPostBack && !$this->Page->IsCallBack)  
		{
			
			$cm = $this->Request['cm'];
			$tipeRawat = $this->Request['tipeRawat'];
			
			$this->setViewState('cm',$cm);
			$this->setViewState('tipeRawat',$tipeRawat);
			
			$sql = "SELECT * FROM tbd_pasien WHERE cm = '$cm'";
			$data = PasienRecord::finder()->findBySql($sql);
			
			$jk = $data->jkel;
			if($jk=='0')
			{
				$jk = 'Laki-Laki';	
			}
			else
			{
				$jk = 'Perempuan';	
			}
			
			$agama = $data->agama;
			if($agama=='0' || $agama=='')
			{
				$agama = '-';
			}
			else
			{
				$agama = AgamaRecord::finder()->findByPk($agama)->nama;
			}
			
			
			if($data->status == '1'){$status = 'Kawin';}
			elseif($data->status == '2'){$goldar = 'Belum Kawin';}
			elseif($data->status == '3'){$status = 'Duda';}
			elseif($data->status == '4'){$status = 'Janda';}
			
			$this->cm->Text = $cm;
			$this->nama->Text = $data->nama;
			$this->ttl->Text = $data->tmp_lahir.', '.$this->convertDate($data->tgl_lahir,'3');
			$this->jk->Text = $jk;
			$this->agama->Text = $agama;
			$this->alamat->Text = $data->alamat;
			$this->telp->Text = $data->telp;
			$this->hp->Text = $data->hp;
			
			$this->stKawin->Text = $status;
			$this->golDarah->Text = $data->goldar;
			$this->pekerjaan->Text = PekerjaanRecord::finder()->findByPk($data->pekerjaan)->nama;
			$this->pendidikan->Text = PendidikanRecord::finder()->findByPk($data->pendidikan)->nama;
			
			if($tipeRawat == '1')//Rawat Jalan
			{
				$this->bindGridJalan();
			}
			elseif($tipeRawat == '2') //rawat inap
			{
				$this->bindGridInap();
			}
			else
			{
				$this->bindGridJalan();
				$this->bindGridInap();
			}
			
			$this->bindGridObat();
		}
	}	
	
	public function onLoad($param)
	{
		if(!$this->IsPostBack && !$this->IsCallBack)  
		{						
				
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
	
	public function getSortExpressionObat() {
        if ($this->getViewState('SortExpressionObat',null)!==null) {
            return $this->getViewState('SortExpressionObat');
        }
        // set default in case there's no 'sortExpression' key in viewstate
        $this->setViewState('SortExpressionObat', $this->sortExpObat);
        return $this->sortExpObat;
    }

    public function setSortExpressionObat($sort) {
        $this->setViewState('SortExpressionObat',$sort);
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
	
	
	//------------------------------------- GRID RWT JALAN ------------------------------------------------- //
	private function bindGridJalan()
    {
	    $this->pageSize = $this->grid->PageSize;
        $this->offset = $this->pageSize * $this->grid->CurrentPageIndex;
        
        $session = $this->getSession();
		
		if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			$cm = $this->Request['cm'];
			$tipeRawat = $this->getViewState('tipeRawat');
			
			$this->historyPanelJalan->GroupingText = '<strong>History Rawat Jalan</strong>';
				
				$sql = "SELECT 
						  tbt_rawat_jalan.cm,
						  tbt_rawat_jalan.no_trans,
						  tbt_rawat_jalan.tgl_visit,
						  tbt_rawat_jalan.icd,
						  tbt_rawat_jalan.keluhan,
						  tbt_rawat_jalan.dokter,
						  tbt_rawat_jalan.st_asuransi,
						  tbt_rawat_jalan.penjamin,
						  tbt_rawat_jalan.st_alih,
						  tbd_pegawai.nama AS nm_dokter,
						  tbm_poliklinik.nama AS nm_poli,
						  tbm_kelompok.nama AS kel_penjamin,
						  tbm_perusahaan_asuransi.nama AS nm_perus,
						  tbm_icd.indonesia AS diagnosa
						FROM
						  tbt_rawat_jalan
						  INNER JOIN tbd_pegawai ON (tbt_rawat_jalan.dokter = tbd_pegawai.id)
						  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
						  INNER JOIN tbm_kelompok ON (tbt_rawat_jalan.penjamin = tbm_kelompok.id)
						  LEFT JOIN tbm_perusahaan_asuransi ON (tbt_rawat_jalan.perus_asuransi = tbm_perusahaan_asuransi.id)
						  LEFT JOIN tbm_icd ON (tbt_rawat_jalan.icd = tbm_icd.kode)
						WHERE
						  tbt_rawat_jalan.cm = '$cm' ";		
						  //AND tbt_rawat_jalan.flag = '0' ";	
			
			$this->setViewState('sqlJalan',$sql);
			//$this->msg->Text = $sql;
            $data = $someDataList->getSomeDataPage($sql);
            $this->grid->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			//$this->jmlData->Text=$someDataList->getSomeDataCount($sql).' produk';    			
            $this->grid->DataSource = $data;
            $this->grid->DataBind();
			
			if($someDataList->getSomeDataCount($sql) > 0)
			{
				//$this->pagerPanel->Display = 'Dynamic';
				$this->msg->Text = '';
        	}
			else
			{
				$this->msg->Text = 'Belum ada data rawat jalan untuk pasien ini.';
			}
			
		}
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->grid->DataSource = $session["SomeData"];
            $this->grid->DataBind();
			
			$this->clearViewState('sql');
        }
    }
	
	protected function grid_PageIndexChanged($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData"); $this->grid->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGridJalan();
    }

    protected function grid_SortCommand($sender,$param)
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
		
        $session = $this->getSession();
        $session->remove("SomeData"); $this->grid->CurrentPageIndex = 0;
        $this->bindGridJalan();

    }	
	
	protected function grid_ItemCreated($sender,$param)
    {
		$tipeRawat = $this->Request['tipeRawat'];
		
        $item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			$no_trans = $this->grid->DataKeys[$item->ItemIndex];
			
			$cm = RwtjlnRecord::finder()->findByPk($no_trans)->cm;
			$dataPas = PasienRecord::finder()->findByPk($cm);
			$this->setViewState('cm',$cm);
			
			$tglKunjung = $this->convertDate($item->DataItem['tgl_visit'],'3');
			$nmPoli = $item->DataItem['nm_poli'];
			$nmDokter = $item->DataItem['nm_dokter'];
			$diag = $item->DataItem['diagnosa'];
			$penjamin = $item->DataItem['kel_penjamin'];
			$nmPerus = $item->DataItem['nm_perus'];
			
			$stAlih = $item->DataItem['st_alih'];
			
			if($stAlih=='0')
			{
				$stAlih = 'PLG';
			}
			else
			{
				$stAlih = 'RWT';
			}
			
			$tglLahir = $dataPas->tgl_lahir;			
			$umur = $this->dateDifference($tglLahir,$item->DataItem['tgl_visit']);
			$umur = $umur['years'].' th '.$umur['months'].' bln '.$umur['days'].' hr';
			
			$item->tglVisitColumn->Text = $tglKunjung;
			$item->poliColumn->Text = $nmPoli;
			$item->umurColumn->Text = $umur;
			$item->kodeColumn->Text = $item->DataItem['icd'];
			$item->diagnosaColumn->Text = $diag;
			$item->DokterColumn->Text = $nmDokter;
			$item->jaminanColumn->Text = $penjamin;
			$item->perusColumn->Text = $nmPerus;
			$item->stPulangColumn->Text = $stAlih;
			
			$tmpVar = $this->authApp('2');
			if($tmpVar == "False" && !$this->User->IsAdmin)
				$this->grid->Columns[8]->Visible = false;
		}
    }
	
	protected function grid_EditCommand($sender,$param)
    { $this->grid->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGridJalan();
    }
	
	protected function grid_CancelCommand($sender,$param)
    { $this->grid->EditItemIndex=-1;
        $this->bindGridJalan();
    }
	
	protected function grid_UpdateCommand($sender,$param)
    {
        $item = $param->Item; $this->grid->EditItemIndex=-1;
        $this->bindGridJalan();
    }
	
	public function gridEditClicked($sender,$param)
	{
		$ID = $param->CommandParameter;
		
		$this->Response->redirect($this->Service->constructUrl('member.editMember',array('ID'=>$ID)));
		/*
		$this->Page->CallbackClient->focus($this->DDkateg);
		$this->DDkateg->SelectedValue = BiblioRecord::finder()->findByPk($ID)->id_kategori;
		$this->namaBrg->Text = BiblioRecord::finder()->findByPk($ID)->nama;
		$this->minStok->Text = BiblioRecord::finder()->findByPk($ID)->min_stok;
		$this->maxStok->Text = BiblioRecord::finder()->findByPk($ID)->max_stok;
		$this->setViewState('editBrg',$ID);
		*/
	}
	
	public function gridHapusClicked($sender,$param)
	{
		$ID = $param->CommandParameter;
		
		$sql = "DELETE FROM tbd_member WHERE id='$ID'";
		$this->queryAction($sql,'C');
		
		$this->bindGridJalan();
	}
	
	public function cetakKwtBtnClicked($sender,$param)
	{
		$noTrans = $sender->CommandParameter;	
		$cm = RwtjlnRecord::finder()->findByPk($noTrans)->cm;
		$nama = RwtjlnRecord::finder()->findByPk($noTrans)->penanggung_jawab;
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRwtJlnDiscount',array('notrans'=>$noTrans,'cm'=>$cm,'nama'=>$nama,'jnsPasien'=>'0','stUlang'=>'1','cito'=>'0')));
	}
	
	
	
	
	//------------------------------------- GRID RWT INAP ------------------------------------------------- //
	private function bindGridInap()
    {
	    $this->pageSize = $this->gridInap->PageSize;
        $this->offset = $this->pageSize * $this->gridInap->CurrentPageIndex;
        
        $session = $this->getSession();
		
		if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			$cm = $this->Request['cm'];
			$tipeRawat = $this->getViewState('tipeRawat');
			
			$this->historyPanelInap->GroupingText = '<strong>History Rawat Inap</strong>';
				
			$sql = "SELECT 
					  tbt_rawat_inap.cm,
					  tbt_rawat_inap.tgl_masuk AS tgl_visit,
					  tbt_rawat_inap.icd,
					  tbt_rawat_inap.dokter,
					  tbt_rawat_inap.st_asuransi,
					  tbt_rawat_inap.penjamin,
					  tbt_rawat_inap.st_alih,
					  tbd_pegawai.nama AS nm_dokter,
					  tbm_kelompok.nama AS kel_penjamin,
					  tbm_perusahaan_asuransi.nama AS nm_perus,
					  tbm_icd.indonesia AS diagnosa
					FROM
					  tbt_rawat_inap
					  INNER JOIN tbd_pegawai ON (tbt_rawat_inap.dokter = tbd_pegawai.id)
					  INNER JOIN tbm_kelompok ON (tbt_rawat_inap.penjamin = tbm_kelompok.id)
					  LEFT JOIN tbm_perusahaan_asuransi ON (tbt_rawat_inap.perus_asuransi = tbm_perusahaan_asuransi.id)
					  LEFT JOIN tbm_icd ON (tbt_rawat_inap.icd = tbm_icd.kode)
					WHERE
					  tbt_rawat_inap.cm = '$cm'	";
					  //AND tbt_rawat_inap.status = '1' ";	
			
			$this->setViewState('sqlInap',$sql);
			//$this->msgInap->Text = $sql;
            $data = $someDataList->getSomeDataPage($sql);
            $this->gridInap->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			//$this->jmlData->Text=$someDataList->getSomeDataCount($sql).' produk';    			
            $this->gridInap->DataSource = $data;
            $this->gridInap->DataBind();
			
			if($someDataList->getSomeDataCount($sql) > 0)
			{
				//$this->pagerPanel->Display = 'Dynamic';
				$this->msgInap->Text = '';
        	}
			else
			{
				$this->msgInap->Text = 'Belum ada data rawat jalan untuk pasien ini.';
			}
			
		}
        else {
            Prado::trace("db not called for datagridInap", "SomeApp");
            
            $this->gridInap->DataSource = $session["SomeData"];
            $this->gridInap->DataBind();
			
			$this->clearViewState('sql');
        }
    }
	
	protected function gridInap_PageIndexChanged($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData"); $this->gridInap->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGridInap();
    }

    protected function gridInap_SortCommand($sender,$param)
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
		
        $session = $this->getSession();
        $session->remove("SomeData"); $this->gridInap->CurrentPageIndex = 0;
        $this->bindGridInap();

    }	
	
	protected function gridInap_ItemCreated($sender,$param)
    {
		$tipeRawat = $this->Request['tipeRawat'];
		
        $item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			$cm = $this->gridInap->DataKeys[$item->ItemIndex];
			$dataPas = PasienRecord::finder()->findByPk($cm);
			$this->setViewState('cm',$cm);
			
			$tglKunjung = $this->convertDate($item->DataItem['tgl_visit'],'3');
			$nmPoli = $item->DataItem['nm_poli'];
			$nmDokter = $item->DataItem['nm_dokter'];
			$diag = $item->DataItem['diagnosa'];
			$penjamin = $item->DataItem['kel_penjamin'];
			$nmPerus = $item->DataItem['nm_perus'];
			
			$stAlih = $item->DataItem['st_alih'];
			
			if($stAlih=='0')
			{
				$stAlih = 'PLG';
			}
			else
			{
				$stAlih = 'RWT';
			}
			
			$tglLahir = $dataPas->tgl_lahir;			
			$umur = $this->dateDifference($tglLahir,$item->DataItem['tgl_visit']);
			$umur = $umur['years'].' th '.$umur['months'].' bln '.$umur['days'].' hr';
								
			
			$item->tglVisitInapColumn->Text = $tglKunjung;
			$item->umurInapColumn->Text = $umur;
			$item->diagnosaInapColumn->Text = $diag;
			$item->DokterInapColumn->Text = $nmDokter;
			$item->jaminanInapColumn->Text = $penjamin;
			$item->perusInapColumn->Text = $nmPerus;
			$item->stPulangInapColumn->Text = $stAlih;
			
			if($tipeRawat == '1')//Rawat Jalan
			{
			
			}
			else //rawat inap
			{
				//$item->poliInapColumn->Text = '-';
			}
		
		}
    }
	
	protected function gridInap_EditCommand($sender,$param)
    { $this->gridInap->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGridInap();
    }
	
	protected function gridInap_CancelCommand($sender,$param)
    { $this->gridInap->EditItemIndex=-1;
        $this->bindGridInap();
    }
	
	protected function gridInap_UpdateCommand($sender,$param)
    {
        $item = $param->Item; $this->gridInap->EditItemIndex=-1;
        $this->bindGridInap();
    }
	
	public function gridInapEditClicked($sender,$param)
	{
		$ID = $param->CommandParameter;
		
		$this->Response->redirect($this->Service->constructUrl('member.editMember',array('ID'=>$ID)));
		/*
		$this->Page->CallbackClient->focus($this->DDkateg);
		$this->DDkateg->SelectedValue = BiblioRecord::finder()->findByPk($ID)->id_kategori;
		$this->namaBrg->Text = BiblioRecord::finder()->findByPk($ID)->nama;
		$this->minStok->Text = BiblioRecord::finder()->findByPk($ID)->min_stok;
		$this->maxStok->Text = BiblioRecord::finder()->findByPk($ID)->max_stok;
		$this->setViewState('editBrg',$ID);
		*/
	}
	
	public function gridInapHapusClicked($sender,$param)
	{
		$ID = $param->CommandParameter;
		
		$sql = "DELETE FROM tbd_member WHERE id='$ID'";
		$this->queryAction($sql,'C');
		
		$this->bindGridInap();
	}
	
	
	//------------------------------------- GRID OBAT ------------------------------------------------- //
	private function bindGridObat()
    {
	    $this->pageSize = $this->gridObat->PageSize;
        $this->offset = $this->pageSize * $this->gridObat->CurrentPageIndex;
        
        $session = $this->getSession();
		
		if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpressionObat;
			
			$cm = $this->Request['cm'];
			
			$tipeRawat = $this->getViewState('tipeRawat');
			
			$this->historyPanelObat->GroupingText = '<strong>History Obat Pasien</strong>';
				
			$sql = "SELECT *
					FROM
					  history_obat_pasien
					WHERE
					  cm = '$cm' ";
					  //AND tbt_rawat_inap.status = '1' ";	
			
			if($tipeRawat == '1')//Rawat Jalan
			{
				$sql .= " AND tipe_pasien = '0' ";
			}
			elseif($tipeRawat == '2') //rawat inap
			{
				$sql .= " AND tipe_pasien = '1' ";
			}
			
			$this->setViewState('sqlObat',$sql);
			//$this->msgObat->Text = $sql;
            $data = $someDataList->getSomeDataPage($sql);
            $this->gridObat->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			//$this->jmlData->Text=$someDataList->getSomeDataCount($sql).' produk';    			
            $this->gridObat->DataSource = $data;
            $this->gridObat->DataBind();
			
			if($someDataList->getSomeDataCount($sql) > 0)
			{
				$this->historyPanelObat->Display = 'Dynamic';
				$this->msgObat->Text = '';
        	}
			else
			{
				$this->msgObat->Text = 'Belum ada data obat untuk pasien ini.';
			}
		}
        else {
            Prado::trace("db not called for datagridObat", "SomeApp");
            
            $this->gridObat->DataSource = $session["SomeData"];
            $this->gridObat->DataBind();
			
			$this->clearViewState('sql');
        }
    }
	
	protected function gridObat_PageIndexChanged($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData"); $this->gridObat->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGridObat();
    }

    protected function gridObat_SortCommand($sender,$param)
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
		
        $session = $this->getSession();
        $session->remove("SomeData"); $this->gridObat->CurrentPageIndex = 0;
        $this->bindGridObat();

    }	
	
	protected function gridObat_ItemCreated($sender,$param)
    {
		$tipeRawat = $this->Request['tipeRawat'];
		
        $item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			$cm = $this->gridObat->DataKeys[$item->ItemIndex];
			$dataPas = PasienRecord::finder()->findByPk($cm);
			//$this->setViewState('cm',$cm);
			
			//$item->tglObatColumn->Text = $this->convertDate($item->DataItem['tgl'],'3');
			$item->namaObatColumn->Text = $item->DataItem['nm_obat'];
			$item->jmlObatColumn->Text = $item->DataItem['jumlah'];
			$item->totalObatColumn->Text = number_format($item->DataItem['total'],'2',',','.');
		
		}
    }
	
	protected function gridObat_EditCommand($sender,$param)
    { $this->gridObat->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGridObat();
    }
	
	protected function gridObat_CancelCommand($sender,$param)
    { $this->gridObat->EditItemIndex=-1;
        $this->bindGridObat();
    }
	
	protected function gridObat_UpdateCommand($sender,$param)
    {
        $item = $param->Item; $this->gridObat->EditItemIndex=-1;
        $this->bindGridObat();
    }
	
	public function gridObatEditClicked($sender,$param)
	{
		$ID = $param->CommandParameter;
		
		$this->Response->redirect($this->Service->constructUrl('member.editMember',array('ID'=>$ID)));
		/*
		$this->Page->CallbackClient->focus($this->DDkateg);
		$this->DDkateg->SelectedValue = BiblioRecord::finder()->findByPk($ID)->id_kategori;
		$this->namaBrg->Text = BiblioRecord::finder()->findByPk($ID)->nama;
		$this->minStok->Text = BiblioRecord::finder()->findByPk($ID)->min_stok;
		$this->maxStok->Text = BiblioRecord::finder()->findByPk($ID)->max_stok;
		$this->setViewState('editBrg',$ID);
		*/
	}
	
	public function gridObatHapusClicked($sender,$param)
	{
		$ID = $param->CommandParameter;
		
		$sql = "DELETE FROM tbd_member WHERE id='$ID'";
		$this->queryAction($sql,'C');
		
		$this->bindGridObat();
	}
	
	
	public function cetakClicked($sender,$param)
    {
		$urut=$this->getViewState('cm');
		$tipeRawat=$this->getViewState('tipeRawat');
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKartuStatusPoli',array('cm'=>$urut,'mode'=>'1','tipeRawat'=>$tipeRawat)));
		
    }
	
	public function cetakHistoriClicked($sender,$param)
    {
		$urut=$this->getViewState('cm');
		$tipeRawat=$this->getViewState('tipeRawat');
		
		if($this->getViewState('sqlJalan'))
		{
			$session=new THttpSession;
			$session->open();
			$session['cetakHistoriPasienJalan'] = $this->getViewState('sqlJalan');
		}
		
		if($this->getViewState('sqlInap'))
		{		
			$session=new THttpSession;
			$session->open();
			$session['cetakHistoriPasienInap'] = $this->getViewState('sqlInap');
		}
		
		if($this->getViewState('sqlObat'))
		{		
			$session=new THttpSession;
			$session->open();
			$session['cetakHistoriObat'] = $this->getViewState('sqlObat');
		}
		
				
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.cetakHistoriPasien',array('cm'=>$urut,'tipeRawat'=>$tipeRawat)));
		
    }
    
    
	
}
?>
