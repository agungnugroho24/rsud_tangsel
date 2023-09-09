<?php
class ernst extends TPage
{
public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
		$this->DDTempatLahir->DataSource=PoliklinikRecord::finder()->findAll();
		$this->TanggalLahir->Text=date("d-m-Y");  
		$this->DDKelurahan->DataSource=PoliklinikRecord::finder()->findAll();
		$this->DDKelurahan->dataBind();
		$this->DDKecamatan->DataSource=PoliklinikRecord::finder()->findAll();
		$this->DDKecamatan->dataBind();
		$this->DDKebangsaan->DataSource=PoliklinikRecord::finder()->findAll();
		$this->DDKebangsaan->dataBind();
		$this->DDCaraMsk->DataSource=caramasukRecord::finder()->findAll();
		$this->DDCaraMsk->dataBind();
		$this->DDStatusRumah->DataSource=PoliklinikRecord::finder()->findAll();
		$this->DDStatusRumah->dataBind();                     		
    }
   
}
?>