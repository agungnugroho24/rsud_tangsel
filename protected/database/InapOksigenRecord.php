<?php
/**
 * Auto generated by prado-cli.php on 2008-06-02 05:12:55.
 */
class InapOksigenRecord extends TActiveRecord
{
	const TABLE='tbt_inap_oksigen';

	public $id;
	public $cm;
	public $no_trans;	
	public $id_tdk;	
	public $tgl;
	public $wkt;
	public $operator;
	public $flag;
	public $catatan;
	public $tarif;
	
	public $disc;
	public $tanggungan_asuransi;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>