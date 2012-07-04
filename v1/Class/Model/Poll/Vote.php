<?php
class Class_Model_Poll_Vote extends Class_Model_Abstract
{
	public function __construct()
	{
		$this->_init('poll_vote');
	}
	protected function _afterSave()
	{
		$db = Zend_Registry::get('dbAdaptor');
		
		$answer = Class_Core::_('Poll_Answer')
			->setData('answerId',$this->getData('pollAnswerId'))
			->setData('pollId',$this->getData('pollId'))
			->load();
		$db->update('poll_answer',
					array('votesCount'=>$answer->getData('votesCount')+1),
					'answerId = '.$this->getData('pollAnswerId'));
		$poll = Class_Core::_('Poll')
			->setData('pollId',$this->getData('pollId'))
			->load();
		$db->update('poll',
					array('votesCount'=>$poll->getData('votesCount')+1),
					'pollId = '.$this->getData('pollId'));
	}
}