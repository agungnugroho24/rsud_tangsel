<?php
/**
 * Auto generated by prado-cli.php on 2008-03-18 11:59:57.
 */
class ProviderDetailRadRecord extends TActiveRecord
{
	const TABLE='tbm_provider_detail_rad';

	public $id;
	public $id_provider;
	public $id_poli;
	public $id_tindakan;
	public $dokter;
	public $st_dokter;
	public $tarif;
	public $diskon;
	public $tarif_gsm;
	public $st;
	public $st_tarif;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>