<?php
/**
 * Auto generated by prado-cli.php on 2008-05-14 05:27:45.
 */
class TarifVisiteRwtInapRecord extends TActiveRecord
{
	const TABLE='tbm_inap_visite';

	public $id;
	public $kel_dokter;
	public $kelas;
	public $tarif;
	
	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>