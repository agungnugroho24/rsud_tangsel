<?php
/**
 * Auto generated by prado-cli.php on 2008-09-18 01:53:45.
 */
class KamarKelasRecord extends TActiveRecord
{
	const TABLE='tbm_kamar_kelas';

	public $id;
	public $nama;
	public $persentase;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>