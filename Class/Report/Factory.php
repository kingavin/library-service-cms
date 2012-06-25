<?
    class Class_Report_Factory
    {
        static function create($sort)
        {
            $className = 'Class_Report_'.$sort;
            if(class_exists($className))
            {
                return new $className();
            }
            else 
            {
                return null;    
            }
        }
    }