<?php
/**
 * Auto generated by prado-cli.php on 2008-06-02 05:12:55.
 */
class InapTarifOksigenRecord extends TActiveRecord
{
	const TABLE='tbm_inap_tarif_oksigen';

	public $id;
	public $nama;
	public $tarif;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>