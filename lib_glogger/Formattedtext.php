<?php
defined('_JEXEC') or die;
/**
* @package   Joomla
* @subpackage glogger
* @version   1.0.0 November, 2016
* @author    Greg Podesta
* @copyright Copyright (C) 2016 Greg Podesta
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*
* This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
*
Thanks to Harry Shaw at http://freestyle-joomla.com for his GREAT work that prompted this idea, and the basis for the table layout used
CREATE TABLE `#__glogger` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL COMMENT 'Title to identify the session',
  `logtime` datetime NOT NULL COMMENT 'Creation date/time of gLogger Object',
  `created_by` int(11) DEFAULT NULL COMMENT 'User ID',
  `remote_addr` varchar(39) DEFAULT NULL COMMENT 'Remote Address ip4/vp6',
  `identifier` char(32) NOT NULL COMMENT 'Joomla SessionID or Unique across gLog Saves',
  `source` varchar(50) DEFAULT NULL COMMENT 'Where the logging was done from, ie.e Cron, API, User...',
  `table_name` varchar(50) DEFAULT NULL COMMENT 'If the logger Table-if-interest was set',
  `table_id` int(11) DEFAULT NULL COMMENT 'Record ID for Table-of-interest',
  `textlog` mediumtext NOT NULL COMMENT 'The extracted Text of the Log',
  `data` longtext NOT NULL COMMENT 'Raw data collected during logging session',
  `flagged_by` int(11) DEFAULT '0' COMMENT 'Flagged-by userid to prevent purging',
  `ref_num` varchar(50) DEFAULT NULL COMMENT 'Reference Number',
  `logs_count` int(11) DEFAULT '0' COMMENT 'How many Log Entries',
  `data_count` int(11) DEFAULT '0' COMMENT 'How many Data Entires',
  PRIMARY KEY (`id`),
  KEY `idxIdentifier` (`identifier`),
  KEY `idxTableRow` (`table_name`,`table_id`),
  KEY `idxRefnum` (`ref_num`),
  KEY `idxSource` (`source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

CREATE TABLE `#__glogger_audit` (
  `id` int(11) unsigned NOT NULL,
  `title` varchar(250) NOT NULL COMMENT 'Title to identify the session',
  `logtime` datetime NOT NULL COMMENT 'Creation date/time of gLogger Object',
  `created_by` int(11) DEFAULT NULL COMMENT 'User ID',
  `remote_addr` varchar(39) DEFAULT NULL COMMENT 'Remote Address ip4/vp6',
  `identifier` char(32) NOT NULL COMMENT 'Joomla SessionID or Unique across gLog Saves',
  `source` varchar(50) DEFAULT NULL COMMENT 'Where the logging was done from, ie.e Cron, API, User...',
  `table_name` varchar(50) DEFAULT NULL COMMENT 'If the logger Table-if-interest was set',
  `table_id` int(11) DEFAULT NULL COMMENT 'Record ID for Table-of-interest',
  `textlog` mediumtext NOT NULL COMMENT 'The extracted Text of the Log',
  `data` longtext NOT NULL COMMENT 'Raw data collected during logging session',
  `flagged_by` int(11) DEFAULT '0' COMMENT 'Flagged-by userid to prevent purging',
  `ref_num` varchar(50) DEFAULT NULL COMMENT 'Reference Number',
  `logs_count` int(11) DEFAULT '0' COMMENT 'How many Log Entries',
  `data_count` int(11) DEFAULT '0' COMMENT 'How many Data Entires',
  PRIMARY KEY (`id`),
  KEY `idxIdentifier` (`identifier`),
  KEY `idxTableRow` (`table_name`,`table_id`),
  KEY `idxRefnum` (`ref_num`),
  KEY `idxSource` (`source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
*/
class gLogHelper {
    static function getGlog($IdEntifier=0){
        // Returns the plain text log
        if(!$IdEntifier) return false;
        $db = jfactory::getDbo();
        $query = $db->getQuery(true)
        ->select('textlog')->from('#__glogger AS a');
        if(is_numeric($IdEntifier)){
            $where = "a.id='{$IdEntifier}'";
        }else{
            $where = "a.identifier='{$IdEntifier}'";
        }
        $query->where($where);
        $db->setQuery($query);
        if( !$result222 = $db->loadColumn()){
            return false;
        }

        $text = '';
        foreach($result222 as $textlog){
            $text .= $textlog;
        }
        $getGlog = $text;
        return $getGlog;

    }
    static function getGlogger($IdEntifier=0, $json = false){
        // Returns the gLogger Object, optionally json encoded
        if(!$IdEntifier) return false;
        $db = jfactory::getDbo();
        $query = $db->getQuery(true)
        ->select('data')->from('#__glogger AS a');
        if(is_numeric($IdEntifier)){
            $query->where("a.id='{$IdEntifier}'");
        }else{
            $query->where("a.identifier='{$IdEntifier}'");
        }
        $db->setQuery($query);
        if( !$log = $db->loadResult() ){
            return false;
        }
        $getGlogger = unserialize($log);
        if($json) $getGlogger = json_encode($getGlogger);
        return $getGlogger;
    }
    static function getGlogsWhere($config = array()){
        // Returns an array of gLogs metadata that match criteria, indexed by the index of #__glogger
        $where      = '1=1';
        $order      = 'logtime';
        $direction  = 'ASC';
        $limit      = null;
        $format     = 'I';                                      //I=string of matching gLogger IDS, else default of plain text logs
        extract($config, EXTR_IF_EXISTS);
        $format     = $format=='I'          ? $format       : 'T';                 // If IDs not specified, fallback to Plain Text
        $direction  = $direction=='DESC'    ? $direction    : 'ASC';   // If IDs not specified, fallback to Plain Text

        $db = jfactory::getDbo();
        $query = $db->getQuery(true);
        if($format=='T'){
            $query->select('textlog');
        }else{
            $query->select('GROUP_CONCAT(id)');
        }
        $query->from('#__glogger');
        if($where) $query->where($where);
        if($order) $query->order("{$order} {$direction}");
        if($limit && is_numeric($limit)) $query->setLimit($limit);

        $getGlogsWhere = new stdClass();
        $getGlogsWhere->sql = trim( preg_replace('/\s+/', ' ', strip_tags($query->dump()) ));
        $query = $getGlogsWhere->sql;
        //        echo 'Line '.__LINE__.' of '.__FILE__.'<pre>'.print_r($query,true).'</pre>';
        try {
            $db->setQuery($query);
            if($format=='I'){
                if( !$result = $db->loadResult()){
                    //                    echo 'Line '.__LINE__.' of '.__FILE__.'<pre>'.print_r($getGlogsWhere,true).'</pre>';                die;
                    return false;
                }
                $getGlogsWhere->result = $result;

            }else{
                //                $result = $db->loadObjectList();
                if( !$result = $db->loadColumn()){
                    //die;
                    //                if( !$result = $db->loadAssocList()){
                    //                    echo 'Line '.__LINE__.' of '.__FILE__.'<pre>'.print_r($getGlogsWhere,true).'</pre>';                die;
                    return false;
                }
                //                echo 'Line '.__LINE__.' of '.__FILE__.'<pre>'.print_r($result,true).'</pre>';                die;
                $text = '';
                foreach($result as $textlog){
                    $text .= $textlog;
                }
                $getGlogsWhere->result = $text;
            }
            $getGlogsWhere->success = true;
            //            echo 'Line '.__LINE__.' of '.__FILE__.'<pre>'.print_r($getGlogsWhere,true).'</pre>';                die;
            return $getGlogsWhere;
        }
        catch (Exception $e) {
            echo '<pre>Line '.__LINE__. ' of ' . __FILE__.'<br/>'.print_r( trim(strip_tags($query->dump()) ),true).'</pre>';
            echo '<pre>Line '.__LINE__. ' of ' . __FILE__.'<br/>'.print_r( $e ,true).'</pre>';die;
            $getGlogsWhere->result = $e->getMessage();
            $getGlogsWhere->success = false;
        }
        return $getGlogsWhere;
    }
}
class gLogger extends JLogLoggerFormattedtext
{
    public $id = null;
    public $identifier;                                     // Unique Identifier to group multiple glogger sessions (using save/reset)
    public $source;                                         // Is log entry from a Cron?
    public $title           = 'Untitled gLogger';           // Your Title for the Log entries being collected
    public $table_name;                                     // Joomla Table of Interest
    public $table_id;                                       // Joomla Table Record ID of Interest
    public $create_time;                                    // When Logging Started
    public $save_time;                                      // When Logging was saved
    public $created_by;
    public $remote_addr;

