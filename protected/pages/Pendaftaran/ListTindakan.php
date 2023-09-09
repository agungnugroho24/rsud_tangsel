<?php
class ListTindakan extends SimakConf
{
	private $sortExp = "nama";
	private $sortDir = "ASC";
	private $offset = 0;
	private $pageSize = 10;
		
	public function onInit($param)
	{		
		parent::onInit($param);
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	}
	 
	public function onPreRender($param)
	{				
		parent::onPreRender($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)        		
		{			
			$this->valKlinik->Enabled = false;
			$this->valKode->Enabled = false;
			$this->valNama->Enabled = false;
			
			$this->stStandard->Display = 'None'; 
			$this->stStandard->Checked = false;
			
			$sql = "SELECT  id, nama FROM tbm_poliklinik ORDER BY nama ";
			$arr = $this->queryAction($sql,'S');
					  
			$this->DDKlinik->DataSource = $arr;
			$this->DDKlinik->dataBind();
			
			if($this->Request['klinik'])
				$this->DDKlinik->SelectedValue = $this->Request['klinik'];
			
			$this->cariClicked($sender,$param);
			$this->DDKlinik->focus();
			
			if($this->Request['nmTable'])
			{
				$this->setViewState('nmTable',$this->Request['nmTable']);		
				$this->bindGridIcd();
			}
			elseif($this->Request['noTransJalan'] && $this->Request['mode'] == 'edit') //edit ICD
			{
				$noTransJalan = $this->Request['noTransJalan'];
				
				$sql="SELECT *,tbm_nama_tindakan.nama FROM tbt_kasir_rwtjln INNER JOIN tbm_nama_tindakan ON (tbm_nama_tindakan.id = tbt_kasir_rwtjln.id_tindakan) WHERE no_trans_rwtjln = '$noTransJalan' ";
				$arrData=$this->queryAction($sql,'S');
				
				if(count($arrData) > 0 )
				{
					foreach($arrData as $row)
					{
						$ID = $row['id_tindakan'];
						$nama = $row['nama'];
						$bhp = $row['bhp'];
						$alat = $row['sewa_alat'];
						$total = $row['total'];
						$catatan = $row['catatan'];
						$pengali = $row['pengali'];
						$tanggungan_asuransi = $row['tanggungan_asuransi'];
						$st_costsharing = $row['st_costsharing'];
						$jml = $total + $bhp + $alat;
						
						if (!$this->getViewState('nmTable'))
						{
							$nmTable = $this->setNameTable('nmTable');
							$sql="CREATE TABLE $nmTable (
											id INT (2) auto_increment,									
											nama VARCHAR(255) NOT NULL,
											id_tdk VARCHAR(4) DEFAULT NULL,
											bhp FLOAT DEFAULT 0,
											alat FLOAT DEFAULT 0,
											total float DEFAULT 0,
											jml float DEFAULT 0, 
											tanggungan_asuransi float DEFAULT 0,
											st_costsharing CHAR(1) DEFAULT '0',
											st_tambahan CHAR(1) DEFAULT '1',
											st_icd_global CHAR(1) DEFAULT '1',
											pengali INT (4) DEFAULT 1,
											catatan VARCHAR(255) DEFAULT NULL,
											PRIMARY KEY (id)) ENGINE = MEMORY";
							
							$this->queryAction($sql,'C');//Create new tabel bro...
						}
						else
						{
							$nmTable = $this->getViewState('nmTable');								
						}	
						
						$sql = "SELECT * FROM $nmTable WHERE id_tdk = '$ID' ";
						$arrData = $this->queryAction($sql,'S');//Select row in tabel bro...
						
						if($arrData)
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','unmaskContent();
								jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan :<br/> <b>'.ucwords(NamaTindakanRecord::finder()->findByPk($ID)->nama).'</b><br/> sudah ditambahkan sebelumnya!</p>\',timeout: 4000,dialog:{
									modal: true
								}});');	
						}
						else
						{
							$sql="INSERT INTO $nmTable (nama,bhp,alat,total,jml,tanggungan_asuransi,id_tdk,st_tambahan,st_costsharing,catatan,pengali) VALUES ('$nama','$bhp','$alat','$total','$jml','$tanggungan_asuransi','$ID','0','$st_costsharing','$catatan','$pengali')";
							$this->queryAction($sql,'C');	
						}
					}
					
					$session = $this->getSession();
					$session->remove("SomeData"); 
					$this->UserGrid->CurrentPageIndex = 0;
					$this->bindGridIcd();
				}
			}
		}
		
		if($this->getViewState('nmTable'))
		{
			$this->simpanBtn->Enabled = true;	
			$this->icdPanel->Display = 'Dynamic';				
		}
		else
		{
			$this->simpanBtn->Enabled = false;	
			$this->icdPanel->Display = 'None';				
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
			
			$idKlinik = $this->Request['klinik'];
			$noTransJalan = $this->Request['noTransJalan'];
			$stAsuransi = RwtjlnRecord::finder()->findByPk($noTransJalan)->st_asuransi;	
				
			if($noTransJalan && $stAsuransi == '1')
			{
				$idPenjamin = RwtjlnRecord::finder()->findByPk($noTransJalan)->penjamin;
				$idPerusAsuransi = RwtjlnRecord::finder()->findByPk($noTransJalan)->perus_asuransi;
						
				$sql="SELECT 
						tbm_nama_tindakan.id,
						tbm_nama_tindakan.nama,
						tbm_nama_tindakan.id_klinik,
						IF((SELECT COUNT(tarif) FROM tbm_provider_detail_tindakan WHERE id_provider='$idPerusAsuransi' AND id_tindakan=tbm_nama_tindakan.id AND id_poli='$idKlinik' LIMIT 0,1) > 0,(SELECT tarif FROM tbm_provider_detail_tindakan WHERE id_provider='$idPerusAsuransi' AND id_tindakan=tbm_nama_tindakan.id AND id_poli='$idKlinik' LIMIT 0,1),(SELECT biaya1 FROM tbm_tarif_tindakan WHERE tbm_tarif_tindakan.id = tbm_nama_tindakan.id)) AS biaya1
					FROM
						tbm_nama_tindakan
					WHERE
						tbm_nama_tindakan.st_rawat = '0' ";
			}
			else
			{
				$sql="SELECT 
						tbm_nama_tindakan.id,
						tbm_nama_tindakan.nama,
						tbm_nama_tindakan.id_klinik,
						tbm_tarif_tindakan.biaya1,
						tbm_tarif_tindakan.biaya2,
						tbm_tarif_tindakan.biaya3,
						tbm_tarif_tindakan.biaya4
					FROM
						tbm_tarif_tindakan
						INNER JOIN tbm_nama_tindakan ON (tbm_tarif_tindakan.id = tbm_nama_tindakan.id)
					WHERE
						tbm_nama_tindakan.st_rawat = '0' ";
			}
			
			if(trim($this->kode->Text) != '')
			{
				$kode = trim($this->kode->Text);
				$sql .= "AND tbm_nama_tindakan.id LIKE '%$kode%' ";
			}
			
			if(trim($this->nmIndo->Text) != '')
			{
				$nmIndo = trim($this->nmIndo->Text);
				$sql .= "AND tbm_nama_tindakan.nama LIKE '%$nmIndo%' ";
			}
			
			if($this->DDKlinik->SelectedValue != '')
			{
				$id_klinik = $this->DDKlinik->SelectedValue;
				$sql .= "AND tbm_nama_tindakan.id_klinik = '$id_klinik' ";	
			}
			
			//$sql .= " ORDER BY tbm_nama_tindakan.nama ASC";
			//$this->setViewState('sql',$sql);
			//$this->showSql->Text = $idKlinik.' - '.$noTransJalan.' - '.$stAsuransi.' - '.$sql;
			
			$data = $someDataList->getSomeDataPage($sql);
			$this->grid->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
			$this->grid->DataSource = $data;
			$this->grid->DataBind();
			
			/*if($someDataList->getSomeDataCount($sql) > 10)
			{
				//$this->pagerPanel->Display = 'Dynamic';
      }
			elseif($someDataList->getSomeDataCount($sql) <= 10 )
			{
				//$this->pagerPanel->Display = 'None';
			}*/
		}
		else 
		{
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
        $item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			$ID = $this->grid->DataKeys[$item->ItemIndex];
			
			//$nmDpn = $item->DataItem['nama_dpn'];
			//$nmBlk = $item->DataItem['nama_blk'];
			
			$item->tarifColumn->Text = number_format($item->DataItem['biaya1'],2,',','.');
			//$item->telpColumn->hpTxt->Text = $hp;
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
	
	
	private function bindGridIcd()
    {
	    $this->pageSize = $this->UserGrid->PageSize;
        $this->offset = $this->pageSize * $this->UserGrid->CurrentPageIndex;
        
        $session = $this->getSession();
		
		if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = 'DESC';
            $someDataList->sortExpression = 'id';
			
			$nmTable = $this->getViewState('nmTable');	
			
			$sql="SELECT 
							*, IF (LENGTH(catatan) > 0,CONCAT(nama, ' (', catatan,')'),nama ) AS nama
						FROM 
							$nmTable ";
		
			//$this->setViewState('sql',$sql);
			//$this->msg->Text = $sql;
            $data = $someDataList->getSomeDataPage($sql);
            $this->UserGrid->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			//$this->jmlData->Text=$someDataList->getSomeDataCount($sql).' produk';    			
            $this->UserGrid->DataSource = $data;
            $this->UserGrid->DataBind();
			
			if($someDataList->getSomeDataCount($sql) > 10)
			{
				//$this->pagerPanel->Display = 'Dynamic';
        	}
			elseif($someDataList->getSomeDataCount($sql) <= 10 )
			{
				//$this->pagerPanel->Display = 'None';
			}
		}
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->UserGrid->DataSource = $session["SomeData"];
            $this->UserGrid->DataBind();
			
			//$this->clearViewState('sql');
        }
    }
	
	protected function changePage($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData"); 
		$this->UserGrid->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGridIcd();
    }
	
	public function prosesTolakDialog()
	{
		//$mode = $param->CallbackParameter->Id;
		$ID = trim($this->idTmp->Text);
		$pengali = intval(abs($this->pengali->Text));
		$catatan = trim($this->catatan->Text);
		
		$nmTable = $this->getViewState('nmTable');
		
		$sql = "UPDATE $nmTable SET jumlah_akhir = 0, jumlah_kecil_akhir = 0, catatan = '$catatan', st = '3' WHERE id='$ID'";
		$this->queryAction($sql,'C');	
		
		$this->idTmp->Text = '';
		$this->catatan->Text = '';
		$this->bindGrid();
	}
	
	public function gridMasukanClicked($sender,$param)
  {
		//$ID = $param->CommandParameter;
		$ID = trim($this->idTmp->Text);
		$catatan = trim($this->catatan->Text);
		
		if(intval(trim($this->pengali->Text)) == false)
			$pengali = 1;
		elseif(intval(trim($this->pengali->Text)) < 1)
			$pengali = abs(trim($this->pengali->Text));
		else
			$pengali = trim($this->pengali->Text);
			
		$nama = NamaTindakanRecord::finder()->findByPk($ID)->nama;
		$st_paket = NamaTindakanRecord::finder()->findByPk($ID)->st_paket;
		
		if($st_paket=='0')
		{
			$total = TarifTindakanRecord::finder()->findByPk($ID)->biaya1;
			$id_paket = 0;
		}
		else
		{
			$pengaliPaket = $pengali;
			$pengali = 1;
			$id_paket = NamaTindakanPaketDetailRecord::finder()->find('id_tindakan=?',$ID)->id_paket;
			$total = NamaTindakanPaketRecord::finder()->findByPk($id_paket)->tarif;
			if($this->getViewState('nmTable') && $id_paket)
			{
				$nmTable = $this->getViewState('nmTable');
				$sql = "SELECT id FROM $nmTable WHERE id_paket = '$id_paket' ";
				if($this->queryAction($sql,'S'))
				{
					$total = 0;
				}
			}
		}
		
		$idKlinik = $this->Request['klinik'];
		
		if($this->Request['noTransJalan'])
		{
			$noTransJalan = $this->Request['noTransJalan'];
			$stAsuransi = RwtjlnRecord::finder()->findByPk($noTransJalan)->st_asuransi;	
			
			if($stAsuransi == '1')
			{
				$idPenjamin = RwtjlnRecord::finder()->findByPk($noTransJalan)->penjamin;
				$idPerusAsuransi = RwtjlnRecord::finder()->findByPk($noTransJalan)->perus_asuransi;
				
				$sql = "SELECT * FROM tbm_provider_detail_tindakan WHERE id_provider='$idPerusAsuransi' AND id_tindakan='$ID' AND id_poli='$idKlinik'";
				if(ProviderDetailTindakanRecord::finder()->findBySql($sql))
				{
					$tarif = abs(ProviderDetailTindakanRecord::finder()->findBySql($sql)->tarif);
					$tarif_gsm = abs(ProviderDetailTindakanRecord::finder()->findBySql($sql)->tarif_gsm);
					$diskon = abs(ProviderDetailTindakanRecord::finder()->findBySql($sql)->diskon);
					$st_tarif = abs(ProviderDetailTindakanRecord::finder()->findBySql($sql)->st_tarif);
					$total = 0;	
					$tanggungan_asuransi = 0;	
					$st_costsharing = '0';
					
					if($st_tarif == '1')//tarif kerjasama
					{
						if($tarif == 0 && $diskon == 0)
							$tanggungan_asuransi = $tarif_gsm;
						elseif($tarif_gsm > 0)
							$tanggungan_asuransi = $tarif;
					}
					elseif($st_tarif == '2')//tarif plafond
					{
						if($tarif > 0 && $tarif_gsm > 0 && $tarif >= $tarif_gsm)
							$tanggungan_asuransi = $tarif_gsm;
						if($tarif > 0 && $tarif_gsm > 0 && $tarif < $tarif_gsm)
						{
							$tanggungan_asuransi = $tarif;
							$total = $tarif_gsm - $tarif;	
							$st_costsharing = '1';
						}
					}
					
					$tanggungan_asuransi = ($tanggungan_asuransi * intval(abs($pengali)));
					$total = ($total * intval(abs($pengali)));
					$st_asuransi = '1';
				}
				else
				{
					$total = ($total * intval(abs($pengali)));
					$st_asuransi = '0';
					$st_costsharing = '1';
				}
			}		
			else
			{
				$total = ($total * intval(abs($pengali)));
				$st_asuransi = '0';
			}
			//$bhp = BhpTindakanRecord::finder()->findByPk($this->DDbhp->SelectedValue)->tarif;
			//$alat = BhpTindakanRecord::finder()->findByPk($this->DDalat->SelectedValue)->tarif;
			
			//$total = ($total * intval(abs($pengali)));
			$jml = $total + $bhp + $alat;	
		}
		
		if (!$this->getViewState('nmTable'))
		{
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (
							id INT (2) auto_increment,									
							nama VARCHAR(255) NOT NULL,
							id_tdk VARCHAR(4) DEFAULT NULL,
							bhp FLOAT DEFAULT 0,
							alat FLOAT DEFAULT 0,
							total float DEFAULT 0,
							jml float DEFAULT 0, 
							tanggungan_asuransi float DEFAULT 0,
							st_costsharing CHAR(1) DEFAULT '0',
							st_tambahan CHAR(1) DEFAULT '1',
							st_icd_global CHAR(1) DEFAULT '1',
							pengali INT (4) DEFAULT 1,
							id_paket INT (11) DEFAULT 0,
							catatan VARCHAR(255) DEFAULT NULL,
							PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		else
		{
			$nmTable = $this->getViewState('nmTable');								
		}	
		
		$sql = "SELECT * FROM $nmTable WHERE id_tdk = '$ID' ";
		$arrData = $this->queryAction($sql,'S');//Select row in tabel bro...
		
		if($arrData)
		{
			$this->getPage()->getClientScript()->registerEndScript
			('','unmaskContent();
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan :<br/> <b>'.ucwords(NamaTindakanRecord::finder()->findByPk($ID)->nama).'</b><br/> sudah ditambahkan sebelumnya!</p>\',timeout: 4000,dialog:{
					modal: true
				}});');	
		}
		else
		{
			if($st_paket=='1')
				$pengali = $pengaliPaket;
				
			$sql="INSERT INTO $nmTable (id_tdk,nama,total,jml,pengali,catatan,tanggungan_asuransi,id_paket) VALUES ('$ID','$nama','$total','$total','$pengali','$catatan','$tanggungan_asuransi','$id_paket')";
			$this->queryAction($sql,'C');	
		}
		
		$session = $this->getSession();
        $session->remove("SomeData"); 
		$this->UserGrid->CurrentPageIndex = 0;
		$this->bindGridIcd();
    }	
		
	public function gridMasukanClicked2($sender,$param)
  {
		$ID = $param->CommandParameter;
		$nama = NamaTindakanRecord::finder()->findByPk($ID)->nama;
		$total = TarifTindakanRecord::finder()->findByPk($ID)->biaya1;
		$idKlinik = $this->Request['klinik'];
		
		if($this->Request['noTransJalan'])
		{
			$noTransJalan = $this->Request['noTransJalan'];
			$stAsuransi = RwtjlnRecord::finder()->findByPk($noTransJalan)->st_asuransi;	
			
			if($stAsuransi == '1')
			{
				$idPenjamin = RwtjlnRecord::finder()->findByPk($noTransJalan)->penjamin;
				$idPerusAsuransi = RwtjlnRecord::finder()->findByPk($noTransJalan)->perus_asuransi;
				
				$sql = "SELECT * FROM tbm_provider_detail_tindakan WHERE id_provider='$idPerusAsuransi' AND id_tindakan='$ID' AND id_poli='$idKlinik'";
				if(ProviderDetailTindakanRecord::finder()->findBySql($sql))
				{
					$total = ProviderDetailTindakanRecord::finder()->findBySql($sql)->tarif;	
				}
			}		
			
			//$bhp = BhpTindakanRecord::finder()->findByPk($this->DDbhp->SelectedValue)->tarif;
			//$alat = BhpTindakanRecord::finder()->findByPk($this->DDalat->SelectedValue)->tarif;
			
			$jml = $total + $bhp + $alat;	
		}
		
		if (!$this->getViewState('nmTable'))
		{
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (
							id INT (2) auto_increment,									
							nama VARCHAR(255) NOT NULL,
							id_tdk VARCHAR(4) DEFAULT NULL,
							bhp FLOAT DEFAULT 0,
							alat FLOAT DEFAULT 0,
							total float DEFAULT 0,
							jml float DEFAULT 0, 
							jml float DEFAULT 0, 
							tanggungan_asuransi float DEFAULT 0,
							st_costsharing CHAR(1) DEFAULT '0',
							st_tambahan CHAR(1) DEFAULT '1',
							st_icd_global CHAR(1) DEFAULT '1',
							pengali INT (4) DEFAULT 1,
							catatan VARCHAR(255) DEFAULT NULL,
							PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		else
		{
			$nmTable = $this->getViewState('nmTable');								
		}	
		
		$sql = "SELECT * FROM $nmTable WHERE id_tdk = '$ID' ";
		$arrData = $this->queryAction($sql,'S');//Select row in tabel bro...
		
		/*if($arrData)
		{
			$this->getPage()->getClientScript()->registerEndScript
			('','unmaskContent();
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan :<br/> <b>'.ucwords(NamaTindakanRecord::finder()->findByPk($ID)->nama).'</b><br/> sudah ditambahkan sebelumnya!</p>\',timeout: 4000,dialog:{
					modal: true
				}});');	
		}
		else
		{*/
			$sql="INSERT INTO $nmTable (id_tdk,nama,total,jml) VALUES ('$ID','$nama','$total','$total')";
			$this->queryAction($sql,'C');	
		//}
		
		$session = $this->getSession();
        $session->remove("SomeData"); 
		$this->UserGrid->CurrentPageIndex = 0;
		$this->bindGridIcd();
    }	
	
	public function itemCreated($sender,$param)
	{
		$item=$param->Item;
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem' || $item->ItemType==='EditItem' || $item->ItemType==='DeleteItem')
		{
			$item->kodeColumn->Text = $item->DataItem['id_tdk'];
			$item->namaColumn->Text = $item->DataItem['nama'];
			$item->pengaliColumn->Text = $item->DataItem['pengali'];
			$item->tarifColumn2->Text = number_format($item->DataItem['jml'],2,',','.');
			$item->tarifColumn3->Text = number_format($item->DataItem['tanggungan_asuransi'],2,',','.');
			
			/*$cm = $this->UserGrid->DataKeys[$item->ItemIndex];
			
			$conTgl = $this->ConvertDate($item->DataItem['tgl_lahir'],'3');
			$item->tglColumn->Text = $conTgl;
			
			if($item->DataItem['jns_kelamin'] == '0')
			$item->jnsKelColumn->Text =  "Laki-Laki";
			else
			$item->jnsKelColumn->Text =  "Perempuan";	*/
		}
	}
    
	public function deleteClicked($sender,$param)
  {
		$item=$param->Item;	
		$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
		$nmTable = $this->getViewState('nmTable');
		
		//CEK PAKET
		$sql="SELECT * FROM $nmTable WHERE id='$ID'";
		$arrData = $this->queryAction($sql,'S');
		foreach($arrData as $row)
		{
			$id_paket = $row['id_paket'];
			$total = $row['total'];
			$jml = $row['jml'];
			$tanggungan_asuransi = $row['tanggungan_asuransi'];
		}
		
		if($id_paket>0 && ($total>0 || $tanggungan_asuransi>0))
		{
			$sql="SELECT MAX(id) AS id FROM $nmTable WHERE id_paket='$id_paket' AND id<>'$ID'";
			$arrData = $this->queryAction($sql,'S');
			foreach($arrData as $row)
			{
				$idTblPaket = $row['id'];
			}
			
			$sql="UPDATE $nmTable SET total='$total',jml='$jml',tanggungan_asuransi='$tanggungan_asuransi' WHERE id='$idTblPaket' ";
			$this->queryAction($sql,'C');
		}
		$this->tes->Text = $sql.' - '.count($arrData).' - '.$id_paket.' - '.$total.' - '.$tanggungan_asuransi;
		
		$sql = "DELETE FROM $nmTable WHERE id='$ID'";
		$this->queryAction($sql,'C');	
		
		$sql="SELECT  * FROM  $nmTable ";
		$arrData=$this->queryAction($sql,'S');
		$jmlData=0;
		foreach($arrData as $row)
		{
			$jmlData++;
		}
		
		$session = $this->getSession();
		$session->remove("SomeData"); 
		$this->UserGrid->CurrentPageIndex = 0;
		$this->bindGridIcd();
		
		if($jmlData==0)
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
			$this->UserGrid->DataSource='';
			$this->UserGrid->dataBind();
			$this->clearViewState('nmTable');
		}
	}	
	
	public function allPoliCheckedChanged($sender,$param)
	{
		$this->DDKlinik->Enabled = true;
		
		if($this->allPoli->Checked == true)
		{
			$this->DDKlinik->SelectedValue = 'empty';
			$this->DDKlinik->Enabled = false;
		}
		else
		{
			if($this->Request['klinik'])
				$this->DDKlinik->SelectedValue = $this->Request['klinik'];
		}
		
		$this->cariClicked($sender,$param);
	}
	
	
	public function cariClicked($sender,$param)
	{			
		
		$session = $this->getSession();
        $session->remove("SomeData");        
        $this->grid->CurrentPageIndex = 0;
        $this->bindGrid();
	}	
	
	public function simpanClicked()
	{
		$nmTable = $this->getViewState('nmTable');
			
		if($this->Request['noTransJalan'] && $this->Request['mode'] == 'edit') //edit ICD
		{
			$noTransEdit = $this->Request['noTransJalan'];
			
			$sql="SELECT * FROM $nmTable WHERE st_tambahan = '1' ORDER BY id";
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			foreach($arrData as $row)
			{
				$notrans = $this->numCounter('tbt_kasir_rwtjln',KasirRwtJlnRecord::finder(),'08');//key '08' adalah konstanta modul untuk Kasir Rawat Jalan
				
				$transRwtJln= new KasirRwtJlnRecord();
				$transRwtJln->no_trans=$notrans;
				$transRwtJln->no_trans_rwtjln=$this->Request['noTransJalan'];
				$transRwtJln->cm=$this->Request['cm'];
				$transRwtJln->klinik=$this->Request['klinik'];
				$transRwtJln->dokter=$this->Request['dokter'];
				$transRwtJln->id_tindakan=$row['id_tdk'];
				$transRwtJln->tgl=date('y-m-d');
				$transRwtJln->waktu=date('G:i:s');
				$transRwtJln->operator=$nipTmp;
				$transRwtJln->bhp=$row['bhp'];
				$transRwtJln->sewa_alat=$row['alat'];
				$transRwtJln->tarif=$row['total'];
				$transRwtJln->total=$row['jml'];
				$transRwtJln->tanggungan_asuransi=$row['tanggungan_asuransi'];
				$transRwtJln->disc=$row['disc'];
				$transRwtJln->st_flag='0';
				$transRwtJln->disc='0';
				$transRwtJln->st_kredit='0';
				$transRwtJln->pengali=$row['pengali'];
				$transRwtJln->catatan=$row['catatan'];
				$transRwtJln->Save();		
			}
			
			if($this->getViewState('nmTable'))
			{
				$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
				$this->clearViewState('nmTable');//Clear the view state	
			}			
		}
				
		$this->getPage()->getClientScript()->registerEndScript('','window.parent.maskContent(); window.parent.modalTambahCallback("'.$nmTable.'"); jQuery.FrameDialog.closeDialog();');
		/*
		$this->getPage()->getClientScript()->registerEndScript
		('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Proses penambahan ICD ?</p>\',timeout: 6000000,dialog:{
				modal: true,
				buttons: {
					"Ya": function() {
						jQuery( this ).dialog( "close" );
						konfirmasi(\'ya\');
					},
					Tidak: function() {
						jQuery( this ).dialog( "close" );
						maskContent();
						konfirmasi(\'tidak\');
					}
				}
			}});');	*/
	}	
	
	public function prosesKonfirmasi($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
		
		//$this->getPage()->getClientScript()->registerEndScript('','alert('.$mode.')');	
				
		if($mode == 'ya')
		{	
			$nmTable = $this->getViewState('nmTable');
			
			if($this->Request['noTransJalan']  && $this->Request['mode'] == 'edit') //edit ICD
			{
				$noTransEdit = $this->Request['noTransJalan'];
				
				$sql="SELECT * FROM $nmTable WHERE st_tambahan = '1' ORDER BY id";
				$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
				foreach($arrData as $row)
				{
					$data= new IcdPasienRecord();
					$data->no_trans = $noTransEdit;
					$data->kode_icd = $row['kode'];
					$data->st_icd_global = $row['st_icd_global'];
					$data->Save();			
				}
				
				if($this->getViewState('nmTable'))
				{
					$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
					$this->clearViewState('nmTable');//Clear the view state	
				}			
			}
					
			$this->getPage()->getClientScript()->registerEndScript('','window.parent.maskContent(); window.parent.modalTambahCallback("'.$nmTable.'"); jQuery.FrameDialog.closeDialog();');	
			
			//$this->Page->CallbackClient->focus($this->nama);
			//$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
			//$this->Response->reload();
		}
		else
		{
			if($this->getViewState('nmTable'))
			{
				$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
				$this->UserGrid->DataSource='';
				$this->UserGrid->dataBind();
				$this->clearViewState('nmTable');//Clear the view state	
			}
			
			$this->getPage()->getClientScript()->registerEndScript('','window.parent.maskContent(); window.parent.modalTambahCallback('.$id.'); jQuery.FrameDialog.closeDialog();');	
		}
	}
	
	public function keluarClicked($sender,$param)
	{		
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
			$this->UserGrid->DataSource='';
			$this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state	
		}
		
		$this->getPage()->getClientScript()->registerEndScript
		('','window.parent.maskContent(); window.parent.modalTambahCallback('.$id.'); jQuery.FrameDialog.closeDialog();');
		
		//$this->Response->reload();
		//$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.JpkPegawai'));		
	}
	
	
	public function stStandardChanged($sender,$param)
	{
		if($this->stStandard->Checked == true)
			$this->valKode->Enabled = false;
		else
			$this->valKode->Enabled = true;
	}
	
}
?>
