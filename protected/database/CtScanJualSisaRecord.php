<?php
/**
 * Auto generated by prado-cli.php on 2008-09-19 03:21:53.
 */
class CtScanJualSisaRecord extends TActiveRecord
{
	const TABLE='tbt_ctscan_jual_sisa';

	public $id;
	public $no_trans;
	public $tgl;
	public $jumlah;	
	
	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>