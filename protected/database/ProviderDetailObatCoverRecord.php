<?php
/**
 * Auto generated by prado-cli.php on 2008-03-18 11:59:57.
 */
class ProviderDetailObatCoverRecord extends TActiveRecord
{
	const TABLE='tbm_provider_detail_obat_cover';

	public $id;
	public $id_provider;
	public $id_obat;
	public $plafon;
	public $margin;
	public $nilai_tanggungan;
	public $st;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>