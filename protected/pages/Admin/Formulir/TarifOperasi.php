<?php
class TarifOperasi extends SimakConf
{   	
	
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)
		{					
			$criteria = new TActiveRecordCriteria;
			$criteria->OrdersBy['nama'] = 'asc';
			
			$this->DDoperasi->DataSource=OperasiNamaRecord::finder()->findAll($criteria);
			$this->DDoperasi->DataBind();
			
			$this->DDkelas->DataSource=KelasKamarRecord::finder()->findAll($criteria);
			$this->DDkelas->DataBind();
			
			$this->DDkelas->Enabled = false;
			$this->tarifPanel->Display = "None";
			$this->tarifPanel->Enabled = false;
			
			$this->editBtn->Enabled = false;
			$this->simpanBtn->Enabled = false;
			$this->batalBtn->Enabled = false;
		}
		else
		{
			$idOperasi = $this->DDoperasi->SelectedValue;
			$idKelas = $this->DDkelas->SelectedValue;
			
			if($idOperasi == '')
			{
				$this->DDkelas->SelectedIndex = -1;
				$this->DDkelas->SelectedValue = '';
				$this->DDkelas->Enabled = false;
				$this->tarifPanel->Display = "None";
				$this->editBtn->Enabled = false;
			}
			else
			{
				$this->DDkelas->Enabled = true;
				
				if($idKelas != '')
				{
					$this->tarifPanel->Display = "Dynamic";
					$this->bindGrid();
					
					if($this->getViewState('editMode') == '1')
					{
						$this->editBtn->Enabled = false;
					}
					else
					{
						$this->editBtn->Enabled = true;
					}
					
				}
				else
				{
					$this->tarifPanel->Display = "None";
					$this->editBtn->Enabled = false;
				}	
			}
		}		
    }	
	
	public function tarifPanelRender($sender, $param)
	{
		$this->tarifPanel->render($param->getNewWriter());
		$this->btnPanel->render($param->getNewWriter());
		
	}
	
	private function bindGrid()
    {
	   $sql = "SELECT 
	   				COLUMNS.COLUMN_COMMENT AS nm_tarif,
					COLUMN_NAME AS nm_field
				FROM INFORMATION_SCHEMA.COLUMNS 
				WHERE COLUMNS.TABLE_SCHEMA = 'simak'
				AND COLUMNS.TABLE_NAME = 'tbm_operasi_tarif'
				AND (COLUMN_COMMENT NOT LIKE 'FK%' AND COLUMN_COMMENT != '')
				
				";
				
		$arr = $this->queryAction($sql,'S');
	  
		$this->Repeater->DataSource=$arr;
		$this->Repeater->dataBind();
    }
	
	public function repeaterDataBound($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			$nm_tarif = $item->DataItem['nm_tarif'];
			$nm_field = $item->DataItem['nm_field'];
			
			$item->txtLabel->Text = $nm_tarif;
			
			$idOperasi = $this->DDoperasi->SelectedValue;
			$idKelas = $this->DDkelas->SelectedValue;
			
			$sql = "SELECT * FROM tbm_operasi_tarif WHERE id_operasi='$idOperasi' AND id_kelas='$idKelas'";
			$arr = $this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$tarif = $row[$nm_field];
			}
			
			$item->txtTarif->Text = $tarif;	
        }
    }
	
	public function DDoperasiChanged($sender,$param)
	{
		$this->Page->CallbackClient->focus($this->DDkelas);
	}
	
	public function DDkelasChanged($sender,$param)
	{
		//$this->Page->CallbackClient->focus($this->editBtn);
	}
	public function cariClicked($sender,$param)
	{
		$this->bindGrid();
	}
	
	
	public function editCallBack($sender, $param)
	{
		$this->firstPanel->render($param->getNewWriter());
		$this->tarifPanel->render($param->getNewWriter());
		$this->btnPanel->render($param->getNewWriter());
	}
	
	public function editClicked($sender,$param)
	{
		$this->firstPanel->Enabled = false;
		$this->tarifPanel->Enabled = true;
		
		$this->editBtn->Enabled = false;
		$this->simpanBtn->Enabled = true;
		$this->batalBtn->Enabled = true;
		
		$this->setViewState('editMode','1');
	}
	
	public function simpanClicked($sender,$param)
	{
		if($this->Page->IsValid)
		{
			$this->firstPanel->Enabled = true;
			$this->tarifPanel->Enabled = false;
			
			$this->editBtn->Enabled = true;
			$this->simpanBtn->Enabled = false;
			$this->batalBtn->Enabled = false;
			
			$this->clearViewState('editMode');
			
			//update tbm_operasi_tarif
			foreach($this->Repeater->Items as $item)
			{
				$idOperasi = $this->DDoperasi->SelectedValue;
				$idKelas = $this->DDkelas->SelectedValue;
				$nmField = $item->nmField->Value;
				$tarif = $item->txtTarif->Text;			
				
				$sql = "SELECT * FROM tbm_operasi_tarif WHERE id_operasi='$idOperasi' AND id_kelas='$idKelas'";
				$arr = $this->queryAction($sql,'S');
				
				if(count($arr) < 1)
				{
					$sql = "INSERT INTO tbm_operasi_tarif (id_operasi,id_kelas) VALUES ('$idOperasi','$idKelas')";						
					$this->queryAction($sql,'C');
				}
				
				$sql = "UPDATE tbm_operasi_tarif SET $nmField = '$tarif' WHERE id_operasi='$idOperasi' AND id_kelas='$idKelas'";						
				$this->queryAction($sql,'C');
			}	
		}
	}
	
	public function batalClicked($sender,$param)
	{
		$this->firstPanel->Enabled = true;
		$this->tarifPanel->Enabled = false;
		
		$this->editBtn->Enabled = true;
		$this->simpanBtn->Enabled = false;
		$this->batalBtn->Enabled = false;
		
		$this->clearViewState('editMode');
	}
	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.AdminFormulir'));		
	}
		
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifOperasiBaru'));		
	}
}
?>
