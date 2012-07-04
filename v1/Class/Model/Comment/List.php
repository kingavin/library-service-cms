<?php class Class_Model_Comment_List extends Class_Model_Eav_Entity_List_Abstract
{
	public function __construct()
	{
		$this->_init('product_entity_comment');
		$this->setModelName('comment');
	}
	public function getBaseTableName()
    {
        return 'product_entity_comment';
    }
	public function joinProduct($field = array())
    {
        if(is_null($field)) {
            $this->getSelect()->joinLeft(
                array('p' => 'product_entity'),
                'p.entityId = main_table.productId'
            );
        } else if(is_array($field)) {
            $this->getSelect()->joinLeft(
                array('p' => 'product_entity'),
                'p.entityId = main_table.productId',
                $field
            );
        } else {
            throw new Exception('array required for comment list to join product_entity!');
        }
        return $this;
    }
    public function joinReply()
    {
    	$this->getSelect()->joinLeft(
        	array('c' => 'product_entity_comment'),
        	'c.productId = main_table.productId and c.parentId = main_table.id',
        	array('c.content as replyContent','c.created as replyCreated','c.poster as replyPoster')
         )->order('c.created ASC');
         return $this;
    }
    public function joinProductThumb()
    {
        $this->getSelect()->joinLeft(
            array('peg' => 'product_entity_graphic'),
            'main_table.productId = peg.entityId',
            array('value', 'alt')
        )->where("peg.type = 'thumb'");
        return $this;
    }
    public function joinProductRandViewImg()
    {
        $this->getSelect()->joinLeft(
            array('peg' => 'product_entity_graphic'),
            'main_table.productId = peg.entityId',
            array('value', 'alt', 'valueId')
        )->where("peg.type = 'view'")
        ->group('peg.entityId' );
        return $this;
    }
    public function addCategoryFilter(array $cids)
    {
    	$this->getSelect()->joinLeft(
            array('lcp' => 'link_category_product'),
            'main_table.productId = lcp.productId',
            array('lcp.categoryId as categoryId')
        )->where("lcp.categoryId in ( " . implode(',', $cids) . ")");
        return $this;
    }
}