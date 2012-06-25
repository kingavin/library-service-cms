<?php
class Class_Link_Renderer_Default extends Class_Link_Renderer_Abstract
{
	public function run($link)
    {
        echo "<ul class='link-".$link->getId()."'>"."\n";
        $i = 0;
        foreach($link->getChildren() as $cLink) {
        	$i++;
            $id = $cLink->getId();
            $pid = $cLink->getParentId();
            if($link->getId() == 0) {
            	echo "<li id='".$id."' parentId='".$pid."' class='lv-one item-".$i."'>"."<a aid='".$id."' href='".$cLink->getHref()."'>".$cLink->label.'</a>'."\n";
            } else {
            	echo "<li id='".$id."' parentId='".$pid."' class='item-".$i."'>"."<a aid='".$id."' href='".$cLink->getHref()."'>".$cLink->label.'</a>'."\n";
            }
            if($cLink->hasChildren()) {
                echo $cLink->render();
            }
            echo "</li>"."\n";
        }
        echo "</ul>"."\n";
    }
}