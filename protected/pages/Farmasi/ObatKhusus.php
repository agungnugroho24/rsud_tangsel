<?php
class ObatKhusus extends SimakConf
{   
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
	
	
	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariNama,$cariID,$tipeCari,$cariGol,$cariKlas,$cariDerivat,$cariPbf,$cariProd,$cariSat)
	{
		if($pil == "1")
		{
			$sql = "SELECT 
					  tbm_obat_hrg_khusus.id AS id,	
					  tbm_obat_hrg_khusus.id_obat AS kode,
					  tbm_obat.nama AS nama,
					  tbm_obat_hrg_khusus.hrg_jual AS hrg
					FROM
					  tbm_obat_hrg_khusus
					  INNER JOIN tbm_obat ON (tbm_obat_hrg_khusus.id_obat = tbm_obat.kode)
					 ";
								
			if($cariNama <> '')			
				if($tipeCari === true){
						$sql .= "AND nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "AND nama LIKE '$cariNama%' ";
					}		
						
			if($cariID <> '')			
				$sql .= "AND kode = '$cariID' ";			
				
			//$sql .= " GROUP BY kode";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
		}else{
			$sql = "SELECT 
					  tbm_obat_hrg_khusus.id AS id,
					  tbm_obat_hrg_khusus.id_obat AS kode,
					  tbm_obat.nama AS nama,
					  tbm_obat_hrg_khusus.hrg_jual AS hrg
					FROM
					  tbm_obat_hrg_khusus
					  INNER JOIN tbm_obat ON (tbm_obat_hrg_khusus.id_obat = tbm_obat.kode)
					";
								
			if($cariNama <> '')			
				if($tipeCari === true){
						$sql .= "AND nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "AND nama LIKE '$cariNama%' ";
					}		
						
			if($cariID <> '')			
				$sql .= "AND kode = '$cariID' ";
				
			//$sql .= " GROUP BY kode";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
		}					 
		$page=$this->queryAction($sql,'S');
		
		$this->showSql->Text=$sql;
		$this->showSql->Visible=false;//show SQL Expression broer!
		return $page;		
		
	}	
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);				
							
			$orderBy=$this->getViewState('orderBy');	
			$limit=$this->getViewState('limit');	
			$offset=$this->getViewState('offset');			
			$cariByNama=$this->getViewState('cariByNama');
			$cariByID=$this->getViewState('cariByID');	
			$elemenBy=$this->getViewState('elemenBy');
			if(!$this->IsPostBack)
			{					
				$jmlData=count($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat));
			
				$this->jmlData->Text=$jmlData;
				$this->UserGrid->VirtualItemCount=$jmlData;
				// fetches all data account information 
				$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat);
				// binds the data to interface components
				$this->UserGrid->dataBind();		
			
			}else{
				$jmlData=count($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat));
			
				$this->jmlData->Text=$jmlData;
				$this->UserGrid->VirtualItemCount=$jmlData;
				// fetches all data account information 
				$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat);
				// binds the data to interface components
				$this->UserGrid->dataBind();		

			}		
				$this->ID->focus();
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
			
			HrgObatKhususRecord::finder()->deleteByPk($ID);
			$this->Response->redirect($this->Service->constructUrl('Farmasi.ObatKhusus'));
			
			//ObatRecord::finder()->deleteByPk($ID);	
			//$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObat'));
			
		}	
    }	
	
	
	public function changePage($sender,$param)
	{		
		$limit=$this->UserGrid->PageSize;
		$this->setViewState('limit', $this->UserGrid->PageSize);		
			
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;	
		$this->setViewState('offset', $offset);
		
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
	
	
	public function sortGrid($sender,$param)
	{		
		$orderBy = $param->SortExpression;
		$this->setViewState('orderBy',$orderBy);	
		
	}	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.ObatKhususBaru'));		
	}
	
	public function cariClicked($sender,$param)
	{		
		if($this->cariNama->Text){
			$this->setViewState('cariByNama', $this->cariNama->Text);
		}else{
			$this->clearViewState('cariByNama');	
		}	
		if($this->ID->Text){ 
			$this->setViewState('cariByID', $this->ID->Text);	
		}else{
			$this->clearViewState('cariByID');	
		}	
		
		if($this->Advance->Checked) {
			$this->setViewState('elemenBy',$this->Advance->Checked);
		}else{
			$this->clearViewState('elemenBy');	
		}
		
	}
	
	public function selectionChangedKec($sender,$param)
	{			
		if($this->cariNama->Text){
			$this->setViewState('cariByNama', $this->cariNama->Text);
		}else{
			$this->clearViewState('cariByNama');	
		}	
		if($this->ID->Text){ 
			$this->setViewState('cariByID', $this->ID->Text);	
		}else{
			$this->clearViewState('cariByID');	
		}	
		
		if($this->Advance->Checked) {
			$this->setViewState('elemenBy',$this->Advance->Checked);
		}else{
			$this->clearViewState('elemenBy');	
		}
	}
}
?>
