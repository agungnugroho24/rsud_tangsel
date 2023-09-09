<?php
Prado::using('Application.modules.ChartDirector.phpchartdir');
class cetakEfisiensiBedBar extends SimakConf
{	
	private $bor = array();
	private $alos = array();
	private $toi = array();
	private $bto = array();
	private $gdr = array();
	private $ndr = array();
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
			//$this->bindGrid();
			 //$c = $this->makeGraphAnglePie();
			//$this->Graph->setGraph($c);
			if($this->Request['ID'])
			{
				$c = $this->makeGraph2($this->Request['ID']);
				$this->Graph->setGraph($c);
			}
		}
	}		
	
	private function makeGraph2($id)
	{
		$data = EfisiensiBedRecord::finder()->findByPk($id);
		
		# The data for the bar chart 
		$bor = array($data->bor_target, $data->bor); 
		$alos = array($data->alos_target, $data->alos); 
		$toi = array($data->toi_target, $data->toi); 
		$bto = array($data->bto_target, $data->bto); 
		$gdr = array($data->gdr_target, $data->gdr); 
		$ndr = array($data->ndr_target, $data->ndr); 
		
		$labels = array("Target", "Tahun ".$data->tahun); 
		/*foreach( $dataJml as $row1 ) {
			$bor[] = $row1['jml'];
			
		}
		
		foreach( $dataRet as $row2 ) {
			$alos[] = $row2['ret'];
			
		}
		
		foreach( $dataLabel as $row3 ) {
			$labels[] = $row3['bln'];
		}*/

		# Create a XYChart object of size 400 x 240 pixels 
		$c = new XYChart(1000, 450); 
		$c->setNumberFormat('.');
		
		# Add a title to the chart using 10 pt Arial font 
		//$c->addTitle("\nGRAFIK Efisiensi Pemakaian Tempat Tidur \n ".$periode."\nRSUD KAB. BELITUNG TIMUR", "", 12); 
		
		# Set the plot area at (50, 25) and of size 320 x 180. Use two alternative background 
		# colors (0xffffc0 and 0xffffe0) 
		$c->setPlotArea(35, 15, 800, 400, 0xffffc0, 0xffffe0); 
		
		# Add a legend box at (55, 18) using horizontal layout. Use 8 pt Arial font, with
		# transparent background 
		$legendObj = $c->addLegend(855, 95, true, "", 12); 
		$legendObj->setBackground(Transparent); 
		
		# Add a title to the y-axis 
		$c->yAxis->setTitle(""); 
		$c->xAxis->setTitle(""); 
		
		# Reserve 20 pixels at the top of the y-axis for the legend box 
		$c->yAxis->setTopMargin(20); 
		
		# Set the x axis labels 
		$c->xAxis->setLabels($labels); 
		$c->xAxis->setLabelStyle("arialbd.ttf", 12);
		# Add a title to the secondary (right) y axis 
		//$c->yAxis2->setTitle("RETRIBUSI"); 
		//$c->yAxis2->setLabelFormat("{value|0.,}");
		
		# set the axis, label and title colors for the primary y axis to green (0x008000) to 
		# match the second data set 
		$c->yAxis->setColors(0x000000, 0x000000, 0x000000); 
		//$c->yAxis2->setColors(0x008000, 0x008000, 0x008000); 
		//$c->yAxis->setAutoScale(0.8, 0.8);
		//$c->yAxis->setLinearScale(0,10000000);
		$c->yAxis->setLinearScale(0, 100, 10);
		
		
		# Add a multi-bar layer with 3 data sets and 3 pixels 3D depth 
		$layer = $c->addBarLayer2(Side, 0); 
		//$layer->addDataSet($bor, 0xff8080, "BANYAKNYA IZIN YANG TERBIT"); 
		//$layer->addDataSet($alos, 0x80ff80, "JML RETRIBUSI"); 
		//$layer->addDataSet($toi, 0x8080ff, "Server #3"); 
		$barLayerObj = $layer->addDataSet($bor, 0x0066CC, "BOR (%)"); 
		$barLayerObj2 = $layer->addDataSet($alos, 0xFF6600, "ALOS (Hari)"); 
		$barLayerObj3 = $layer->addDataSet($toi, 0x339900, "TOI (Hari)"); 
		$barLayerObj4 = $layer->addDataSet($bto, 0x660066, "BTO"); 
		$barLayerObj5 = $layer->addDataSet($gdr, 0xCC9900, "GDR (%)"); 
		$barLayerObj6 = $layer->addDataSet($ndr, 0xFF0000, "NDR (%)"); 
		
		//$barLayerObj2->setUseYAxis2(); 
		//$barLayerObj2->setDataLabelFormat("Rp. {value|0.,}");
		
		# Add labels to the top of the bar
		//$layer->setAggregateLabelStyle("arialbd.ttf", 8, 
		//$layer->yZoneColor(0, 0x000000, 0x000000)); 
		
		$textBoxObj = $layer->setAggregateLabelStyle("helvB.pcf", 12, $layer->yZoneColor(0, 0x000000, 0x000000) );
		$textBoxObj->setBackground(0xffffff, Transparent, 1); 
		
		
		header("Content-type: image/png"); print($c->makeChart2(PNG));
	}

}

?>
