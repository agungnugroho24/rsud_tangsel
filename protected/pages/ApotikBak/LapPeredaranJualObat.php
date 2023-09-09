<?php
class LapPeredaranJualObat extends SimakConf
{  	
	private $sortExp = "nama";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 20;
	
	/**
	* General Public Variable.
	* Holds offset and limit data from array
	* @return array subset of values
	*/
		
	/**
	* Returns a subset of data.
	* In MySQL database, this can be replaced by LIMIT clause
	* in an SQL select statement.
	* @param integer the starting index of the row
	* @param integer number of rows to be returned
	* @return array subset of data
	*/
	
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
		if($this->getViewState('nmTable'))
		{
			$nmTable = $this->getViewState('nmTable');
			$this->queryAction($nmTable,'D');//Droped the table						
			$this->dtgSomeData->DataSource='';
			$this->dtgSomeData->dataBind();
			$this->clearViewState('nmTable');//Clear the view state	
		}
		
		
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
			
			$tglAwal = $this->convertDate($this->tglAwal->Text,'2');
			$tglAkhir = $this->convertDate($this->tglAkhir->Text,'2');
			$kateg = $this->collectSelectionResult($this->modeInput);
				
			if(!$this->getViewState('nmTable'))
			{
				$nmTable = $this->setNameTable('nmTable');
				$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
											 nama VARCHAR(30) NOT NULL,
											 id_obat VARCHAR(5) NOT NULL,	
											 id_harga VARCHAR(20) default NULL,									 
											 jumlah INT(11) NOT NULL,
											 tot_jml INT(11) NOT NULL,
											 tgl date NOT NULL,
											 persentase FLOAT(11,2) NOT NULL,							 							 
											 PRIMARY KEY (id)) ENGINE = MEMORY";
				
				$this->queryAction($sql,'C');//Create new tabel bro...
				
				
				/*
				$sql = "SELECT 
						  view_total_jml_jual_obat.id_obat AS id,
						  view_total_jml_jual_obat.nama,
						  view_total_jml_jual_obat.id_harga,
						  sum(view_total_jml_jual_obat.jumlah) AS jumlah,
						  (SELECT sum(view_total_jml_jual_obat.jumlah) AS jumlah FROM view_total_jml_jual_obat WHERE tgl BETWEEN '$tglAwal' AND '$tglAkhir' GROUP BY
						  view_total_jml_jual_obat.id_obat) AS tot_jml,
						  view_total_jml_jual_obat.tgl,
						  (sum(view_total_jml_jual_obat.jumlah) / (SELECT sum(view_total_jml_jual_obat.jumlah) AS jumlah FROM view_total_jml_jual_obat WHERE tgl BETWEEN '$tglAwal' AND '$tglAkhir' GROUP BY
						  view_total_jml_jual_obat.id_obat)) * 100 AS persentase
						FROM
						  view_total_jml_jual_obat
						WHERE
							tgl BETWEEN '$tglAwal' AND '$tglAkhir'
						  
						GROUP BY
						  view_total_jml_jual_obat.id_obat";
						  */
				
				$sql = "SELECT 
						  view_total_jml_jual_obat.id_obat AS id,
						  view_total_jml_jual_obat.nama,
						  view_total_jml_jual_obat.id_harga,
						  sum(view_total_jml_jual_obat.jumlah) AS jumlah,
						  view_total_jml_jual_obat.tgl
						FROM
						  view_total_jml_jual_obat
						WHERE
							tgl BETWEEN '$tglAwal' AND '$tglAkhir'
						GROUP BY
						  view_total_jml_jual_obat.id_obat"; 
										  
