<?php
/**
 * Auto generated by prado-cli.php on 2008-04-28 06:05:46.
 */
class IgdRecord extends TActiveRecord
{
	const TABLE='tbt_igd';

	public $cm;
	public $no_trans;	
	public $tgl_visit;
	public $wkt_visit;
	public $cr_masuk;
	public $dokter;
	public $keluhan;
	public $icd;
	public $catatan;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>