<?php
/**
 * Auto generated by prado-cli.php on 2008-05-24 12:33:02.
 */
class SatuationRecord extends TActiveRecord
{
	const TABLE='tbm_satuation';

	public $id;
	public $nama;
	public $singkatan;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>