<?
 abstract class Class_Report_Abstract
{
    //array('start'=>'2009-01-01','end'='2009-02-02','period'=>'week day month quarter year')  //string '2009-01-09'
    protected $dateStart;
    protected $dateEnd;
    protected $datePeriod;
    
    protected $userId;
    protected $email;
    
    protected $productId;
    protected $categoryId;
    
    protected $groupBy;
    protected $orderBy;
    protected $option;
    protected $paid;
    protected $status;

    final public function getReport()
    {
        //return $this->report();
        switch($this->option)
        {
            case 'bar':
                return $this->bar();
                break;
            case 'pie':            
            default:
                return $this->pie(); 
        }
    }
    final public function set($settings=array())
    {
        if(is_array($settings))
        {
            foreach($settings as $k => $v)
            {
                if(property_exists($this,$k)) 
                {
                   if (!get_magic_quotes_gpc()) $this->$k = addslashes($v);
                   else  $this->$k = $v;
                }    
            }
        }
    }
    final function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;
    }
    final function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
    }
    final function setDatePeriod($datePeriod)
    {
        $this->datePeriod = $datePeriod;
    }
    final function setUserId($userId)
    {
        $this->userId = $userId;
    }
    final function setEmail($email)
    {
        $this->email = $email;
    }
    
    final function setProductId($productId)
    {
        $this->productId = $productId;
    }
    
    final function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }
    
    final function setGroupBy($groupBy)
    {
        $this->groupBy = $groupBy;
    }
    
    final function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;
    }
    
    final function setOption($option)
    {
        $this->option = $option;
    }
    

    abstract function pie();
    abstract function bar();
    //needed rewrite
    abstract function report();
}