				$arr = $this->queryAction($sql,'S');
				if(count($arr > 0))
				{
					foreach($arr as $row)
					{
						$idObat = $row['id'];
						$nama = $row['nama'];
						$id_harga = $row['id_harga'];
						$jumlah = $row['jumlah'];
						$tgl = $row['tgl'];
						
						/*
						$sql = "SELECT 
									id_obat,SUM(jumlah) AS jml_stok,tgl_terima
								FROM
								  tbt_obat_masuk
								WHERE
									tgl_terima BETWEEN '$tglAwal' AND '$tglAkhir'
								GROUP BY
								  id_obat"; */
						
						$sql = "SELECT 
									id_obat,SUM(jumlah) AS jml_stok
								FROM
								  tbt_stok_lain
								WHERE
									id_obat = '$idObat'
								GROUP BY
								  id_obat";
								  		  
						$arr = $this->queryAction($sql,'S');
						
						 foreach($arr as $row)
						{
							$tot_jml = $row['jml_stok'] + $jumlah ;
						}
						$persentase = ($jumlah / $tot_jml) * 100;
						
						
						$sql="INSERT INTO $nmTable (id_obat,nama,id_harga,jumlah,tot_jml,tgl,persentase) VALUES ('$idObat','$nama','$id_harga','$jumlah','$tot_jml','$tgl','$persentase')";
						$this->queryAction($sql,'C');//Insert new row in tabel bro...
					}
				}		
			}
			
        	$sql = "SELECT 
				  id_obat ,
				  nama,
				  id_harga,
				  jumlah,
				  tot_jml,
				  tgl,
				  persentase
				FROM
				 $nmTable 
				WHERE
					tgl BETWEEN '$tglAwal' AND '$tglAkhir' ";
					
			if($kateg <> '')
			{
				if($kateg == '1') //slow moving
				{
					$sql .= " AND persentase <= 20 "; 
				}
				else
				{
					$sql .= " AND persentase >= 80 ";
				}
			}
					
			$sql .= "	GROUP BY
				  		id_obat";
			
			//$this->showSql->Text=$sql;  
			
			$data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();	
			
			$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tglAwal->Text,'2'),'3').' s.d. '.$this->ConvertDate($this->ConvertDate($this->tglAkhir->Text,'2'),'3');
			
			if($someDataList->getSomeDataCount($sql) > 0)
			{
				$this->cetakLapBtn->Enabled = true;
			}
			else
			{
				$this->cetakLapBtn->Enabled = false;
			}
			
		}
        else
		{
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
        
        
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
        $this->bindGrid();

    }	
	
	protected function dtgSomeData_ItemCreated($sender,$param)
    {
        $item=$param->Item;	
		 
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes
           	   
        } 
    }
	
	protected function dtgSomeData_EditCommand($sender,$param)
    {
        $this->dtgSomeData->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGrid();
    }
	
	protected function dtgSomeData_CancelCommand($sender,$param)
    {
        $this->dtgSomeData->EditItemIndex=-1;
        $this->bindGrid();
    }
	
	protected function dtgSomeData_UpdateCommand($sender,$param)
    {
        $item = $param->Item;
		/*
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
            $oSomeData->save();
            
            // clear data in session because we need to refresh it from db
            // you could also modify the data in session and not clear the data from session!
            $session = $this->getSession();
            $session->remove("SomeData");        
        }
		*/
        $this->dtgSomeData->EditItemIndex=-1;
        $this->bindGrid();
    }
		
	/**
     * Populates the datagrid with user lists.
     * This method is invoked by the framework when initializing the page
     * @param mixed event parameter
    */
    public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('3');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);			
		
		if(!$this->IsPostBack)
		{	
			$this->tglAwal->Date = date('d-m-Y');
			$this->tglAkhir->Date = date('d-m-Y');
		}
		
		$purge=$this->Request['purge'];
		
		if($purge =='1'	)
		{	
			$nmTable=$this->Request['table'];		
			if($nmTable)
			{		
				$this->queryAction($nmTable,'D');//Droped the table						
				$this->dtgSomeData->DataSource='';
				$this->dtgSomeData->dataBind();
				$this->clearViewState('nmTable');//Clear the view state				
			}
			
			$this->Response->redirect($this->Service->constructUrl('Apotik.LapPeredaranJualObat',array('goto'=>'1')));
	
		}	
		
		$goto=$this->Request['goto'];	
		
		if($goto == '1')
		{
			$this->tglAwal->Focus();
		}
    }
	
	public function modeInputChanged($sender,$param)
	{
		$this->cariClicked();
	}
	
	public function cariClicked()
	{	
		
			
		$session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
	}		
	
	public function cetakClicked()
	{	
		$table = $this->getViewState('nmTable');
		$tglAwal = $this->convertDate($this->tglAwal->Text,'2');
		$tglAkhir = $this->convertDate($this->tglAkhir->Text,'2');
		$kateg = $this->collectSelectionResult($this->modeInput);
		
		$this->Response->redirect($this->Service->constructUrl('Apotik.cetakPeredaranJualObat',array('table'=>$table,'tglAwal'=>$tglAwal,'tglAkhir'=>$tglAkhir,'kateg'=>$kateg)));
		
	}
	
	public function keluarClicked($sender,$param)
	{		
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
			$this->dtgSomeData->DataSource='';
			$this->dtgSomeData->dataBind();
			$this->clearViewState('nmTable');//Clear the view state	
		}
		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
}
?>
