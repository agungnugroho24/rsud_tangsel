<?php
class ernst2 extends TPage
{
public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);
		$this->DDKecamatan1->DataSource=PoliklinikRecord::finder()->findAll();
		$this->DDKecamatan1->dataBind();
		$this->DDKabupaten1->DataSource=PoliklinikRecord::finder()->findAll();
		$this->DDKabupaten1->dataBind();
		$this->DDProvinsi1->DataSource=PoliklinikRecord::finder()->findAll();
		$this->DDProvinsi1->dataBind(); 
		                   		
    }
   
}
?>