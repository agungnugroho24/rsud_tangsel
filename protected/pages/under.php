<?php
class under extends SimakConf
{   
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{			
			$sql = "SELECT MAX(cm) AS cm FROM tbd_pasien ORDER BY cm ";
			$arr = $this->queryAction($sql,'S');
			foreach($arr as $row)
			{	
				$maxCm= intval($row['cm']);
			}
			
			$sql = "SELECT cm FROM tbd_pasien ORDER BY cm ";
			$arr = $this->queryAction($sql,'S');
			$i = 0;
			$arr1=array();
			foreach($arr as $row)
			{	
				$cm = intval($row['cm']);
				$arr1[$i]=$cm;
				$i++;
			}

			$data = $this->missingNumbers($arr1,1,$maxCm,true);
			
			foreach( $data as $keyname=>$valuename)
			{
				$this->tes->Text .= '<div>'.substr('000000',-6,6-strlen($valuename)).$valuename.' - '.count($data).'</div><br/>';
			}
			/*
			$i = 0;
			for($missing as $data)
			{
				$this->tes->Text .= $data[$i].', ';	
				$i++;
			}
			*/
			
		}
  }	


	public function missingNumbers($array=array(), $min=false, $max=false, $impose=false){
	if(!is_array($array)){return false;};
	if(!sizeof($array)){return array();};
	sort($array, SORT_NUMERIC);
	$L=sizeof($array)-1;
	$min=(is_bool($min))?$array[0]:(float)$min;
	$max=(is_bool($max))?$array[$L]+1:(float)$max;
	if(!$impose && $max<$array[$L]){$max=$array[$L]+1;};
	if(!$impose && $min>$array[0]){$min=$array[0];};
	return array_values( array_diff(range($min,$max), $array) );
	/*keep this comment to reuse freely
	http://www.fullposter.com/?1*/}

	protected function keluarClicked()
	{
		$this->Response->redirect($this->Service->constructUrl('Simak'));
	}
}
?>
