<?php
class KmrTdkEdit extends SimakConf
{   
	private $sortExp = "id_kasir_rwtjln";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 30;	
		
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
			//$cm=$this->Request['cm'];
			$this->cariCM->focus();	
			//$this->cariCM->text=$cm;
			//$this->cmChanged($sender, $param);
			
			//$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			//$this->DDKlinik->dataBind();
			//$this->DDKlinik->Enabled = false;
			
			$this->gridPanel->Display = 'None';
							
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
            
			$cm = $this->formatCm($this->cariCM->Text);
			//$poli = $this->DDKlinik->SelectedValue;		
			$sql .= "SELECT
							tbt_inap_operasi_billing.id AS id_kasir_rwtjln,
							tbt_inap_operasi_billing.id_opr AS id_tindakan,
							tbm_operasi_nama.nama AS nm_tindakan
						FROM
							tbt_inap_operasi_billing
							Inner Join tbm_operasi_nama ON tbt_inap_operasi_billing.id_opr = tbm_operasi_nama.id
						WHERE tbt_inap_operasi_billing.cm='$cm' AND tbt_inap_operasi_billing.st='0' ";

			
			/*	
            $sql .= "SELECT 
					  tbt_kasir_rwtjln.id AS id_kasir_rwtjln,
					  tbt_rawat_jalan.cm,
					  tbt_rawat_jalan.no_trans,
					  tbt_rawat_jalan.id_klinik,
					  tbm_poliklinik.nama AS nm_poli,
					  tbt_kasir_rwtjln.id_tindakan AS id_tindakan,
					  tbm_nama_tindakan.nama AS nm_tindakan,
					  tbt_kasir_rwtjln.bhp,
					  tbt_kasir_rwtjln.tarif,
					  tbt_kasir_rwtjln.total,
					  tbt_kasir_rwtjln.st_flag
					FROM
					  tbt_rawat_jalan
					  INNER JOIN tbt_kasir_rwtjln ON (tbt_rawat_jalan.no_trans = tbt_kasir_rwtjln.no_trans_rwtjln)
					  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
					  INNER JOIN tbm_nama_tindakan ON (tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id)
					WHERE tbt_kasir_rwtjln.st_flag = '0' 
					 	AND tbt_rawat_jalan.cm = '$cm'";
			/*
			if($this->cekBayar->Checked){		
				$sql .=	" tbt_kasir_rwtjln.st_flag = '1' "; 
			}else{
				$sql .=	" tbt_kasir_rwtjln.st_flag = '0' "; 	
			}
				$sql .="	AND tbt_rawat_jalan.cm = '$cm'
					AND tbt_rawat_jalan.id_klinik = '$poli' ";
			 */
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			//$this->jmlDataPas->Text=$someDataList->getSomeDataCount($sql);    			
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
        
       
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
        $this->bindGrid();

    }
	
	public function itemCreated($sender,$param)
    {
        $item=$param->Item;
		
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes 
			// $item->tindakanColumn->TextBox->Columns=50;
			$item->tindakanColumn->DropDownList->Width = '220px';
        } 
		
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
			$item=$param->Item;
			$ID=$this->dtgSomeData->DataKeys[$item->ItemIndex];
			
			$sql="DELETE FROM tbt_inap_operasi_billing WHERE id='$ID' ";
			$arr=$this->queryAction($sql,'C');
			//$this->showSql->text=$sql;
			//$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarCariRwtJln'));
			
			$this->dtgSomeData->EditItemIndex=-1;
        	$this->bindGrid();
			
		}	
    }	
	
	protected function dtgSomeData_EditCommand($sender,$param)
    {
        $this->dtgSomeData->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGrid();
    }

    protected function dtgSomeData_UpdateCommand($sender,$param)
    {
        $item = $param->Item;
		$idKasirRwtJln = $this->dtgSomeData->DataKeys[$item->ItemIndex];
        // implement doSomeValidation() if you need to check the modified data before you commit the changes
        $idTindakan = $item->tindakanColumn->DropDownList->SelectedValue;
		$bhp = $this->ambilTxt($item->bhpColumn->DropDownList);
		
		$sql="SELECT 
				(biaya1 + biaya2 + biaya3 + biaya4) AS total 
			  FROM 
			  	tbm_tarif_tindakan 
			  WHERE id='$idTindakan' ";
		$tarif = TarifTindakanRecord::finder()->findBySql($sql)->total;
		
        if ($this->User->IsAdmin)
		{
			$oSomeData = KasirRwtJlnRecord::finder()->findByPk($idKasirRwtJln);
            $oSomeData->id_tindakan = $idTindakan;
			$oSomeData->tarif = $tarif;
			$oSomeData->bhp = $bhp;
			$oSomeData->total = $bhp + $tarif;
            $oSomeData->save();
            
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
	
	public function cmChanged($sender,$param)
	{		
		$cm = $this->formatCm($this->cariCM->Text);
		$this->msg->Text = '';/*
		if($this->cekBayar->Checked)
		{
			$sql = "SELECT * FROM tbt_rawat_jalan WHERE cm = '$cm' AND flag = '1'";		
		}else{*/
			$sql = "SELECT * FROM tbt_inap_operasi_billing WHERE cm = '$cm' AND st = '0'";
		//}	
		$arr = $this->queryAction($sql,'S');
		
		if(count($arr) > 0) //pasien rawat jalan yang status nya belum keluar ditemukan
		{/*
			$data[] = array('id'=>'','nama'=>'--Pilih Poloklinik--');
			foreach($arr as $row)
			{
				$idKlinik = $row['id_klinik'];
				//$data[] = array('id'=>$idKlinik,'nama'=>PoliklinikRecord::finder()->findByPk($idKlinik)->nama);
			}
			
			//$this->DDKlinik->DataSource = $data;
			//$this->DDKlinik->dataBind();
			//$this->DDKlinik->Enabled = true;			
			//$this->Page->CallbackClient->focus($this->DDKlinik);			*/
			$this->gridPanel->Display = 'Dynamic';
			$this->bindGrid();	
		}
		else
		{
			$this->msg->Text = 'Pasien dengan No. Rekam medis <b>'.$cm.'</b> tidak ditemukan';
			$this->cariCM->Text = '';
			$this->Page->CallbackClient->focus($this->cariCM);
			//$this->DDKlinik->Enabled = false;
		}
		
		if($cm == '')
		{
			$this->msg->Text = '';
			$this->DDKlinik->Enabled = false;
			
			$this->gridPanel->Display = 'None';
			$session = $this->getSession();
			$session->remove("SomeData");
			
			$this->dtgSomeData->CurrentPageIndex = 0;
			
			$this->Page->CallbackClient->focus($this->cariCM);
		}
	}
	/*
	public function DDKlinikChanged($sender,$param)
	{
		$idKlinik = $this->DDKlinik->SelectedValue;
		if($idKlinik != '')
		{
			$this->gridPanel->Display = 'Dynamic';
			$this->bindGrid();		
		}
		else
		{
			$this->gridPanel->Display = 'None';
			$session = $this->getSession();
			$session->remove("SomeData");
			
			$this->dtgSomeData->CurrentPageIndex = 0;
		}
	}*/
	
	public function getTindakanRecords()
	{
		$sql="SELECT 
				  tbm_nama_tindakan.id,
				  tbm_nama_tindakan.nama
				FROM
				  tbm_tarif_tindakan
				  INNER JOIN tbm_nama_tindakan ON (tbm_tarif_tindakan.id = tbm_nama_tindakan.id)
				WHERE
				  (tbm_tarif_tindakan.biaya1 + tbm_tarif_tindakan.biaya2 + tbm_tarif_tindakan.biaya3 + tbm_tarif_tindakan.biaya4) > 0
				ORDER BY
				  nama ASC";				
		
		$data=NamaTindakanRecord::finder()->findAllBySql($sql);
		return $data;
	}
	
	public function getBhpRecords()
	{
		$data = $this->bhpData(2500,20);
		return $data;
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
		
	public function cariClicked($sender,$param)
	{		
		$session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
		
		
	}
	
	public function batalClicked($sender,$param)
    {			
		$this->Response->reload();
	}	
	
}
	
?>
