<?php
/**
 * Auto generated by prado-cli.php on 2008-05-23 05:36:27.
 */
class FilmMasukEmbelRecord extends TActiveRecord
{
	const TABLE='tbt_film_masuk_embel';
	
	public $id;
	public $no_po;
	public $jml_fktr;
	public $no_fktr;
	public $tgl_fktr;
	public $tgl_jth_tempo;
	public $tgl_trima_brg;
	public $materai;
	public $ppn;
	public $pot;
	
	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>
