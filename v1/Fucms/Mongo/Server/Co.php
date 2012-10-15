<?php
namespace Fucms\Mongo\Server;

class Co extends \App_Mongo_Db_Collection
{
	protected $_name = 'server_center';
	protected $_documentClass = 'Fucms\Mongo\Server\Doc';
}