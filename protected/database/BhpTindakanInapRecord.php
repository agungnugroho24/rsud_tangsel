<?php
/**
 * Auto generated by prado-cli.php on 2008-05-30 09:57:05.
 */
class BhpTindakanInapRecord extends TActiveRecord
{
	const TABLE='tbm_bhp_tindakan_inap';

	public $id;
	public $nama;
	public $id_kateg;
	public $tarif;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>
