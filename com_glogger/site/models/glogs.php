<?php
/**
* @author
* @copyright
* @license
*/

defined("_JEXEC") or die("Restricted access");
jimport('glogger.Formattedtext');

/**
* List Model for glogs.
*
* @package     Glogger
* @subpackage  Models
*/
class GloggerModelGLogs extends JModelList
{
    /**
    * Constructor.
    *
    * @param   array  $config  An optional associative array of configuration settings.
    */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'a.title', 'title','state'
            );
        }
        parent::__construct($config);
    }

    /**
    * Method to auto-populate the model state.
    *
    * This method should only be called once per instantiation and is designed
    * to be called on the first call to the getState() method unless the model
    * configuration flag to ignore the request is set.
    *
    * Note. Calling getState in this method will result in recursion.
    *
    * @param   string  $ordering   An optional ordering field.
    * @param   string  $direction  An optional direction (asc|desc).
    *
    * @return  void
    */
    protected function populateState($ordering = 'logtime', $direction = 'DESC')
    {
        // Get the Application
        $app = JFactory::getApplication();
        $menu = $app->getMenu();
//$gLogger = new gLogger($config);
//$gLogger->setTitle('GregTitle')->setSource('gLogs Model')->logEntry('Userid '.$user->id .' executed '.basename(__FILE__));
//$gLogger->logData(  array(1,2,3) );
//$gLogger->logData(  array(1,2,3) );
//$gLogger->logData(  array(1,2,3) );
//$gLogger->setTitle('GregTitle')->setSource('gLogs Model')->logEntry('Userid '.$user->id .' executed '.basename(__FILE__));
        // Set filter state for search
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);


        //    [filter_order] => a.identifier
        //    [filter_order_Dir] => asc
        //$filter_order       = $app->input->get('filter_order','');
        //$filter_order_Dir   = $app->input->get('filter_order_Dir','ASC');
        //$filter_order = $app->getUserStateFromRequest( 'la_filter.'.'fss_filter_language', 'fss_filter_language', 'fss_filter_language', '', 'string' );
        //$g = $app->getUserStateFromRequest('userstatevarname','reqvariablename','default','type');
        $filter_order       = $app->getUserStateFromRequest('list.ordering','filter_order','a.logtime','string');
        $filter_order_Dir   = $app->getUserStateFromRequest('list.direction','filter_order_Dir','DESC','string');
        //echo 'Line '.__LINE__.' of '.__FILE__.'<pre>'.print_r($filter_order,true).'</pre>';
        //echo 'Line '.__LINE__.' of '.__FILE__.'<pre>'.print_r($filter_order_Dir,true).'</pre>';
        $this->setState('list.ordering', $filter_order);
        $this->setState('list.direction', $filter_order_Dir);
        //echo 'Line '.__LINE__.' of '.__FILE__.'<pre>'.print_r($this,true).'</pre>';
        //die;
        // Load the parameters.
        $params = JComponentHelper::getParams('com_glogger');
        $active = $menu->getActive();
        empty($active) ? null : $params->merge($active->params);
        $this->setState('params', $params);

//if(isset($_REQUEST['limit'])) {
//$limit = $app->getUserStateFromRequest('list.limit','limit','10','int');
//$this->setState('list.limit', $limit);
//}
        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
    * Method to get a store id based on model configuration state.
    *
    * This is necessary because the model is used by the component and
    * different modules that might need different sets of data or different
    * ordering requirements.
    *
    * @param   string  $id  A prefix for the store id.
    *
    * @return  string  A store id.
    *
    * @since   1.6
    */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');

        return parent::getStoreId($id);
    }

    /**
    * Build an SQL query to load the list data.
    *
    * @return  JDatabaseQuery
    */
    protected function getListQuery()
    {
        // Get database object
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('a.*, LENGTH(`data`) AS databytes, b.username, c.username AS flag_user')->from('#__glogger AS a');
        //echo 'Line '.__LINE__.' of '.__FILE__.'<pre>'.print_r($this,true).'</pre>';die;
        //$ordering   = $this->getState('list.ordering');
        //$direction   = $this->getState('list.direction');
        //echo 'Line '.__LINE__.' of '.__FILE__.'<pre>'.print_r($filter_order,true).'</pre>';
        //echo 'Line '.__LINE__.' of '.__FILE__.'<pre>'.print_r($this,true).'</pre>';
        //die;

        $search         = $this->getState('filter.search');
        $source         = $this->getState('filter.source');
        $table_name     = $this->getState('filter.table_name');
        $ref_num        = $this->getState('filter.ref_num');
        $ordering       = $this->getState('list.ordering');
        $direction      = $this->getState('list.direction');
        $fullordering   = $this->getState('list.fullordering', 'a.logtime DESC');
        if(empty($fullordering)) $fullordering = 'a.logtime DESC';
        $limit          = $this->getState('list.limit');
        $start          = $this->getState('list.start');
        if(false){
            echo 'Line '.__LINE__.' of '.__FILE__.'<pre>';
            echo "filter.search\t\t". $search.PHP_EOL;
            echo "filter.source\t\t". $source.PHP_EOL;
            echo "filter.table_name\t". $table_name.PHP_EOL;
            echo "list.ordering\t\t". $ordering.PHP_EOL;
            echo "list.direction\t\t". $direction.PHP_EOL;
            echo "list.fullordering\t". $fullordering.PHP_EOL;
            echo "list.limit\t\t". $limit.PHP_EOL;
            echo "list.start\t\t". $start.PHP_EOL;
            //        echo PHP_EOL;
            //        echo "list.ordering\t\t". $filter_order.PHP_EOL;
            //        echo "list.direction\t\t". $filter_order_Dir.PHP_EOL;
            echo '</pre>';
        }
        // Filter by search
        $search = $this->getState('filter.search');
        $s = $db->quote('%'.$db->escape($search, true).'%');

        if (!empty($search))
        {
            if(!is_numeric($search)){
                $search = $db->escape(trim($search));
                $query->where("a.identifier = '{$search}'");
            }else{
                $s = (int) $search;
                $query->where("a.table_id = '{$search}'");
            }
        }
        if(!empty($source)){
            $query->where("(a.source = '{$source}')");
        }
        if(!empty($table_name)){
            $query->where("(a.table_name = '{$table_name}')");
        }
        if(!empty($ref_num)){
            $query->where("(a.ref_num = '{$ref_num}')");
        }

        $query->leftJoin('#__users AS b ON b.id=a.created_by');
        $query->leftJoin('#__users AS c ON c.id=a.flagged_by');

        $sort = $this->getState('list.ordering', 'a.logtime');
        $order = $this->getState('list.direction', 'DESC');
        $query->order($db->escape($sort).' '.$db->escape($order));
        //        $query->order($db->escape($fullordering));


//                echo '<pre>Line '.__LINE__. ' of ' . __FILE__.'<br/>'.print_r( trim(strip_tags($query->dump()) ),true).'</pre>';
//        echo 'Line '.__LINE__.' of '.__FILE__.'<pre>'.print_r($_REQUEST,true).'</pre>';
//        echo 'Line '.__LINE__.' of '.__FILE__.'<pre>'.print_r($this,true).'</pre>';
//        die;
        return $query;
    }

    /**
    * Method to get an array of data items.
    *
    * @return  mixed  An array of data items on success, false on failure.
    *
    * @since   12.2
    */
    public function getItems()
    {
        if ($items = parent::getItems()) {
            //Do any procesing on fields here if needed
        }

        return $items;
    }
}
?>