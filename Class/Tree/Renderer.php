<?php
class Class_Tree_Renderer
{
    public function run($branch)
    {
        echo "<ul>"."\n";
        foreach($branch->getChildren() as $cBranch) {
            $id = $cBranch->getId();
            $pid = $cBranch->parentId;
            echo "<li id='".$id."' parentId='".$pid."'>"."<span>".$cBranch->label.'</span>'."\n";
            if($cBranch->hasChildren()) {
                echo $cBranch->render();
            }
            echo "</li>"."\n";
        }
        echo "</ul>"."\n";
    }
}