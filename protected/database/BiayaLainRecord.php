<?php
/**
 * Auto generated by prado-cli.php on 2008-06-02 05:12:55.
 */
class BiayaLainRecord extends TActiveRecord
{
	const TABLE='tbm_biaya_lain';

	public $id;
	public $nama;
	public $tarif;	

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>