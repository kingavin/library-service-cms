<?php
class Class_Tree_DivRenderer extends Class_Tree_Renderer
{
    public function run($branch)
    {
        echo "<ul>"."\n";
        foreach($branch->getChildren() as $cBranch) {
            $id = $cBranch->getId();
            $class = $cBranch->hasChildren ? 'node' : 'leaf';
            echo "<li class='".$class."'>"."<div id='".$id."' class='label'>".$cBranch->label.' <span class=\'info\'>['.$cBranch->getDescription().']</span></div>'."\n";
            if($cBranch->hasChildren()) {
                echo $cBranch->render();
            }
            echo "</li>"."\n";
        }
        echo "</ul>"."\n";
    }
}