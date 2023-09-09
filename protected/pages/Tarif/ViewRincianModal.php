<?php
class ViewRincianModal extends SimakConf
{
	private $sortExp = "tgl_visit";
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
	
	public function onPreRender($param)
	{
		parent::onPreRender($param);
		if(!$this->Page->IsPostBack && !$this->Page->IsCallBack)  
		{
			
			$cm = $this->Request['cm'];
			$noTrans = $this->Request['noTrans'];
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
			
			if($tipeRawat == '1') //rawat jalan
			{
				$sql = "SELECT * FROM tbt_rawat_jalan WHERE no_trans = '$noTrans'";
				$dataRawat = RwtjlnRecord::finder()->findBySql($sql);
				
				$idKlinik = 
			}
			elseif($tipeRawat == '2') //rawat inap
			{
				$sql = "SELECT * FROM tbt_rawat_inap WHERE no_trans = '$noTrans'";
				$dataRawat = RwtInapRecord::finder()->findBySql($sql);
			}
			
			$this->cm->Text = $cm;
			$this->noTrans->Text = $noTrans;
			$this->nama->Text = $data->nama;
			$this->jk->Text = $jk;
			$this->poliklinik->Text = $agama;
			$this->dokter->Text = $data->alamat;
			$this->penjamin->Text = $data->telp;
			
			$this->bindGrid();
			
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
	
	private function bindGrid()
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
			
			$cm = $this->getViewState('cm');
			$tipeRawat = $this->getViewState('tipeRawat');
			
			if($tipeRawat == '1')//Rawat Jalan
			{
				$this->historyPanel->GroupingText = '<strong>History Rawat Jalan</strong>';
				
				$sql = "SELECT 
						  tbt_rawat_jalan.cm,
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
						  tbt_rawat_jalan.cm = '$cm'	
						  AND tbt_rawat_jalan.flag = '1' ";	
			}
			else //Rawat Inap
			{
				$this->historyPanel->GroupingText = '<strong>History Rawat Inap</strong>';
				
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
						  tbt_rawat_inap.cm = '$cm'	
						  AND tbt_rawat_inap.status = '1' ";	
			}
			
			//$this->setViewState('sql',$sql);
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
				if($tipeRawat == '1')//Rawat Jalan
				{
					$this->msg->Text = 'Belum ada data rawat jalan untuk pasien ini.';
				}
				else //rawat inap
				{
					$this->msg->Text = 'Belum ada data rawat inap untuk pasien ini.';
				}
				//$this->pagerPanel->Display = 'None';
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
        $this->bindGrid();
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
        $this->bindGrid();

    }	
	
	protected function grid_ItemCreated($sender,$param)
    {
		$tipeRawat = $this->Request['tipeRawat'];
		
        $item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			$cm = $this->grid->DataKeys[$item->ItemIndex];
			$dataPas = PasienRecord::finder()->findByPk($cm);
			
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
			$item->diagnosaColumn->Text = $diag;
			$item->DokterColumn->Text = $nmDokter;
			$item->jaminanColumn->Text = $penjamin;
			$item->perusColumn->Text = $nmPerus;
			$item->stPulangColumn->Text = $stAlih;
			
			if($tipeRawat == '1')//Rawat Jalan
			{
			
			}
			else //rawat inap
			{
				$item->poliColumn->Text = '-';
			}
		
		}
    }
	
	protected function grid_EditCommand($sender,$param)
    { $this->grid->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGrid();
    }
	
	protected function grid_CancelCommand($sender,$param)
    { $this->grid->EditItemIndex=-1;
        $this->bindGrid();
    }
	
	protected function grid_UpdateCommand($sender,$param)
    {
        $item = $param->Item; $this->grid->EditItemIndex=-1;
        $this->bindGrid();
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
		
		$this->bindGrid();
	}
	
	public function kembaliClicked($sender,$param)
    {
		
		$this->msg->Text = '   
		<script type="text/javascript">
			window.parent.location="index.php?page=Tarif.LapClaimAsuransiDraf"; 
			opener.document.getElementById(\'loading\').setStyle({ display: \'none\' });
			window.close();
		</script>';	
		$this->msg->Text = '';
		
    }
	
}
?>