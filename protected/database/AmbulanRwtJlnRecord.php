<?php
/**
 * Auto generated by prado-cli.php on 2008-06-02 05:12:55.
 */
class AmbulanRwtJlnRecord extends TActiveRecord
{
	const TABLE='tbt_rwtjln_ambulan';

	public $id;
	public $cm;
	public $no_trans;	
	public $no_trans_rwtjln;	
	public $tgl;
	public $wkt;
	public $operator;
	public $flag;
	public $catatan;
	public $tarif_dasar;
	public $tarif_tunggu;
	public $tarif;
	public $disc;
	public $tanggungan_asuransi;
	public $tujuan;
	public $jns_pasien;
	public $lainnya;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>