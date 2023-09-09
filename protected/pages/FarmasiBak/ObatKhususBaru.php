<?php
class ObatKhususBaru extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
 public function onLoad($param)
	{
		parent::onLoad($param);
		/*
		$tmpVar=$this->authApp('8');//ID aplikasi keKaban
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
		{
			$this->Response->redirect($this->Service->constructUrl('Home'));
		
		}
		*/
			
		if(!$this->IsPostBack)
		{			
			$this->DDObat->DataSource=ObatRecord::finder()->findAll();
			$this->DDObat->dataBind();		
		}		
	}
	
	public function selectionChangedObat($sender,$param)
	{	
		if($this->DDObat->SelectedValue != '')
		{
			$idObat = $this->DDObat->SelectedValue;
			
			$sql = "SELECT *FROM tbm_obat_hrg_khusus WHERE id_obat='$idObat'";
			$arr=$this->queryAction($sql,'S');
			
			//cek apakah obat yg dipilih sudah masuk dalam tabel obat harga khusus
			if(count($arr) == 0) //jika obat belum ada di tabel obat harga khusus
			{
				$sql = "SELECT MAX(id) AS id FROM tbt_obat_harga WHERE kode='$idObat'";
				$idTbtObatHarga = HrgObatRecord::finder()->findBySql($sql)->id;
					
				//cek hrg_ppn_disc
				//jika tidak nol, pakai hrg_ppn_disc untuk harga beli
				
				$hrgPpnDisc = HrgObatRecord::finder()->findByPk($idTbtObatHarga)->hrg_ppn_disc;
				if($hrgPpnDisc == 0)
				{
					$hrgBeli = HrgObatRecord::finder()->findByPk($idTbtObatHarga)->hrg_ppn;					
				}
				else
				{
					$hrgBeli = HrgObatRecord::finder()->findByPk($idTbtObatHarga)->hrg_ppn_disc;
				}
				
				$this->hrgBeli->Text=$hrgBeli;
				
				//$this->test->text=$this->DDObat->SelectedValue;
				$this->hrg->focus();
				$this->cekObatMsg->Text= ''; 
			}
			else
			{
				$this->cekObatMsg->Text= 'Obat yang dipilih sudah masuk dalam daftar obat harga khusus'; 
				$this->DDObat->SelectedIndex= -1;
			}
			
			
		}
		else
		{
			$this->hrgBeli->Text='';
			$this->hrg->Text='';
			$this->DDObat->focus();
		}
		
		$this->errMsg->Visible = false; 		
	}
	
	public function checkRM($sender,$param)
    {       
		// valid if the id kabupaten is not found in the database
		$param->IsValid=(ObatRecord::finder()->findByPk($this->ID->Text)===null);
    }
	
	public function nextPoin($sender,$param)
    {       
		// valid if the id kabupaten is not found in the database
		$this->ID->focus();
    }
	
	protected function collectSelectionListResult($input)
	{
		$indices=$input->SelectedIndices;		
		foreach($indices as $index)
		{
			$item=$input->Items[$index];
			return $index;
		}		
	}		
	
	public function makeID($varTable,$activeTable)
    {			
		//Mbikin No Urut
		$find = $activeTable;//::finder();		
		$sql = "SELECT id FROM " . $varTable . " order by id desc";
		
		$num = $find->findBySql($sql);
		if($num==NULL)//jika kosong bikin ndiri
		{
			$urut='0001';			
		}
		else
		{	
			$urut = substr($num->getColumnValue('id'),1,4);				
			$urut = $urut + 1;				
			$tmp=substr('0000',0,4-strlen($urut)).$urut;
		}
		return $tmp;
	}
	
	public function simpanClicked($sender,$param)
	{			
		if($this->IsValid)  // when all validations succeed
        {
			if($this->hrg->Text >= $this->hrgBeli->Text) //jika harga jual >= harga beli
			{
				$this->errMsg->Visible = false; 
				
				$noUrut = $this->makeID('tbm_obat_hrg_khusus',HrgObatKhususRecord::finder());
			
				$idObat = $this->DDObat->SelectedValue;
				
				//$sql = "SELECT AS id FROM tbt_obat_harga WHERE kode='$idObat'";
				//$arr = $this->queryAction($sql,'S');
				//$idTbtObatHarga = HrgObatRecord::finder()->findBySql($sql)->id;
					
				//cek hrg_ppn_disc
				//jika tidak nol, pakai hrg_ppn_disc untuk harga beli
				/*
				$hrgPpnDisc = HrgObatRecord::finder()->findByPk($idTbtObatHarga)->hrg_ppn_disc;
				if($hrgPpnDisc == 0)
				{
					$hrgBeli = HrgObatRecord::finder()->findByPk($idTbtObatHarga)->hrg_ppn;					
				}
				else
				{
					$hrgBeli = HrgObatRecord::finder()->findByPk($idTbtObatHarga)->hrg_ppn_disc;
				}
				*/
				
					
				// populates a UserRecord object with user inputs
				$ObatRecord=new HrgObatKhususRecord;
				$ObatRecord->id='K'.$noUrut;
				$ObatRecord->id_obat=ucwords($this->DDObat->SelectedValue);
				$ObatRecord->hrg_jual=TPropertyValue::ensureInteger($this->hrg->text);
				//$ObatRecord->hrg_beli=$hrgBeli;
				$ObatRecord->tgl=date('Y-m-d');
				$ObatRecord->operator=$this->User->IsUserNip;
				$ObatRecord->ket='';
				
				// saves to the database via Active Record mechanism
				$ObatRecord->save(); 			
				// redirects the browser to the homepage
				$this->Response->redirect($this->Service->constructUrl('Farmasi.ObatKhusus'));
			}
			else //jika harga jual < harga beli
			{
				$this->errMsg->Visible = true; 
				$this->hrg->focus();
			}
        }	
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('', 'unmaskContent();');
		}		
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.ObatKhusus'));		
	}
}
?>