<?php

class cetakEfisiensiBed extends SimakConf
{	
	private $data0 = array();
	private $data1 = array();
	private $labels = array();
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	
	public function onPreRenderComplete($param)
	{		
		parent::onPreRenderComplete($param);
		
		if(!$this->IsPostBack && !$this->IsCallBack)
		{	
			if($this->Request['ID'] && $this->Request['mode'])
			{
				if($this->Request['mode'] == '1')
				{
					$this->judul->Text = 'Grafik Efisiensi Pemakaian Tempat Tidur ';
					$this->frame1->FrameUrl = "index.php?page=Pendaftaran.cetakEfisiensiBedBar&ID=".$this->Request['ID']."&mode=".$this->Request['mode'];
				}
				elseif($this->Request['mode'] == '2')
				{
					$this->judul->Text = 'Grafik Barber Jhonson';
					
					$data = EfisiensiBedRecord::finder()->findByPk($this->Request['ID']);
					
					$thn = $data->tahun;
					$jmlHari = 0;
					for($i=1;$i<=12;$i++)
					{
						$jmlHari += cal_days_in_month(CAL_GREGORIAN,$i,$thn);	
					}
					
					$this->frame1->FrameUrl = 'http://tantos.web.id/grafik_barber_johnson/?bor='.$data->bor.'&avlos='.$data->alos.'&toi='.$data->toi.'&bto='.$data->bto.'&periode='.$jmlHari.'&rs=';
				}
			}
		}
	}	
}

?>
