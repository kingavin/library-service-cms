<?php
    class Class_Report_Sale extends Class_Report_Abstract 
    {
            function report()
        {
            $option="";
            switch($this->option)
            {
                case 'bar':
                    if($this->datePeriod!='')
                    {
                        $datePeriod = "";
                        switch($this->datePeriod)
                        {
                            case 'hour':
                            case 'date':
                            case 'week':
                            case 'month':
                            case 'quarter':
                            case 'year':    
                                  $datePeriod = strtoupper($this->datePeriod);
                                  break;                                   
                            default :
                                $datePeriod = "DATE";
                        }
                        
                        if( $datePeriod!='YEAR' )
                            $datePeriodStr = $datePeriod . "(orders.created), DATE(orders.created), YEAR(orders.created)";
                        else
                            $datePeriodStr = " YEAR(orders.created) ";
                        $option = " GROUP BY $datePeriodStr  ORDER BY  DATE(orders.created),$datePeriod";
                    }
                    else
                    {
                        $option = " GROUP BY DATE(orders.created)  ORDER BY DATE(orders.created) ";
                    }
                    break;
                case 'pie':
                default:
                    $option=" GROUP BY p.entityTypeId ORDER BY p.entityTypeId ";
            }
            $where=array();
            if($this->dateStart!='') $where[] = " DATE(orders.created) >= '".$this->dateStart."'";
            if($this->dateEnd!='') $where[] = " DATE(orders.created) <= '".$this->dateEnd."'";
            if($this->paid!='') $where[] = " orders.paid = ".intval($this->paid);
            if($this->status !='' )
                $where[] = " orders.status = '".$this->status."'";
            else
                $where[] = " orders.status != 'cancelled'";     
            $whereStr = implode(' AND ',$where);
            $whereStr = ' WHERE '.$whereStr;
            $db=Zend_Registry::get("dbAdaptor");
            $query = "  select orders_item.productId, 
												p.name, 
												p.sku,
												HOUR(orders.created) as hour,
												WEEK(orders.created) as week,
												MONTH(orders.created) as month,
												QUARTER(orders.created) as quarter,
												YEAR(orders.created) as year,
												c.entityId as categoryId,
												c.name as categoryName, 
												DATE(orders.created) as date, 
												sum(orders_item.qty) as total_qty, 
												sum(orders_item.itemPrice*orders_item.qty) as total
        							from orders_item
        							left join orders  on orders.id=orders_item.orderId
        							left join product_entity as p on p.entityId=orders_item.productId
        							left join category_entity as c on c.entityId=p.entityTypeId
											 ".$whereStr."
											 $option;";
	        //die($query);										 							 
            $data=$db->fetchAll($query);
            return $data;
        }
        
        function pie()
        {
           $report = $this->report();
           $userData=' [';
           $i=1;
           if(is_array($report)){
               foreach($report as $k =>$v)
                {
                    if($i==1) $userData .= '[\''.$v['categoryName'].'\','.$v['total'].']';
                    else  $userData .=  ',[\''.$v['categoryName'].'\','.$v['total'].']';
                    $i++;
                }
           }
           $userData.='] ';
           return $userData;
        }
        
        function bar()
        {
            $report = $this->report();
            $userData=' ['; 
		    $i=1;
		    
		    if(is_array($report)){
    		    foreach($report as $k =>$v)
    		    {
    		        
    		        if($i==1) $dot="";
    		        else $dot=" , ";
    		        switch($this->datePeriod)
    		        {
                        case 'hour':
                              $userData .=  $dot.'[\''.$v['date'].' '.substr(strtoupper($this->datePeriod),0,1).' '.$v[strtolower($this->datePeriod)].'\','.$v['total'].']';
                              break;
                        case 'week':
                        case 'month':
                        case 'quarter':
                              $userData .=  $dot.'[\''.$v['year'].' '.substr(strtoupper($this->datePeriod),0,1).' '.$v[strtolower($this->datePeriod)].'\','.$v['total'].']';
                              break;
                        case 'year':
                              $userData .=  $dot.'[\''.$v['year'].'\','.$v['total'].']';
                              break;
                        case 'date':                              
                        default:
                              $userData .=  $dot.'[\''.$v['date'].'\','.$v['total'].']'; 		                
    		        }
    		        $i++;
    		    }
		    }
		    $userData .='] ';
		    return $userData;
        }
    }
