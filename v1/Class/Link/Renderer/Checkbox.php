<?php
class Class_Link_Renderer_Checkbox extends Class_Link_Renderer_Abstract
{
	protected $_checkedList = array();
	
	public function setChecked(Array $checkedList)
	{
		$this->_checkedList = $checkedList;
	}
	
	public function run($link)
    {
        echo "<ul class='link-".$link->getId()."' style='padding: 0 15px;'>"."\n";
        foreach($link->getChildren() as $cLink) {
            $id = $cLink->getId();
            $pid = $cLink->getParentId();
            echo "<li id='".$id."' parentId='".$pid."'>";
            $checkedString = "";
            if(in_array($id, $this->_checkedList)) {
            	$checkedString = "checked='checked'";
            }
            echo "<input type='checkbox' name='res[".$id."]' id='cb-".$id."' value='1' ".$checkedString."><label for='cb-".$id."'>".$cLink->label."</label>\n";
            if($cLink->hasChildren()) {
                echo $cLink->render();
            }
            echo "</li>"."\n";
        }
        echo "</ul>"."\n";
    }
}