<?php
/**
 * Auto generated by prado-cli.php on 2008-09-19 03:21:53.
 */
class RwtJalanSisaDiscRecord extends TActiveRecord
{
	const TABLE='tbt_rawat_jalan_sisa_disc';

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