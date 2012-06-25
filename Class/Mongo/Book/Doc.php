<?php
class Class_Mongo_Book_Doc extends App_Mongo_Db_Document
{
	protected $_pages;
	protected $_head;
	
	public function readPages()
	{
		$co = App_Factory::_m('Book_Page');
		$this->_head = $co->create(array('label' => 'ROOT', 'parentId' => null));
		
		$bookPageDoc = $co->setFields(array('label', 'parentId', 'sort', 'link'))
			->addFilter('bookId', $this->getId())
			->sort('sort', 1)
			->fetchDoc();
		$this->_pages = $bookPageDoc;
		$this->_buildConnection($this->_head);
		$this->_head->sortChildren();
		return $this;
	}
	
	protected function _buildConnection($parent)
	{
		$count = count($this->_pages);
        if($count > 0) {
            foreach($this->_pages as $i => $parent) {
            	if($parent->parentId == 0) {
            		$this->_head->appendChild($parent);
            	}
                foreach($this->_pages as $j => $child) {
            		if($parent->getId() == $child->parentId) {
            			$parent->appendChild($child);
            			continue;
            		}
                }
            }
        }
        return;
	}
	
	public function buildIndex()
	{
		$naviArr = array();
		$childrenPage = $this->_head->buildNaviArr($naviArr);
		
		return $naviArr;
	}
	
	public function renderPages()
	{
        ob_start();
        $this->_head->render();
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
	}
}