<?php
class BayarPbfDraf extends SimakConf
{ 
	private $sortExp = "tgL_faktur";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 20;
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
		
	 }
	 	
	public function onPreRender($param)
	{
		parent::onPreRender($param);
		
		if(!$this->IsPostBack && !$this->IsCallBack)
		{
			//$this->tglawal->Text = date('d-m-Y');
			//$this->tglakhir->Text = date('d-m-Y');
			$this->tglBayar->Date = date('d-m-Y');
			
			$this->gridPanel->Display = "None";
			$this->cetakBtn->Enabled = false;
			
			$this->bindGridCariFaktur();
		}
		else
		{
			if ($this->getViewState('nmTable'))
			{
				$nmTable = $this->getViewState('nmTable');
				
				$this->gridPanel->Display = 'Dynamic';	
				
				$this->bindGridBrg();
				$this->cetakBtn->Enabled = true;
			}
			else
			{
				$this->gridPanel->Display = 'None';
				$this->cetakBtn->Enabled = false;
			}
		}	
		
		
	}
	
	public function onLoad($param)
	{	
		parent::onLoad($param);
		
	}
	
	public function prosesLock()
	{	
		$this->tglBayar->Enabled = false;
		$this->prosesBtn->Enabled = false;
	} 
	
	public function prosesUnlock()
	{	
		$this->tglBayar->Enabled = true;
		$this->prosesBtn->Enabled = true;
		$this->tglBayar->Date = date('d-m-Y');
		$this->cetakBtn->Enabled = false;
		$this->gridPanel->Display = 'None';
		
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			$this->clearViewState('nmTable');
		}
		
			
	} 
	
	public function checkNoFaktur($noFaktur)
	{
		//$tglBayar = $this->convertDate($tglBayar,'2');
		//$sql = "SELECT * FROM tbt_obat_masuk WHERE tgl_jth_tempo <= '$tglBayar' AND st_keuangan='0'";	
		
		$sql = "SELECT * FROM tbt_obat_masuk WHERE no_faktur = '$noFaktur' AND st_keuangan='0'";	
		$arr = $this->queryAction($sql,'S');
		$jmlData = count($arr);
		if($jmlData>0)
		{
			$sql = "SELECT * FROM tbt_bayar_pbf WHERE no_faktur = '$noFaktur'";	
			$arr = $this->queryAction($sql,'S');
			$jmlData = count($arr);
			if($jmlData>0)
			{
				$hasil = false;
			}
			else
			{
				$hasil = true;
			}
			
		}
		else
		{
			$hasil = false;
		}
		
		return $hasil;
	}
	
	public function checkNoFakturTmp($noFaktur)
	{
		$nmTable = $this->getViewState('nmTable');	
		$sql = "SELECT * FROM $nmTable WHERE no_faktur = '$noFaktur'";	
		$arr = $this->queryAction($sql,'S');
		$jmlData = count($arr);
		if($jmlData > 0)
		{
			$hasil = true;
		}
		else
		{
			$hasil = false;
		}
		
		return $hasil;
	}
	
	public function prosesClicked()
	{	
		if($this->IsValid)
        { 
			$session = $this->getSession();
			$session->remove("SomeData");    	
			$this->gridCariFaktur->CurrentPageIndex = 0;
			$this->bindGridCariFaktur();
			
			/*
			if($this->checkNoFaktur($this->noFaktur->Text)==true)
			{	
				$this->makeTblTemp();			
				
				$session = $this->getSession();
				$session->remove("SomeData");    	
				$this->gridBrg->CurrentPageIndex = 0;
				$this->bindGridBrg();
				$this->noFaktur->Text = '';
				$this->modeBayar->SelectedValue= '0';
			}
			else
			{
				$this->msg->Text = '   
				<script type="text/javascript">
					alert("No. Faktur : '.$this->noFaktur->Text.' tidak ditemukan.\nHarap cek ulang No. Faktur yang dimasukan.");
				</script>';	
				$this->msg->Text = '';				
			}
			*/
			
		}
	} 
	
	public function insertTmpTable($noFaktur,$modeBayar)
	{
		$nmTable = $this->getViewState('nmTable');	
		
		$sql = "SELECT * FROM tbt_obat_masuk WHERE no_faktur = '$noFaktur' AND st_keuangan='0'";	
		$arr = $this->queryAction($sql,'S');
	
		foreach($arr as $row)
		{
			$id_obat = $row['id_obat'];
			$jumlah = $row['jumlah'];
			$no_po = $row['no_po'];
			$no_faktur = $row['no_faktur'];
			$tgl_faktur = $row['tgl_faktur'];
			$tgl_jth_tempo = $row['tgl_jth_tempo'];
			$tgl_terima = $row['tgl_terima'];
			$discount = $row['discount'];
			$hrg_nett = $row['hrg_nett'];
			$hrg_disc = $row['hrg_disc'];
			$hrg_ppn = $row['hrg_ppn'];
			$hrg_ppn_disc = $row['hrg_ppn_disc'];
			$st_tunda = $modeBayar;
			
			$sql="INSERT INTO $nmTable (
					id_obat,
					jumlah,
					no_po,
					no_faktur,						
					tgl_faktur,
					tgl_jth_tempo,
					tgl_terima,
					discount,
					hrg_nett,
					hrg_disc,
					hrg_ppn,
					hrg_ppn_disc,
					st_tunda) 
				VALUES (
					'$id_obat',
					'$jumlah',
					'$no_po',
					'$no_faktur',
					'$tgl_faktur',
					'$tgl_jth_tempo',
					'$tgl_terima',
					'$discount',
					'$hrg_nett',
					'$hrg_disc',
					'$hrg_ppn',
					'$hrg_ppn_disc',
					'$st_tunda')";
			$this->queryAction($sql,'C');//Insert new row in tabel bro...
		}
	}
	
	public function makeTblTemp($noFaktur,$modeBayar)
	{	
		//$noFaktur = $this->noFaktur->Text;
		
		if (!$this->getViewState('nmTable'))
		{
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (
					id INT (2) auto_increment,
					id_obat varchar(5) NOT NULL,
					jumlah int(11) NOT NULL,
					no_po varchar(100) NOT NULL,
					no_faktur varchar(100) NOT NULL,
					tgl_faktur date NOT NULL,
					tgl_jth_tempo date NOT NULL,
					tgl_terima date NOT NULL,
					discount varchar(4) NOT NULL,
					hrg_nett float DEFAULT '0',
					hrg_disc float DEFAULT '0',
					hrg_ppn float DEFAULT '0',
					hrg_ppn_disc float DEFAULT '0',
					st_tunda char(1) DEFAULT '0',
					PRIMARY KEY (id)
				) ENGINE = MEMORY";
				
			$this->queryAction($sql,'C');//Create new tabel bro...
			
			$this->insertTmpTable($noFaktur,$modeBayar);
		
		}
		else
		{
			//Cek apakah no faktur sudah masuk sebelumnya
			if($this->checkNoFakturTmp($noFaktur)==true)
			{	
				$this->msg->Text = '   
				<script type="text/javascript">
					alert("No. Faktur : '.$noFaktur.' sudah dimasukan sebelumnya.");
				</script>';	
				$this->msg->Text = '';
				
			}
			else
			{
				$this->insertTmpTable($noFaktur,$modeBayar);
			}
		}
		
		$this->msg->Text .= ' '.$nmTable;
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
	
	private function bindGridCariFaktur()
    {
        $this->pageSize = $this->gridCariFaktur->PageSize;
		
		/*
		if($this->getViewState('offsetEdit')!='')
		{
			$this->gridCariFaktur->CurrentPageIndex = $this->getViewState('offsetEdit') - 1;
			$this->offset = $this->pageSize * $this->gridCariFaktur->CurrentPageIndex;		
			
			$this->clearViewState('offsetEdit');
		}
		else
		{
			 $this->offset = $this->pageSize * $this->gridCariFaktur->CurrentPageIndex;
		}
		*/
		
        $this->offset = $this->pageSize * $this->gridCariFaktur->CurrentPageIndex;
        
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {			
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			//$sql = "SELECT * FROM tbt_obat_masuk WHERE no_faktur = '$noFaktur' AND st_keuangan='0'";	
			//$sql = "SELECT * FROM tbt_obat_masuk WHERE st_keuangan='0'";	
			
			$sql="SELECT 
					tbm_pbf_obat.nama AS nm_supplier, tbt_obat_masuk.* 
				  FROM 
				  	tbt_obat_masuk
				  INNER JOIN 
				  	tbt_obat_beli ON (tbt_obat_masuk.no_po = tbt_obat_beli.no_po)
				  INNER JOIN 
				  	tbm_pbf_obat ON (tbt_obat_beli.pbf = tbm_pbf_obat.id)
				  ";
					 
			if($this->tglawal->Text != '' && $this->tglakhir->Text != '')
			{
				$cariTglAwal=$this->ConvertDate($this->tglawal->Text,'2');
				$cariTglAkhir=$this->ConvertDate($this->tglakhir->Text,'2');
				$sql .= " AND tbt_obat_masuk.tgl_jth_tempo BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
				
				//$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tglawal->Text,'2'),'3').' s.d. '.$this->ConvertDate($this->ConvertDate($this->tglakhir->Text,'2'),'3');
			}
			
			$sql .= " GROUP BY tbt_obat_masuk.no_faktur ";
			
			$this->setViewState('sql',$sql);
			
            $data = $someDataList->getSomeDataPage($sql);
            $this->gridCariFaktur->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			//$this->msg->Text=$someDataList->getSomeDataCount($sql).' Barang';    			
            $this->gridCariFaktur->DataSource = $data;
            $this->gridCariFaktur->DataBind();
	    	
			//$this->msg->Text=$sql;
			/*
			if($someDataList->getSomeDataCount($sql) > 0)
			{
				$this->cetakBtn->Enabled = true;
        	}
			else
			{
				$this->cetakBtn->Enabled = false;
			}
			*/
		}
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->gridCariFaktur->DataSource = $session["SomeData"];
            $this->gridCariFaktur->DataBind();
			
			$this->clearViewState('sql');
        }
    }
	
	protected function gridCariFaktur_PageIndexChanged($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData"); 
		$this->gridCariFaktur->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGridCariFaktur();
    }

    protected function gridCariFaktur_SortCommand($sender,$param)
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
        $session->remove("SomeData"); $this->gridCariFaktur->CurrentPageIndex = 0;
        $this->bindGridCariFaktur();

    }	
	
	protected function gridCariFaktur_ItemCreated($sender,$param)
    {
        $item=$param->Item;	
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' ||$item->ItemType==='EditItem')
        {
			$noFaktur = $item->DataItem['no_faktur'];
			$tglFaktur = $item->DataItem['tgl_faktur'];
			$tglJthTempo = $item->DataItem['tgl_jth_tempo'];
			$st_tunda = $item->DataItem['st_tunda'];
			
			$tgl_faktur = explode('-',$tglFaktur );
			$tgl_jth_tempo = explode('-',$tglJthTempo);
			
			$sql = "SELECT * FROM tbt_obat_masuk WHERE no_faktur='$noFaktur'";
			$arr = $this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				if($row['disc'] > 0) //jika ada disc, pake hrg_ppn_disc
				{
					$totHrg += $row['jumlah'] * $row['hrg_ppn_disc'];
				}
				else //pake hrg_ppn
				{
					$totHrg += $row['jumlah'] * $row['hrg_ppn'];
				}
			}
			
			$item->fakturColumn1->Text = $noFaktur;
			$item->fakturColumn2->Text = $tgl_faktur['2'].'-'.$tgl_faktur['1'].'-'.$tgl_faktur['0'];
			$item->fakturColumn3->Text = $tgl_jth_tempo['2'].'-'.$tgl_jth_tempo['1'].'-'.$tgl_jth_tempo['0'];
			$item->fakturColumn4->Text = $item->DataItem['nm_supplier'];
			$item->fakturColumn5->Text = number_format($totHrg,'2',',','.');
			$item->fakturColumn6->modeBayar->SelectedValue = '0';
        } 
    }
	
	protected function gridCariFaktur_EditCommand($sender,$param)
    { 
		$this->gridCariFaktur->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGridCariFaktur();
    }
	
	protected function gridCariFaktur_CancelCommand($sender,$param)
    { 
		$this->gridCariFaktur->EditItemIndex=-1;
        $this->bindGridCariFaktur();
    }
	
	protected function gridCariFaktur_UpdateCommand($sender,$param)
    {
        $item = $param->Item; 
		$this->gridCariFaktur->EditItemIndex=-1;
        $this->bindGridCariFaktur();
    }
	
	public function noFaktur($sender,$param)
	{
		$ID = $param->CommandParameter;
	}
	
	public function gridCariFaktur_itemCommand($sender,$param)
	{
		$item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' ||$item->ItemType==='EditItem')
        {
			$ID = $this->gridCariFaktur->DataKeys[$item->ItemIndex];
			$noFaktur = ObatMasukRecord::finder()->findByPk($ID)->no_faktur;
			$modeBayar = $item->fakturColumn6->modeBayar->SelectedValue;
			
			$this->msg->Text = $item->fakturColumn6->modeBayar->SelectedValue.' - '.$noFaktur;
			
			$this->makeTblTemp($noFaktur,$modeBayar);
		}
		
		$this->pageSize = $this->gridCariFaktur->PageSize;
		$this->offset = $this->pageSize * $this->gridCariFaktur->CurrentPageIndex;		
		
		$this->bindGridCariFaktur();
	}
	
	public function pengajuanBtnClicked($sender,$param)
	{	
		//$page = $this->gridCariFaktur->CurrentPageIndex+1;
		//$item = $param->Item;
		//$this->datagrid->EditItemIndex=$item->ItemIndex;
		//$this->datagrid->DataSource=$this->tariff->getAll($page);
		//$this->datagrid->dataBind();
		
		//$item = $this->gridCariFaktur->Item;
		
		$no_faktur = $param->CommandParameter;
		
		
		
		foreach ($this->gridCariFaktur->Items as $i) {	
			$id = $i->ItemIndex;
			
			if($id == '0')
			{
				$ck = $i->fakturColumn6->modeBayar->SelectedValue;
				$this->msg->Text = 	$ck; 	
			}
			
			
			
			//$this->msg->Text = 	$i->ItemIndex; 	
			//$ch = $this->getViewState($this->pageName . ".checkBoxes");
			//$ch[$id] = $ck;
			//$this->setViewState($this->pageName . ".checkBoxes", $ch);
			//$this->msg->Text = 	$this->gridCariFaktur->EditItem->modeBayar->SelectedValue; 
			
		}
		
		
		$this->msg->Text = 	$no_faktur; 	
		
		//$this->gridCariFaktur->modeBayar->SelectedValue;
		
		//$this->makeTblTemp($noFaktur);
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	private function bindGridBrg()
    {
        $this->pageSize = $this->gridBrg->PageSize;
        $this->offset = $this->pageSize * $this->gridBrg->CurrentPageIndex;
        
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {
			$nmTable = $this->getViewState('nmTable');
			
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			$sql="SELECT tbm_pbf_obat.nama AS nm_supplier, $nmTable.* FROM $nmTable
					 INNER JOIN tbt_obat_beli ON ($nmTable.no_po = tbt_obat_beli.no_po)
					 INNER JOIN tbm_pbf_obat ON (tbt_obat_beli.pbf = tbm_pbf_obat.id)
					 GROUP BY $nmTable.no_faktur ";
			
			$this->setViewState('sql',$sql);
            $data = $someDataList->getSomeDataPage($sql);
            $this->gridBrg->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			//$this->msg->Text=$someDataList->getSomeDataCount($sql).' Barang';    			
            $this->gridBrg->DataSource = $data;
            $this->gridBrg->DataBind();
	    	//$this->test->Text=$sql;
			/*
			if($someDataList->getSomeDataCount($sql) > 0)
			{
				$this->cetakBtn->Enabled = true;
        	}
			else
			{
				$this->cetakBtn->Enabled = false;
			}
			*/
		}
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->gridBrg->DataSource = $session["SomeData"];
            $this->gridBrg->DataBind();
			
			$this->clearViewState('sql');
        }
    }
	
	protected function gridBrg_PageIndexChanged($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData"); 
		$this->gridBrg->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGridBrg();
    }

    protected function gridBrg_SortCommand($sender,$param)
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
        $session->remove("SomeData"); $this->gridBrg->CurrentPageIndex = 0;
        $this->bindGridBrg();

    }	
	
	protected function gridBrg_ItemCreated($sender,$param)
    {
        $item=$param->Item;	
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' ||$item->ItemType==='EditItem')
        {
			$noFaktur = $item->DataItem['no_faktur'];
			$tglFaktur = $item->DataItem['tgl_faktur'];
			$tglJthTempo = $item->DataItem['tgl_jth_tempo'];
			$st_tunda = $item->DataItem['st_tunda'];
			
			$tgl_faktur = explode('-',$tglFaktur );
			$tgl_jth_tempo = explode('-',$tglJthTempo);
			
			$sql = "SELECT * FROM tbt_obat_masuk WHERE no_faktur='$noFaktur'";
			$arr = $this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				if($row['disc'] > 0) //jika ada disc, pake hrg_ppn_disc
				{
					$totHrg += $row['jumlah'] * $row['hrg_ppn_disc'];
				}
				else //pake hrg_ppn
				{
					$totHrg += $row['jumlah'] * $row['hrg_ppn'];
				}
			}
			
			$item->column1->Text = $noFaktur;
			$item->column2->Text = $tgl_faktur['2'].'-'.$tgl_faktur['1'].'-'.$tgl_faktur['0'];
			$item->column3->Text = $tgl_jth_tempo['2'].'-'.$tgl_jth_tempo['1'].'-'.$tgl_jth_tempo['0'];
			$item->column4->Text = $item->DataItem['nm_supplier'];
			$item->column5->Text = number_format($totHrg,'2',',','.');
			
			if($st_tunda=='0')
			{
				$item->column6->Text = 'Tidak';
			}
			else
			{
				$item->column6->Text = 'Ya';
			}
        } 
    }
	
	protected function gridBrg_EditCommand($sender,$param)
    { $this->gridBrg->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGridBrg();
    }
	
	protected function gridBrg_CancelCommand($sender,$param)
    { 
		$this->gridBrg->EditItemIndex=-1;
        $this->bindGridBrg();
    }
	
	protected function gridBrg_UpdateCommand($sender,$param)
    {
        $item = $param->Item; 
		$this->gridBrg->EditItemIndex=-1;
        $this->bindGridBrg();
    }
	
	public function gridEditBrgClicked($sender,$param)
	{
		$ID = $param->CommandParameter;
	}
	
	public function gridHapusBrgClicked($sender,$param)
	{
		$no_faktur = $param->CommandParameter;
		
		$nmTable = $this->getViewState('nmTable');
				
		$sql = "DELETE FROM $nmTable WHERE no_faktur='$no_faktur'";
		$this->queryAction($sql,'C');	
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arr = $this->queryAction($sql,'S');
		
		if(count($arr)==0)
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			$this->clearViewState('nmTable');
		}
		
		
	}
	
	public function cetakClicked($sender,$param)
    {
		$nmTable = $this->getViewState('nmTable');
		$notrans = $this->numCounter('tbt_bayar_pbf',BayarPbfRecord::finder(),'31');
		
		$sql = "SELECT 
					SUM(IF($nmTable.discount > 0, $nmTable.hrg_ppn_disc, $nmTable.hrg_ppn) * $nmTable.jumlah) AS total,
					$nmTable.* FROM $nmTable GROUP BY $nmTable.no_faktur ORDER BY $nmTable.id";	
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$no_faktur = $row['no_faktur'];
			$no_po = $row['no_po'];
			
			$st_tunda = $row['st_tunda'];
			
			$data= new BayarPbfRecord();
			
			$data->no_trans = $notrans;
			$data->no_po = $no_po;
			$data->no_faktur = $no_faktur;
			$data->total = $row['total'];
			$data->tgl_bayar = $this->convertDate($this->tglBayar->Text,'2');
			$data->tgl = date('Y-m-d');
			$data->wkt = date('G:i:s');
			$data->petugas = $this->User->IsUserNip;
			$data->st_tunda = $st_tunda;
			$data->Save();
			
			//Update st_keuangan di tbt_obat_masuk
			$sql="UPDATE tbt_obat_masuk SET st_keuangan='1' WHERE no_faktur='$no_faktur' ";
			$this->queryAction($sql,'C');
		}
			
		$this->queryAction($nmTable,'D');
		$this->clearViewState('nmTable');
		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.cetakBayarPbfDraf',array('noTrans'=>$notrans)));
	}
	
	public function batalClicked($sender,$param)
	{
		$this->prosesUnlock();
	}
	
	public function keluarClicked($sender,$param)
	{	
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			$this->clearViewState('nmTable');
		}	
			
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	}
?>
