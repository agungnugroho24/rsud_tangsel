<?php
/**
 * Auto generated by prado-cli.php on 2008-05-14 05:27:45.
 */
class InapAskepRecord extends TActiveRecord
{
	const TABLE='tbt_inap_askep';

	public $id;
	public $cm;
	public $no_trans;
	public $id_tdk;
	public $nama;
	public $tgl;	
	public $wkt;
	public $operator;
	public $flag;
	public $catatan;
	public $tarif;
	
	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>