    // Defaults if com_glogger not installed
    private $newline        = PHP_EOL;                      // New Line charactes to use for text Log
    private $jAddentry      = false;                        // Toggle Native Joomla jLogger write-to-file (error.php)
    private $purgeDays      = 30;                           // Number of days to keep logs, 0 to keep forever
    private $audit_tables   = array();                      // Array of tables to add audit record for, when logging with row ID

    private $saveonadd      = false;                        // Save the gLog object each time an entry or data item is added
    private $forceSave      = true;                         // Toggle whether to automatically save on die (__destruct)
    public $gLogEntries     = array();                      // Log Entries collected
    public $gLogDatas       = array();                      // Data items of interest collected

    protected $format = "{DATETIME}\t{CLIENTIP}\t{SCRIPT}\t{PRIORITY}\t{CATEGORY}\t{MESSAGE}";

    public function __construct($options = null){
        if(!$options || !is_array($options)) $options = array();
        $this->create_time   = new JDate('now');
        $this->created_by   = JFactory::getUser()->id;
        $this->remote_addr  = $_SERVER['REMOTE_ADDR'];

        // If com_glogger installed, get config values
        if (JComponentHelper::isEnabled('com_glogger', true)){
            $params = JComponentHelper::getParams('com_glogger');
            $this->saveonadd        = $params->get('saveonadd', false);
            $this->jAddentry        = $params->get('jAddentry', false);
            $this->purgeDays        = $params->get('purgeDays', 30);

            // Alerts
            $this->sendalert        = false;
            $this->alert_email      = $params->get('alert_email', null);
            $this->alert_priorities = $params->get('alert_priorities', array());   // Array

        }
        if(!is_array($options)) $options=array($options);
        if(array_key_exists('native_addentry',$options))    $this->jAddentry    = $options['native_addentry'];
        if(array_key_exists('glogger_source',$options))     $this->source       = $options['glogger_source'];
        if(array_key_exists('glogger_title',$options))      $this->title        = $options['glogger_title'];

        if( !$this->identifier = JFactory::getSession()->getId() ){
            $this->identifier = strtoupper( md5(uniqid(rand(), true)) );
        }

        // Get Audit Tables
        if(isset($params) && $parm = $params->get('audit_tables',false) ) {
            $parm = json_decode( $parm );
            $audit_tables = $parm->tables;
            if(count($audit_tables)){
                $db = JFactory::getDbo();
                foreach($audit_tables as $k=>$v){
                    $this->audit_tables[] = $db->replacePrefix($v, $prefix = '#__');
                }
            }
        }
        parent::__construct($options);
        // If com_glogger installed, get config values
        if (isset($params) && $params->get('messageformat', false) ){
            $this->format    = '{'.implode("}\t{", $params->get('messageformat')) .'}';
        }
    }
    public function __destruct() {
        if($this->forceSave) self::save();
        // Self Cleaning
        if($this->purgeDays){
            $this->purgeDays = abs($this->purgeDays);
            $db = jfactory::getDbo();
            $db->setQuery( "DELETE FROM #__glogger WHERE flagged_by=0 AND `logtime` < NOW() - INTERVAL {$this->purgeDays} DAY" );
            $db->execute();
        }
        return;
    }
    public function __toString() {
        return print_r($this, true);
    }
    public function __wakeup(){
        $this->forceSave = false;   // Prevents resaving a new record when unserialized;
    }

