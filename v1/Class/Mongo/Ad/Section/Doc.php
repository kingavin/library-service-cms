<?php
class Class_Mongo_Ad_Section_Doc extends App_Mongo_Db_Document
{
	protected $_field = array(
		'label',
		'preview'
	);
	
	public function updatePreview()
	{
		$adDocs = App_Factory::_m('Ad')->addFilter('sectionId', $this->getId())
			->setPageSize(3)
			->setPage(1)
			->addSort('_id', -1)
			->fetchDoc();
		$preview = array();
		foreach($adDocs as $d) {
			$preview[] = $d->filename;
		}
		$this->preview = $preview;
		$this->save();
	}
}