<?php
interface Class_Tree_Node
{
	public function appendChild(Class_Tree_Node $row);
	public function setParent();
	public function hasChildren();
	public function getChildren();
	public function getId();
	public function getParentId();
	public function getOrder();
}