    public function set($var, $value){
        if( property_exists ( $this , $var ) ){
            $this->$var = $value;
        }
    }
    public function setSource( $source = null ) {
        if(isset($source) && is_string($source)) $this->source = $source;
        return $this;
    }
    public function setTitle( $title = null ) {
        if(isset($title) && is_string($title)) $this->title = $title;
        return $this;
    }
    public function setTable( $table_name = null, $table_id = null ) {
        if( isset($table_name) && is_string($table_name) ) {
            if(!empty($this->table_name) && $this->table_name != $table_name){
                // New Table? - Save the entry that has the old table specified for future queries
                $this->save();
            }
            $db = JFactory::getDbo();
            $table_name = $db->replacePrefix($table_name, $prefix = '#__');
            $this->table_name = $table_name;
            if(!isset($table_id)){
                $this->table_id = null; // If changing table, don't want to carry an old ID around
            }
        }
        if( isset($table_id) && is_numeric($table_id) ) $this->table_id = (int) $table_id;
        return $this;
    }
    public function setTableID( $table_id = null ) {
        if(!empty($this->table_id) && $this->table_id != $table_id){
            // New Table ID? - Save the entry that has the old table specified for future queries
            $this->save();
        }
        if( isset($table_id) && is_numeric($table_id) ) {
            $this->table_id = (int) $table_id;
        }
        return $this;
    }
    public function setNewline( $newline = PHP_EOL ) {
        if(isset($newline) && is_string($newline)) $this->newline = $newline;
        return $this;
    }

    public function logEntry( $message, $priority = JLog::INFO, $category = '', $date = null ) {
        if(is_string($message)) {
            if(!strlen(trim($message))) $message = 'Blank logEntry';
            $message = new JLogEntry($message, $priority, $category, $date);
        }
        if(!is_object($message)) {
            return $this;
        }
        if(in_array($priority, $this->alert_priorities)) {
            $this->sendalert = true;
        }

        if($this->jAddentry) {
            parent::addEntry( clone $message );    // Use clone because the parent method converts the date from datetime object to a string date
        }
        $this->gLogEntries[] = $message;                            // Add to this Array
        if($this->saveonadd) $this->save();                         // Save after each addition
        return $this;
    }
    public function logData( $data = null ) {
        if(!$data) return;
        $this->gLogDatas[] = $data;         // Add to this Array
        if($this->saveonadd) $this->save(); // Save after each addition
        return $this;
    }

