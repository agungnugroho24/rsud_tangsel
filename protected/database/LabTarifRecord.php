<?php
/**
 * Auto generated by prado-cli.php on 2008-05-29 07:06:08.
 */
class LabTarifRecord extends TActiveRecord
{
	const TABLE='tbm_lab_tarif';

	public $id;
	public $tarif;
	public $tarif1;
	public $tarif2;
	public $tarif3;
	public $tarif4;
	public $tarif5;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>