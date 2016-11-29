<?php
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
*/
if (!defined('_JEXEC')) {
    define( '_JEXEC', 1 );
    define('JPATH_BASE', realpath(dirname(dirname(dirname(__FILE__)))));
    require_once ( JPATH_BASE .'/includes/defines.php' );
    require_once ( JPATH_BASE .'/includes/framework.php' );
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
}
require_once JPATH_LIBRARIES . '/import.legacy.php';
require_once JPATH_LIBRARIES . '/cms.php';
require_once JPATH_CONFIGURATION . '/configuration.php';
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$app = JFactory::getApplication('site');
$config = new JConfig;
global $db;
$db = jfactory::getDbo();
$db->setQuery("SELECT template FROM #__template_styles WHERE client_id=1 AND home = 1");
$admin_template = $db->loadResult();

jimport('glogger.Formattedtext');
?>
<!DOCTYPE html>
<html lang="en-gb" dir="ltr">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta charset="utf-8" />
        <meta name="description" content="gLogger Examples" />
        <title>gLogger Examples</title>
        <script src="../../media/jui/js/jquery.min.js"></script>
        <script src="../../components/com_glogger/css/google-code-prettify/prettify.min.js"></script>
        <link rel="stylesheet" href="../../administrator/templates/<?php echo $admin_template; ?>/css/template.css" />
        <link rel="stylesheet" href="../../components/com_glogger/css/google-code-prettify/prettify.min.css" />
        <style type="text/css">
            legend {margin-bottom:0px;}
            .control-group {margin-top:10px !important; margin-bottom:10px !important;}
            div#glog-results {background-color:#EFEC99; border:1px solid #F2D59A; }
            #row-result {background-color: white;}
            #row-result td {font-family:monospace !important;}
            #example-table td {padding-top:1px; padding-bottom:1px;}
            #example-table button {padding-top:1px; padding-bottom:1px;}
            #example-table td:nth-child(1) {
                width:1% !important;
                white-space:nowrap !important;
            }
            td.sample-code {display:none;}
            #code_viewer {
                display:none;
                max-width:600px;
                white-space:nowrap !important;
                line-height:14px !important;
                font-size:12px !important;
                overflow-x:scroll !important;
                overflow-y:auto !important;
            }
            .linenums {margin-left:0px;}
        </style>
        <script type="text/javascript">
            jQuery(document).ready(function() {
                $('#code_viewer').height( parseInt($('#example-table').outerHeight())-20 );
                $('#example-table tr').hover(function() {
                    $('#code_viewer').show();
                    var string = $(this).find('td.sample-code').text().trim();
                    $('#code_viewer').text(  string.replace(/\t\f\v+/g, " ") );
                    $('.prettyprinted').removeClass('prettyprinted');
                    prettyPrint();
                });
            });
        </script>
    </head>
    <body class="admin com_menus view-items layout- task- itemid-" >
        <!-- Top Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="admin-logo " href="../../administrator/index.php"><span class="icon-joomla"></span></a>
                    <div class="nav-collapse collapse">
                        <ul id="menu" class="nav ">
                            <li><a class="no-dropdown menu-component" target="_blank" href="../../index.php?option=com_glogger&amp;view=glogs">Front-End gLogs Viewer</a></li>
                        </ul>
                        <ul id="nav-empty" class="dropdown-menu nav-empty hidden-phone"></ul>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </nav>
        <!-- Header -->
        <header class="header">
            <div class="container-logo">
                <img src="../../administrator/templates/isis/images/logo.png" class="logo" alt="gLogger" />
            </div>
            <div class="container-title">
                <h1 class="page-title">
                    <span class="icon-list menumgr"></span>
                    gLogger : Examples Page</h1>

            </div>
        </header>
        <!-- container-fluid -->
        <div class="container-fluid container-main">
            <section id="content">
                <form action="../../libraries/glogger/examples.php" method="get" name="adminForm" id="adminForm">
                    <!-- Begin Content -->
                    <div class="row-fluid">
                        <div class="span6">
                            <fieldset class="form-horizontal">
                                <legend>Free-form Title/Source Metadata to use</legend>
                                <div class="control-group">
                                    <div class="control-label"> <label id="log_title-lbl" for="log_title">Log Title</label></div>
                                    <div class="controls">      <input name="log_title" id="log_title" size="40" type="text" placeholder="Whatever you would like" value="<?php echo $_GET['log_title'] ?>"></div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label"> <label id="log_source-lbl" for="log_source">Log Source</label></div>
                                    <div class="controls">      <input name="log_source" id="log_source" size="40" type="text" placeholder="Source of the log, i.e. API" value="<?php echo $_GET['log_source'] ?>"></div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="span6">
                            <fieldset class="form-horizontal">
                                <legend>Table and Row Data to use</legend>
                                <strong>No updates to tables are actually performed -</strong> <em>only entries for gLogger tables are created</em>.
                                <div class="control-group">
                                    <div class="control-label"> <label id="log_table-lbl" for="log_table">Table Name</label></div>
                                    <div class="controls">      <input name="log_table" id="log_table" size="40" type="text" placeholder="Any of your Joomla Tables" value="<?php echo $_GET['log_table'] ?>"></div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label"> <label id="log_table_id-lbl" for="log_table_id">Table Row ID</label></div>
                                    <div class="controls">      <input name="log_table_id" id="log_table_id" size="6" type="text" placeholder="A record ID" value="<?php echo $_GET['log_table_id'] ?>"></div>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="row-fluid">
                        <div class="span6">
                            <fieldset class="form-horizontal">
                                <legend>Example Uses</legend>
                                <table id="example-table" class='table table-hover table-condensed table-bordered'><tbody>
                                        <tr>
                                            <td><button name="example" value="1" type="submit" class="btn btn-link">Log the execution of this script</button></td>
                                            <td>Simply submits this page, and logs the fact that the script was executed</td>
                                            <td class="sample-code">
                                                $gLogger = new gLogger($config);
                                                $gLogger->logEntry('Userid '.$user->id .' executed '.basename(__FILE__));
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><button name="example" value="2" type="submit" class="btn btn-link">Use the Title and Source metadata</button></td>
                                            <td>Uses your choice of Title and Source from Example Metadata.</td>
                                            <td class="sample-code">
                                                $gLogger = new gLogger($config);
                                                $gLogger->setTitle($title);
                                                $gLogger->setSource($source);
                                                $gLogger->logEntry('Userid '.$user->id .' executed '.basename(__FILE__));
                                                sleep(2);
                                                $gLogger->logEntry('The script paused for 2 seconds to show timestamp differences');
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><button name="example" value="3" type="submit" class="btn btn-link">Save the current $user object</button></td>
                                            <td>Saves your user record with <code>$gLogger->logData($user)()</code>></td>
                                            <td class="sample-code">
                                                $gLogger = new gLogger($config);
                                                $gLogger->logEntry('Userid '.$user->id .' executed '.basename(__FILE__));
                                                $gLogger->logEntry('The variable type "'.gettype($user).'" is being saved in the database with this gLogger record');
                                                $gLogger->logData($user);
                                                $gLogger->logEntry('The current $user object was saved in the database with this gLogger record');
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><button name="example" value="4" type="submit" class="btn btn-link">Merged Plain Text from Multiple gLogs</button></td>
                                            <td>Saves 3 log entries, and shows the merged Plain text using Session Identifier</td>
                                            <td class="sample-code">
                                                $gLogger = new gLogger($config);

                                                $gLogger->logEntry('Save #1'); sleep(1);
                                                $gLogger->save();

                                                $gLogger->logData('a string variable');
                                                $gLogger->logEntry('Save #2, with string data saved'); sleep(1);
                                                $gLogger->save();

                                                $gLogger->logData(array(1=>'one'));
                                                $gLogger->logData(array(1=>'one',2=>'two'));
                                                $gLogger->logEntry('Save #3 with two arrays saved with they log entry'); sleep(1);
                                                $gLogger->save();
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><button name="example" value="5" type="submit" class="btn btn-link">Saving a native JLog entry</button></td>
                                            <td>Using a JLog Entry instead of a string, using <code>new JLogEntry()</code></td>
                                            <td class="sample-code">
                                                $gLogger = new gLogger($config);
                                                $entry = new JLogEntry("A Joomla jLogger entry being logged with Priority 'JLog::INFO'", JLog::INFO, 'MyJlogCategory');
                                                $gLogger->logEntry($entry);
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><button name="example" value="6" type="submit" class="btn btn-link">Recording a Table and Record</button></td>
                                            <td>Do a series of gLog saves, recording record IDs that are affected.  <Br/>If you have not removed table <span style='font-family:monospace;'>#__glogger_auditing_example</span> from the Component Options, then an audit record will also be created.</td>
                                            <td class="sample-code">
                                                $gLogger = new gLogger($config);
                                                // Set table and id if provided
                                                $gLogger->setTable($log_table, $log_table_id);
                                                $gLogger->logEntry("Saving some remark about table '{$log_table}', #{$log_table_id}");

                                                $different_id = $log_table_id>3 ? rand(1,($log_table_id-1)) : 999999;
                                                $gLogger->setTableID($different_id);
                                                $gLogger->logEntry("Changing the ID for the table, and saving another remark, same table,different ID '{$log_table}', #{$different_id}");

                                                $different_id = $log_table_id>3 ? rand(1,($log_table_id-1)) : 999999;
                                                $gLogger->setTableID($different_id);
                                                $gLogger->logEntry("Another different ID for the table, and saving a remark, same table '{$log_table}', #{$different_id}");

                                                $gLogger->setTable('#__some_other_table', $different_id);
                                                $gLogger->logEntry("Now a different table, that has the same record ID we are working with: '{$log_table}', #{$different_id}");
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><button name="example" value="7" type="submit" class="btn btn-link">Use SQL to select and merge TextLogs</button></td>
                                            <td>After saving several sample logs, merge the last 10 Textlogs from the past 30 minutes</td>
                                            <td class="sample-code">
                                $QueryParms = array(
                                    'where'=>"`logtime` >= NOW() - INTERVAL 10 MINUTE",
                                    'order'=>'id',
                                    'direction'=>'DESC',
                                    'limit'=>'10',
                                    'format'=>'T'   // T for Text Logs, I for string of #__glogger IDs found
                                );
                                $where_output = gLogHelper::getGlogsWhere($QueryParms);
                                if(!$where_output->success) {
                                    $output = 'Constructed SQL failed - did you save logs with table names and IDs yet!!';
                                    $output .= PHP_EOL.$where_output->sql;
                                    $output .= PHP_EOL.$where_output->result;
                                }else{
                                    $output = 'Constructed SQL: ';
                                    $output .= $where_output->sql;
                                    $output .= PHP_EOL.$where_output->result;
                                }
                                            </td>
                                        </tr>
                                    </tbody></table>
                            </fieldset>
                        </div>
                        <div class="span6 clearfix" id="sample-code">
                            <fieldset class="form-horizontal">
                                <legend>Sample PHP</legend>
                                <pre id="code_viewer" class="gLog prettyprint linenums"></pre>
                            </fieldset>
                        </div>
                    </div>
                    <?php
                    $jinput = $app->input;
                    $example = $jinput->getString('example',false);
                    if($example){
                        $user = jfactory::getUser();

                        // Examples Input
                        $title          = $jinput->getString('log_title','Example '.$example);
                        $source         = $jinput->getString('log_source','eg'.$example);
                        $log_table      = $jinput->getString('log_table','No example Table supplied');
                        $log_table_id   = $jinput->getInt('log_table_id','No example Table ID supplied');
                        if(empty($title))           $title          = 'Example '.$example;
                        if(empty($source))          $source         = 'eg'.$example;
                        if(empty($log_table))       $log_table      = 'No example Source provided';
                        if(empty($log_table_id))    $log_table_id   = null;
                        //echo 'Line '.__LINE__.' of '.__FILE__.'<pre>'.print_r($source,true).'</pre>';die;
                        $config = array(
                            'native_addentry'   =>false,
                            //                            'glogger_source'    => "Example script",    // Set source in Config, or with setSource() method
                            //                            'glogger_title'     => 'Untitled Example'       // Set title in Config, or with setTitle() mehtod
                        );

                        switch ($example) {
                            case '1':
                                $gLogger = new gLogger($config);
                                $gLogger->logEntry('Userid '.$user->id .' executed '.basename(__FILE__));
                                sleep(1);
                                $gLogger->save();                           // For Demo, save before the script ends so that we can get an ID to display
                                $example_output = gLogHelper::getGlog($gLogger->id);  // Using the Helper, retrieve the plain text Log of what we just saved
                                break;

                            case '2':
                                $gLogger = new gLogger($config);
                                $gLogger->logEntry("Userid {$user->id} is about to execute Example {$example} in PHP script ".basename(__FILE__));
                                $gLogger->logEntry('We are about to set the Title and Source');
                                $gLogger->logEntry(" - The provided Title is '{$title}'");
                                $gLogger->logEntry(" - The provided Source is '{$source}'");
                                $gLogger->setTitle($title);
                                $gLogger->setSource($source);
                                $gLogger->logEntry('We have set the Title and Source');
                                sleep(1);
                                $gLogger->logEntry('The script paused for 1 second to show timestamp differences');

                                $gLogger->logEntry('Now we are going to do some gLogger stuff for the example script');
                                $gLogger->save();                           // For Demo, save before the script ends so that we can get an ID to display
                                $example_output = gLogHelper::getGlog($gLogger->id);  // Using the Helper, retrieve the plain text Log of what we just saved
                                break;

                            case '3':
                                $gLogger = new gLogger($config);
                                $gLogger->setTitle($title);
                                $gLogger->setSource($source);

                                $gLogger->logEntry('Userid '.$user->id .' executed '.basename(__FILE__));
                                $gLogger->logEntry('The variable type "'.gettype($user).'" is being saved in the database with this gLogger record');
                                $gLogger->logData($user);
                                $gLogger->logEntry('The current $user object was saved in the database with this gLogger record');

                                $gLogger->save();                           // For Demo, save before the script ends so that we can get an ID to display
                                $example_output = gLogHelper::getGlog($gLogger->id);  // Using the Helper, retrieve the plain text Log of what we just saved
                                break;

                            case '4':
                                $gLogger = new gLogger($config);
                                $gLogger->setTitle($title);
                                $gLogger->setSource($source);

                                $gLogger->logEntry('Save #1'); sleep(1);
                                $gLogger->save();                           // For Demo, save before the script ends so that we can get an ID to display

                                $gLogger->logData('a string variable');
                                $gLogger->logEntry('Save #2, with string data saved'); sleep(1);
                                $gLogger->save();                           // For Demo, save before the script ends so that we can get an ID to display

                                $gLogger->logData(array(1=>'one'));
                                $gLogger->logData(array(1=>'one',2=>'two'));
                                $gLogger->logEntry('Save #3 with two arrays saved with they log entry'); sleep(1);
                                $gLogger->save();                           // For Demo, save before the script ends so that we can get an ID to display

                                // The Identifier has remain unchanged throught the saving, so lets collect those plain text logs
                                $session = $gLogger->identifier;
                                $example_output = gLogHelper::getGlog($session);
                                break;

                            case '5':
                                $gLogger = new gLogger($config);
                                $gLogger->setTitle($title);
                                $gLogger->setSource($source);
                                $entry = new JLogEntry("A Joomla jLogger entry being logged with Priority 'JLog::INFO'", JLog::INFO, 'MyJlogCategory');
                                $gLogger->logEntry($entry);
                                $gLogger->save();                           // For Demo, save before the script ends so that we can get an ID to display
                                $example_output = gLogHelper::getGlog($gLogger->id);  // Using the Helper, retrieve the plain text Log of what we just saved
                                break;

                            case '6':
                                $gLogger = new gLogger($config);
                                $gLogger->setTitle($title);
                                $gLogger->setSource($source);
                                $gLogger->set('audit', true);
                                // Set table and id if provided
                                $gLogger->setTable($log_table, $log_table_id);
                                $gLogger->logEntry("Saving some remark about table '{$log_table}', #{$log_table_id}");

                                $different_id = $log_table_id>3 ? rand(1,($log_table_id-1)) : 999999;
                                $gLogger->setTableID($different_id);
                                $gLogger->logEntry("Changing the ID for the table, and saving another remark, same table,different ID '{$log_table}', #{$different_id}");

                                $different_id = $log_table_id>3 ? rand(1,($log_table_id-1)) : 999999;
                                $gLogger->setTableID($different_id);
                                $gLogger->logEntry("Another different ID for the table, and saving a remark, same table '{$log_table}', #{$different_id}");

                                $gLogger->setTable('#__glogger_auditing_example', $different_id);
                                $gLogger->logEntry("Now table #__some_other_table, that has the same record ID we are working with: '{$log_table}', #{$different_id}");

                                $gLogger->save();                           // For Demo, save before the script ends so that we can get an ID to display
                                $example_output = gLogHelper::getGlog($gLogger->identifier);    // Using the Helper, retrieve the plain text Log of what we just saved
                                break;

                            case '7':
                                $gLogger = new gLogger($config);
                                $gLoggerConfig = array(
                                    'where'=>"`logtime` >= NOW() - INTERVAL 30 MINUTE",
//                                    'order'=>'id',
                                    'limit'=>'10',
                                    'format'=>'T'
                                );
                                $where_output = gLogHelper::getGlogsWhere($gLoggerConfig);
                                if(!$where_output->success) {
                                    //echo 'Line '.__LINE__.' of '.__FILE__.'<pre>'.print_r($where_output,true).'</pre>';die;
                                    $example_output = 'Constructed SQL failed!!';
                                    $example_output .= PHP_EOL.$where_output->sql;
                                    $example_output .= PHP_EOL.$where_output->result;
                                }else{
                                    $example_output = 'Constructed SQL: ';
                                    $example_output .= $where_output->sql;
                                    $example_output .= PHP_EOL.$where_output->result;
                                }
                                break;

                        }
                        echo '<div id="glog-results" class="well well-small">
                        <strong>Result for Example '.$example.':</strong><pre>'.print_r($example_output,true).'</pre>';
                        //                        if($gLogger && $gLogger->id){
                        //                            $html = showGloggerRow($gLogger->id);        // For Demo, get the saved gLogger row from the table
                        //                            echo $html;
                        //                        }

                        if($gLogger && $gLogger->identifier){
                            $html = showGloggerRow($gLogger->identifier);        // For Demo, get the saved gLogger row from the table
                            echo $html;
                        }
                        echo "</div>";
                        $gLogger->set('forceSave',false);


                    }
                    function showGloggerRow($id){
                        global $db;
                        if( is_numeric($id) ) {
                            $db->setQuery("SELECT id,title,identifier,source,table_name,table_id FROM #__glogger WHERE id='{$id}'");
                        }else{
                            $db->setQuery("SELECT id,title,identifier,source,table_name,table_id FROM #__glogger WHERE identifier='{$id}'");
                        }
                        if(!$rows = $db->loadObjectList() ){
                            $html = '';
                        }else{
                        $html = "<strong>gLogger metadata in table #__glogger:</strong><table id='row-result' class='table table-condensed'><tbody>";
                        $html .= "<tr><th>id</th><th>title</th><th>source</th><th>table_name</th><th>table_id</th><th>identifier</th></tr>";
                        foreach($rows as $row) {
                            $html .= "<tr><td>{$row->id}</td><td>{$row->title}</td><td>{$row->source}</td><td>{$row->table_name}</td><td>{$row->table_id}</td><td>{$row->identifier}</td></tr>";
                        }
                        $html .= "</tbody</table>";
                        }
                        return $html;
                    }

                    $app->close();
                    ?>
                    <!--                    </div>-->
                </form>
                <!-- End Content -->
            </section>
        </div>
        <!-- End Status Module -->
    </body>
</html>
