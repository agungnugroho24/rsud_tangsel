<?php
/**
 * Auto generated by prado-cli.php on 2008-05-18 01:27:03.
 */
class MedisRecord extends TActiveRecord
{
	const TABLE='tbd_asset_medis';

	public $id;
	public $nama;
	public $jenis;
	public $deskripsi;
	public $tipe;
	public $merk;
	public $no_seri;
	public $nilai;
	public $tgl_beli;
	public $distributor;
	public $produsen;
	public $jaminan;
	public $lama_jaminan;
	public $detil_jaminan;
	public $penyusutan;
	public $ket;
	public $jumlah;
	public $id_ruang;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>