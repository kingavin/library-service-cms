<?php
class Class_Link_Renderer_FrontDrag extends Class_Link_Renderer_Abstract
{
    public function run($link)
    {
        echo "<ul>"."\n";
        foreach($link->getChildren() as $cLink) {
            $id = $cLink->getId();
            $pid = $cLink->getParentId();
            echo "<li class='solid' id='".$id."' parentId='".$pid."'>";
            echo "<div class='handle'></div>"."<div class='label'>".$cLink->label.'</div>'."\n";
            echo "<div class='action'><a class='link' href='/admin/category/edit/id/".$id."'>操作</a></div>";
            echo "<div class='clear'></div>";
            if($cLink->hasChildren()) {
                echo $cLink->render();
            }
            echo "</li>"."\n";
        }
        echo "</ul>"."\n";
    }
}