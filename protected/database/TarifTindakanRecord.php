<?php
/**
 * Auto generated by prado-cli.php on 2008-05-14 05:27:45.
 */
class TarifTindakanRecord extends TActiveRecord
{
	const TABLE='tbm_tarif_tindakan';

	public $id;	
	public $biaya1;
	public $biaya2;
	public $biaya3;
	public $biaya4;
	
	public $nama;
	public $total;
	public $bhp;
	
	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>