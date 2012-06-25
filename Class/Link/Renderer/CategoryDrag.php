<?php
class Class_Link_Renderer_CategoryDrag extends Class_Link_Renderer_Abstract
{
    public function run($link)
    {
        echo "<ul>"."\n";
        echo "<li class='empty group dropable'></li>";
        foreach($link->getChildren() as $cLink) {
            $id = $cLink->getId();
            $pid = $cLink->getParentId();
            echo "<li class='solid dropable' id='".$id."' parentId='".$pid."'>";
            echo "<div class='item-container'>";
            echo "<div class='handle' draggable='true'></div>"."<div class='label'>".$cLink->label.'</div>'."\n";
            echo "<div class='action'><a class='link' href='/admin/category/edit/id/".$id."'>操作</a></div>";
            echo "<div class='clear'></div>";
            echo "</div>";
            if($cLink->hasChildren()) {
                echo $cLink->render();
            }
            echo "</li>"."\n";
            echo "<li class='empty group dropable'></li>";
        }
        echo "</ul>"."\n";
    }
}