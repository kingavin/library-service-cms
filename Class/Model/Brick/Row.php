<?php
class Class_Model_Brick_Row extends Zend_Db_Table_Row_Abstract
{
	public function createSolidBrick(Zend_Controller_Request_Abstract $request, $globalParams = '{}')
    {
        $className = $this->extName;
	    
        $folderPath = str_replace('_', '/', $className);
        $fileNameArr = explode('_', $className);
        $fileName = $fileNameArr[count($fileNameArr) - 1];
	    
        if(is_file(CONTAINER_PATH.'/extension/brick/'.$folderPath.'/'.$fileName.'.php')) {
            require_once CONTAINER_PATH.'/extension/brick/'.$folderPath.'/'.$fileName.'.php';
        } else {
            throw new Class_Brick_Exception('Brick file: '.CONTAINER_PATH.'/extension/brick/'.$folderPath.'/'.$fileName.'.php'.' not exist for '.$className);
        }
	    $solidBrick = new $className($this, $request, $globalParams);
	    return $solidBrick;
    }
    
	public function reOrderSiblings()
	{
		$oldPlace = isset($this->_cleanData['order']) ? $this->_cleanData['order'] : 9999;
		$oldPosition = isset($this->_cleanData['position']) ? $this->_cleanData['position'] : null;
		$placeToBe = $this->order;
		$tb = Class_Base::_('Brick');
		
		if($this->order != $oldPlace || $this->position != $oldPosition) {
			if($this->position != $oldPosition && $oldPosition != null) {
				$siblingRowset = $tb->fetchAll($this->select(false)->from($tb, array('brickId', 'order'))
					->where('position = ?', $oldPosition)
					->order('order')
				);
				$i = 1;
				foreach($siblingRowset as $positionRow) {
					if($positionRow->order > $oldPlace) {
						$positionRow->order =  $i - 1;
						$positionRow->save();
					}
					$i++;
				}
				$oldPlace = 9999;
			}
			
			$siblingRowset = $tb->fetchAll($this->select(false)->from($tb, array('brickId', 'order'))
				->where('position = ?', $this->position)
				->order('order')
			);
			
			if($placeToBe < $oldPlace) {
				$placeToBe++;
				$i = 1;
				//item moved up
				foreach($siblingRowset as $positionRow) {
					if($positionRow->order >= $placeToBe && $positionRow->order < $oldPlace) {
						$positionRow->order =  $i + 1;
						$positionRow->save();
					} else {
						if($positionRow->order != $i && $positionRow->brickId != $this->brickId) {
							$positionRow->order = $i;
							$positionRow->save();
						}
					}
					$i++;
				}
				$this->order = $placeToBe;
			} else if($placeToBe > $oldPlace) {
				$i = 1;
				//item moved down
				foreach($siblingRowset as $positionRow) {
					if($positionRow->order <= $placeToBe && $positionRow->order > $oldPlace) {
						$positionRow->order = $i - 1;
						$positionRow->save();
					} else {
						if($positionRow->order != $i && $positionRow->brickId != $this->brickId) {
							$positionRow->order = $i;
							$positionRow->save();
						}
					}
					$i++;
				}
			}
		}
		return $this;
	}
}