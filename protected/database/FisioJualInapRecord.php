<?php
/**
 * Auto generated by prado-cli.php on 2008-09-19 03:24:32.
 */
class FisioJualInapRecord extends TActiveRecord
{
	const TABLE='tbt_fisio_penjualan_inap';

	public $id;
	public $cm;
	public $no_trans;
	public $no_trans_inap;
	public $id_tindakan;
	public $tgl;
	public $wkt;
	public $harga;
	public $disc;
	public $tanggungan_asuransi;
	public $operator;
	public $flag;
	public $dokter;
	public $klinik;
	public $catatan;
	public $st_bayar;
	
	public $st_rujukan;
	public $id_fisio_rujukan;
	
	public $st_lunas_tunai;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>