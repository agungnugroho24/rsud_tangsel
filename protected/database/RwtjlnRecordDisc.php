<?php
/**
 * Auto generated by prado-cli.php on 2008-04-27 05:12:10.
 */
class RwtjlnRecord extends TActiveRecord
{
	const TABLE='tbt_rawat_jalan';

	public $cm;
	public $no_trans;
	public $id_klinik;	
	public $tgl_visit;
	public $wkt_visit;
	public $cr_masuk;
	public $dokter;
	public $keluhan;
	public $icd;
	public $catatan;
	public $flag;
	public $st_alih;
	public $discount;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>