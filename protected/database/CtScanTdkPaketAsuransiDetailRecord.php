<?php
/**
 * Auto generated by prado-cli.php on 2008-05-24 12:33:02.
 */
class RadTdkPaketAsuransiDetailRecord extends TActiveRecord
{
	const TABLE='tbm_ctscan_tindakan_paket_asuransi_detail';

	public $id;
	public $id_paket;
	public $id_asuransi;
	public $id_tindakan;
	public $tarif;
	public $st_rawat;
	
	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>