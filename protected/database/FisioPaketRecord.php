<?php
/**
 * Auto generated by prado-cli.php on 2008-05-30 10:00:12.
 */
class FisioPaketRecord extends TActiveRecord
{
	const TABLE='tbm_fisio_paket';

	public $id;
	public $kode;
	public $item;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>