    public function save($SaveOnDie = true) {
        if(!$SaveOnDie){
            $this->forceSave = false;
            //            return $this;
        }
        $this->save_time = new JDate('now');
        $obj = new stdClass();
        $obj->id            = null;
        $obj->created_by    = $this->created_by;
        $obj->remote_addr   = $this->remote_addr;
        $obj->identifier    = $this->identifier;
        $obj->source        = $this->source;
        $obj->title         = $this->title;
        $obj->table_name    = $this->table_name;
        $obj->table_id      = $this->table_id;
        $obj->logs_count    = count($this->gLogEntries);
        $obj->data_count    = count($this->gLogDatas);
        $obj->logtime       = $this->create_time->format('Y-m-d H:i:s');
        $obj->data          = serialize( $this );
        $obj->textlog        = $this->_getTextLog();
        $db = jfactory::getDbo();
        $db->insertObject('#__glogger',$obj,'id');
        $this->id = $db->insertid();

        if(!empty($this->table_name)
        && !empty($this->table_id)
        && count($this->audit_tables)
        && in_array( $this->table_name, $this->audit_tables) ) {
            $db->insertObject('#__glogger_audit',$obj,'id');
        }
        $this->gLogEntries  = array();  // Reset Entries for next save
        $this->gLogDatas    = array();  // Reset Data for next save

        if($this->sendalert && isset($this->alert_email )){
            $mailer = JFactory::getMailer();
            $config = JFactory::getConfig();
            $sender = array(
                $config->get( 'mailfrom' ),
                $config->get( 'fromname' )
            );
            $mailer->setSender( $sender );
            $mailer->addRecipient( $this->alert_email );

            $mailer->setSubject("gLog Alert [{$this->id}]: ".$this->title);
            $body = print_r($this,true);
            $mailer->setBody($body);

            $send = $mailer->Send();

        }

        return $this;
    }
    public function getTextLog() {
        return $this->_getTextLog();
    }
    private function _getTextLog(){

        $table_name = $this->table_name ? " {$this->table_name} "   : '';
        $table_id   = $this->table_id   ? " #{$this->table_id} "    : '';
        if(count($this->gLogDatas)==0){
            $datacount = 'No data items were';
        }elseif(count($this->gLogDatas)==1){
            $datacount = count($this->gLogDatas).' data item was';
        }else{
            $datacount = count($this->gLogDatas).' data items were';
        }

        $log = substr( "#-{$table_name}-{$table_id}--- ({$datacount} saved with log) ".str_repeat('-',100), 0 , 100 ).$this->newline;
        foreach($this->gLogEntries as $k => $entry){
            // If the time field is missing or the date field isn't only the date we need to rework it.
            if ((strlen($entry->date) != 10) || !isset($entry->time))
            {
                // Get the date and time strings in GMT.
                //                $entry->datetime = $entry->date->toISO8601();
                //$entry->datetime = $entry->date->format('Y-m-d H:i:s T',true);
                $entry->datetime = $entry->date->format('Y-m-d H:i:s T');
                $entry->time = $entry->date->format('H:i:s', false);
                $entry->date = $entry->date->format('Y-m-d', false);

            }
            // Get a list of all the entry keys and make sure they are upper case.
            $tmp = array_change_key_case(get_object_vars($entry), CASE_UPPER);
            // Decode the entry priority into an English string.
            $tmp['PRIORITY'] = $this->priorities[$entry->priority];
            $tmp['CLIENTIP'] = $this->remote_addr;
            $last = $entry->callStack[1];
            $tmp['SCRIPT'] = basename($last['file']).' Line '.$last['line'];
            // Fill in field data for the line.
            $line = $this->format;
            foreach ($this->fields as $field){
                $line = str_replace('{' . $field . '}', (isset($tmp[$field])) ? $tmp[$field] : '-', $line);
            }
            $log .= $line.$this->newline;
        }
        return   $log;
    }
    public function getLog($id = 0){
        if(!$id) return false;
        $db = jfactory::getDbo();
        $query = $db->getQuery(true)
        ->select('textlog')->from('#__glogger AS a')->where("a.id='{$id}'");
        $db->setQuery($query);
        if( !$log = $db->loadResult() ){
            $log = null;
        }
        return $log;
    }
    public function getLogsMerged($id = null){
        if(!$id) return false;
        $db = jfactory::getDbo();
        $query = $db->getQuery(true)
        ->select('textlog')->from('#__glogger AS a')->where("a.id='{$id}'");
        $db->setQuery($query);
        if( !$log = $db->loadResult() ){
            $log = null;
        }
        return $log;
    }
}
?>
