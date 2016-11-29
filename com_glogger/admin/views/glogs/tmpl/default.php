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

defined("_JEXEC") or die("Restricted access");
jimport ('joomla.html.html.bootstrap');

$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::root()    . 'components/com_glogger/assets/google-code-prettify/prettify.min.css');
$doc->addScript(JUri::root()        . 'components/com_glogger/assets/google-code-prettify/prettify.min.js');
$doc->addScript(JUri::root()        . 'components/com_glogger/assets/google-code-prettify/run_prettify.js');
$style = '  .prettyprint ol.linenums > li { list-style-type: decimal; }
pre.result {background-color:#EFEC99;}
.subhead-collapse{display:none !important;}
h3 {margin:30px 0 12px 0;}
#glog-methods dt{font-family:monospace;}
#glog-methods dd{margin-bottom:15px;}
#glog-methods code { background-color: inherit; color: black; border: none; font-size: inherit;}
';
$doc->addStyleDeclaration( $style );
?>
<div class="alert info" style="margin-top:25px;">Aside from setting <a href="index.php?option=com_config&amp;view=component&amp;component=com_glogger&amp;path=">Options</a>, there are no administrator functions for gLogger.
<ul>
    <li>Log entries are viewable from the front-end Details View</li>
    <li>Default Permissions allow only Super Administrators to view the gLog Details; adjust according to your needs</li>
</ul>
</div>

<?php echo JHtml::_('bootstrap.startTabSet', 'gLogger-admin-tabs', array('active' => 'tabs_gLogger_main') );?>

<?php echo JHtml::_('bootstrap.addTab', 'gLogger-admin-tabs', 'tabs_gLogger_main', 'Introduction'); ?>
<div>The goal of gLogger was to:
    <ul>
        <li>Save logs to a database (instead of or in addition to) normal Joomla log files</li>
        <li>Consolidate logging across timezones, and sources of events (APIs, Crons, User forms, Admin forms, etc.)</li>
        <li>Add specific tracing information about the log entries</li>
        <li>Add Table/ID fields to the logs to have some relationship between the logs themselves and the data being logged</li>
        <li>Provide an easy interface to find and view log entries</li>
    </ul>
    In short, the gLogger:
    <ul>
        <li>Gathers information along the way in normal logging fashion</li>
        <li>Saves itself to the database for later use</li>
        <li>Allows the addition of relevant metadata such as title, affected table, the row affected</li>
        <li>With unique session identifers, each gLog session can be linked to other sessions that share the same tables/rows</li>
        <li>Log files are self-cleaning, based on settings, but an additional gLogger <em>Audit</em> record can also be written that will never be automatically deleted.</li>
        <li>The enables you to see every logged event (regardless of who/where/how it occurred) against tables/records of your choice, over any period of time</li>
    </ul>
    <div style='font-weight: bold;;'>Other Possibilities of gLogger</div>
    <p>Because gLogger can save entire data items along the way of logging, and each logging session can be labeled and categorized so freely, it becomes possible to have a full audit trail of changes made in your database.  Of course, a system plugin and related events could accomplish the same thing, but this is an added benefit if you are analyzing data logs with gLogger.
        <br/>Using PHP/SQL with #__glogger, it is possible to find all logged changes to the data of interest <em>and</em> access whatever data of that change that is of interest to you.</p>
</div>
<?php echo JHtml::_('bootstrap.endTab');?>


<?php echo JHtml::_('bootstrap.addTab', 'gLogger-admin-tabs', 'tab2_j31_id', 'Data and Methods'); ?>
<?php echo JHtml::_('bootstrap.startAccordion', 'accordion-glogger-data'); ?>

<p>gLogger extends the core Joomla logging Utility, so configuration and native use all remain intact. See <a target="_blank" href="https://docs.joomla.org/Using_JLog">Using JLog</a> for configuration and further documentation.</p>
<?php echo JHtml::_('bootstrap.addSlide', 'accordion-glogger-data', 'Creating and Configuring a gLogger Instance', 'slide0_id'); ?>
<p>Creating an instance of gLogger is the same as a JLog object, with the addition of these optional configuration settings.  Note that any JLog config values <em>will</em> be used in the subsequent JLog entries.
    <Br/>The gLogger config values are not required, and all values will default to the values in the Component Options.</p>
<pre class="prettyprint linenums">jimport('glogger.Formattedtext');
    $config = array(
    'native_addentry'   => false,               // True or False to include native JLog write to logfile
    'glogger_source'    => "My Source Name",    // Set source when the object is created, or with setSource() method
    'glogger_title'     => 'Example label'      // Set title when the object is created, or with setTitle() method
    );
    $gLogger = new gLogger($config);
</pre>
<?php echo JHtml::_('bootstrap.endSlide'); ?>

<?php echo JHtml::_('bootstrap.addSlide', 'accordion-glogger-data', 'gLogger Data Explanations', 'slide1_id'); ?>
<h3>User Fields</h3>
<p>These User fields are generally unchecked for validity, and all are optional.</p>
<dl>
    <dt>Title</dt>
    <dd>The Title acts as a label for the Logging instance.  It is stored and used exactly the same as the Name/Title column found in most Joomla tables.  By design, it is intended to contain values like "Overnight Job #1", etc. <Br/>If not specified, it defaults to <em>Untitled gLogger</em>. </dd>

    <dt>Source</dt>
    <dd>This entry should be indicative of where the gLog came from.  By design the values are intended to be entries like "User Form", "API", "CRON" </dd>

    <dt>Table Name</dt>
    <dd>This is the mySQL table name that you may be logging activity for.</dd>

    <dt>Table Id</dt>
    <dd>If a mySQL table name has been specified, then this can contain the particular record ID of interest.</dd>
</dl>

<h3>Internal Fields</h3>
<dl>
    <dt>ID</dt>
    <dd>The is the normal unique record Identifier stored in the table.  Each time the instance is saved, a new record is created, and this number is incremented.</dd>

    <dt>Identifier</dt>
    <dd>This value is effectively a "session" identifier, and remains unchanged through the life of the gLogger instance regardless of how many times the gLog is saved.<br />
        You might choose to save() the object 5 times, each having it's own record id, but this value remains the same for all 5 records.
    </dd>

    <dt>Text Log</dt>
    <dd>When logging entries during the execution of your script, a great deal more than just a text item may be captured.<br/>
        This field contains a plain text summary of all entries were logged, and can be retrieved directly from the database or using the gLogger helper function <code>gLogHelper::getGlog()</code>.
        <pre class="result">
            --- xyz_yourtable --- #123 --- (2 data items were saved with log) ------------------
            2016-11-17 20:28:45 GMT    example.php Line 91    --    Log with a new title from here on
            2016-11-17 20:28:46 GMT    example.php Line 97    --    Some Account activity to log for account #123
            2016-11-17 20:28:47 GMT    example.php Line 99    --    Some more Account activity to log
        </pre>
        A helper function is included that allows you to merge all the Text logs for a given criteria to give a single chronological reference to your logs (See sample uses).
    </dd>

    <dt>Data</dt>
    <dd>Whenever the gLogger is saved, it saves a serialized copy of itself to the database. The saved instance can be loaded with any PHP script using <code>unserialize()</code>, or with the gLogger helper function <code>gLogHelper::getGlogger()</code>.
        <br/>Each Jlog entry added <em>and</em> each data item (arrays or objects, see below) that you saved along the way are retained in the gLogger object.
        <br/><em>This feature can be particularly useful because the JLog entries that are logged contain the PHP backtrace from the point being logged.</em></dd>
</dl>

<h3>Stored Log Entry Types</h3>
<dl>
    <dt>Log Entries</dt>
    <dd>Each execution of <code>$gLogger->logEntry()</code> stores a JLogEntry object, containing the Category, Date (JDate object with timezone), Message, Priority, and Callstack</dd>

    <dt>Data Entries</dt>
    <dd>Each execution of <code>$gLogger->logData()</code> stores the given data item in the gLogger instance.  A data item may be any valid PHP variable, including arrays and object
        <p class="alert" style="margin-top:5px;"><strong>Caution: </strong>Saving many large data items could become problematic with available resources.  <br/>If you want to save a great deal of custom data, consider using the <code>$gLogger-&gt;save()</code> method at appropriate points to remove saved entries from the glogger instance.</p></dd>

</dl>

<?php echo JHtml::_('bootstrap.endSlide'); ?>
<?php echo JHtml::_('bootstrap.addSlide', 'accordion-glogger-data', 'Public Methods', 'slide2_id'); ?>

<dl id="glog-methods">
    <dt>$gLogger->setSource( $source )</dt>
    <dd>Sets/Replaces <code>$gLogger->source</code></dd>

    <dt>$gLogger->setTitle( $title )</dt>
    <dd>Sets/Replaces <code>$gLogger->title</code></dd>

    <dt>$gLogger->setTable( $table_name , [$table_id] )</dt>
    <dd>Sets/Replaces <code>$gLogger->table</code>.  The Joomla prefix of <code>#__</code> is optional; if it is not there, it will be prepended.</dd>

    <dt>$gLogger->setTableID( $table_id )</dt>
    <dd>Sets/Replaces <code>$gLogger->table_id</code>.  If provided, it should be the unique row id of the table specified.  It is used in SQL statements with <code>$gLogger->table</code>.</dd>

    <dt>$gLogger->setNewline( $newline )</dt>
    <dd>By default, the plain text log uses <code>PHP_EOL</code> for new lines, and the Component then uses <code>nl2br()</code> for display.  If you are using the plain text logs in a 3rd party application, you can specify a different linebreak string to use.</dd>

    <dt>$gLogger->logEntry( $message, [$priority], [$category], [$date] )</dt>
    <dd>This is the method used to actually log entries throughout your script - each entry is saved in the gLogger object.  The <code>$message</code> argument is the only required element;
        see <a target="_blank" href="https://docs.joomla.org/Using_JLog">Using JLog</a> for detailed documentation.</dd>

    <dt>$gLogger->logData( $variable )</dt>
    <dd>This is the method used to save any variable to the gLogger object - any valid PHP variable is allowed.
        <p class="alert" style="margin-top:5px;"><strong>Caution: </strong>Saving many large data items could become problematic with available resources.  <br/>If you want to save a great deal of custom data, consider using the <code>$gLogger-&gt;save()</code> method at appropriate points to remove saved entries from the glogger instance.</p>
    </dd>

    <dt>$gLogger->save( [$SaveOnDie] )</dt>
    <dd>Normally, gLogger is automatically saved when the instance is destroyed ( <code>function __destruct()</code> ).
        <br/>If you have saved a lot of data, or for any other reason, this method will create a record in table <code>#__glogger</code>, and re-initialize the logged entries and logged data items.
        <br/>The optional boolean argument allows you to turn OFF automatic saving when you manually perform the save.
    </dd>
</dl>

<?php echo JHtml::_('bootstrap.endSlide'); ?>
<?php echo JHtml::_('bootstrap.endAccordion'); ?>

<?php echo JHtml::_('bootstrap.endTab');?>


<?php echo JHtml::_('bootstrap.addTab', 'gLogger-admin-tabs', 'tab3_j31_id', 'Sample Uses & Helper Functions'); ?>

<p><a href="../libraries/glogger/examples.php" target="glogexamples">Examples Page <span class="icon-out-2 small"></span></a></p>

<?php echo JHtml::_('bootstrap.startAccordion', 'accordion-glogger-main'); ?>
<?php echo JHtml::_('bootstrap.addSlide', 'accordion-glogger-main', 'Basic Usage', 'slide10_id'); ?>
<pre class="prettyprint linenums">
    $config = array(
    'native_addentry'   =>false,
    'glogger_source'    => "Example script",    // Set source in Config, or with setSource() method
    'glogger_title'     => 'Sample dataa'       // Set title in Config, or with setTitle() mehtod
    );
    $gLogger = new gLogger($config);

    // Add a Log Entry with just a line of text
    $gLogger->logEntry('Some text to log. Suppose I changed the username here');
</pre>
<?php echo JHtml::_('bootstrap.endSlide'); ?>
<?php echo JHtml::_('bootstrap.addSlide', 'accordion-glogger-main', 'Retrieve the plain text log of events by specifying the Id (integer) of the gLogger record, or the Session Identifier (Alphanumeric)', 'slide11_id'); ?>
<pre class="prettyprint linenums">
    jimport('glogger.Formattedtext');
    $text = gLogHelper::getGlog(183);
    print_r($text);
</pre>
Will return something like this:
<pre class="result">
    --- xyz_yourtable --- #123 --- (2 data items were saved with log) ------------------
    2016-11-17 20:28:45 GMT    example.php Line 91    --    Log with a new title from here on
    2016-11-17 20:28:46 GMT    example.php Line 97    --    Some Account activity to log for account #123
    2016-11-17 20:28:47 GMT    example.php Line 99    --    Some more Account activity to log
</pre>
<?php echo JHtml::_('bootstrap.endSlide'); ?>
<?php echo JHtml::_('bootstrap.addSlide', 'accordion-glogger-main', 'Retrieve the complete gLogger classed as a gLogger Object by specifying the Id (integer) of the gLogger record, or the Session Identifier (Alphanumeric)', 'slide12_id'); ?>
<pre class="prettyprint linenums">
    // Get the complete gLogger Object, and print the Entries or Data Items that were logged
    jimport('glogger.Formattedtext');
    $obj = gLogHelper::getGlogger('5A8A48D9BD5ABCF11989BE35F54CFC65');
    echo '&lt;pre&gt;'.print_r($obj->gLogEntries,true).'&lt;/pre&gt;';  // Log Entries (JLog class)
    //echo '&lt;pre&gt;'.print_r($obj->gLogDatas,true).'&lt;/pre&gt;';  // Data Items (mixed, depending on what data variables you logged)
    $app->close();
</pre>
Will return something like this:
<pre class="result">   (
    [0] => JLogEntry Object (
    [category] =>
    [date] => JDate Object (
    [tz:protected] => DateTimeZone Object
    (
    [timezone_type] => 2
    [timezone] => GMT
    )

    [date] => 2016-11-19 18:40:46
    [timezone_type] => 2
    [timezone] => GMT
    )

    [message] => Userid 877 is about to execute Example 2 in PHP script examples.php
    [priority] => 64
    [priorities:protected] => Array (
    [0] => 1
    [1] => 2
    [2] => 4
    [3] => 8
    [4] => 16
    [5] => 32
    [6] => 64
    [7] => 128
    )
    [callStack] => Array (
    [0] => Array (
    [file] => D:\wamp\www\ambrosevideo\libraries\glogger\Formattedtext.php
    [line] => 249
    [function] => __construct
    [class] => JLogEntry
    [type] => ->
    )
    [1] => Array (
    [file] => D:\wamp\www\ambrosevideo\libraries\glogger\examples.php
    [line] => 312
    [function] => logEntry
    [class] => gLogger
    [type] => ->
    )
    )
    )
</pre>
<?php echo JHtml::_('bootstrap.endSlide'); ?>
<?php echo JHtml::_('bootstrap.addSlide', 'accordion-glogger-main', 'Return the complete gLogger object as JSON by specify TRUE in the function call.', 'slide13_id'); ?>
<pre class="prettyprint linenums">
    jimport('glogger.Formattedtext');
    $json = gLogHelper::getGlogger(183, true);
    header('Content-Type: application/json');
    print_r( $json );
    $app->close();
</pre>
Will return something like this:
<pre class="result">{
    "id": null,
    "identifier": "FC6A38F84A18E8FD98A62710B56E938E",
    "source": "Example script",
    "title": "Sample dataa",
    "table_name": "xyz_users",
    "table_id": 877,
    "create_time": {
    "date": "2016-11-17 21:49:55",
    "timezone_type": 2,
    "timezone": "GMT"
    },
    "save_time": {
    "date": "2016-11-17 21:49:57",
    "timezone_type": 2,</pre>
<?php echo JHtml::_('bootstrap.endSlide'); ?>
<?php echo JHtml::_('bootstrap.addSlide', 'accordion-glogger-main', 'Return Merged text gLogs that match the given criteria', 'slide15_id'); ?>
<pre class="prettyprint linenums">
    jimport('glogger.Formattedtext');
    $gLogger->setTable($log_table);
    $gLoggerConfig = array(
    'where'=>"table_name='{$log_table}' AND `logtime` >= NOW() - INTERVAL 24 HOUR",
    'order'=>'id',
    'direction'=>'DESC',
    'limit'=>'0',
    'format'=>'T' // T for Text Logs, I for string of #__glogger IDs found
    );
    $output = gLogHelper::getGlogsWhere($gLoggerConfig);
    if(!$output->success) {
    $output = 'Constructed SQL failed!!';
    $output .= PHP_EOL.$where_output->sql;
    $output .= PHP_EOL.$where_output->result;
    }else{
    $output = 'Constructed SQL: ';
    $output .= $where_output->sql;
    $output .= PHP_EOL.$where_output->result;
    }
    print_r($output);
    $app->close();
</pre>
Will return something like this:
<pre class="result">--- xyz_yourtable --- #123 --- (2 data items were saved with log) ------------------
    2016-11-16 20:23:35 GMT    example.php Line 91    --    Log with a new title from here on
    2016-11-16 20:23:46 GMT    example.php Line 97    --    Some Account activity to log for account #123
    2016-11-16 20:23:57 GMT    example.php Line 99    --    Some more Account activity to log
    --- xyz_yourtable --- #123 --- (2 data items were saved with log) ------------------
    2016-11-17 12:18:45 GMT    example.php Line 91    --    Log with a new title from here on
    2016-11-17 12:18:50 GMT    example.php Line 97    --    Some Account activity to log for account #123
    2016-11-17 12:18:53 GMT    example.php Line 99    --    Some more Account activity to log
    --- xyz_yourtable --- #123 --- (2 data items were saved with log) ------------------
    2016-11-17 20:14:45 GMT    example.php Line 91    --    Log with a new title from here on
</pre>
<?php echo JHtml::_('bootstrap.endSlide'); ?>
<?php echo JHtml::_('bootstrap.addSlide', 'accordion-glogger-main', 'Access logged Data Items for gLogger Records that match the given criteria', 'slide16_id'); ?>
If you are interested in the details of matching Log Entries, you may return the IDs of those records and use <code>gLogHelper::getGlog()</code> to inspect each of them to examine the contained data.
<pre class="prettyprint linenums">
    jimport('glogger.Formattedtext');
    $gLogger->setTable($log_table);
    $gLoggerConfig = array(
    'where'=>"table_name='{$log_table}' AND `logtime` >= NOW() - INTERVAL 24 HOUR",
    'order'=>'id',
    'direction'=>'DESC',
    'limit'=>'0',
    'format'=>'I' // T for Text Logs, I for string of #__glogger IDs found
    );
    $output = gLogHelper::getGlogsWhere($gLoggerConfig);
    if(!$output->success) {
    $output = 'Constructed SQL failed!!';
    $output .= PHP_EOL.$output->sql;
    $output .= PHP_EOL.$output->result;
    }else{
    $sql = "SELECT * FROM #__glogger WHERE id IN (" . $output->result . ")";
    $db = jfactory::getDbo();
    $db->setQuery( $sql );
    $records = $db->loadObjectList();
    foreach($records as $record) {
    $saved_data = unserialize($record->data);
    echo '&lt;pre&gt;' . $saved_data. '&lt;/pre&gt;';  // This is the saved gLog data that contains anything you logged with it.
    }
    }
    $app->close();
</pre>
Will return something like this:
<pre class="result">1,202,661,915</pre>
<?php echo JHtml::_('bootstrap.endSlide'); ?>
<?php echo JHtml::_('bootstrap.endAccordion'); ?>
<?php echo JHtml::_('bootstrap.endTab');?>
<?php echo JHtml::_('bootstrap.addTab', 'gLogger-admin-tabs', 'tabs_gLogger_disclaimer', 'Disclaimer, Warnings, etc.'); ?>
<p>The idea for this component was hatched when I needed a better way to track what was going on in our software and database.  Googling and inquires on <a href="http://joomla.stackexchange.com/questions/18377/are-there-any-tools-or-components-for-jlogger" target="_blank">Joomla Stack Exchange</a> yielded absolutely no results, so I took it upon myself to come up with a solution.  <Br/>There was no time available to truly plan and design the software, and scope creep soon became a reality, so I've decided to stop where I'm at and see how it is received, and what else <em>should</em> be done, versus what else <em>could</em> be done.  <br />Here's what I know so far:</p>
<dl>
    <dt>UI aspects of the Component</dt>
    <dd>The emphasis of the project was on the logging, and so a well known component creator was used to generate the component itself.  It has it's deficiencies, but it does most of the job needed.<br/>The current iteration is a trimmed back version of what was generated, but my needs of gLogger will ultimately render a better Search/Filtering implementation, as well as saving/printing/exporting those filtered results for external use.</dd>

    <dt>Timezones</dt>
    <dd>Further development of Timezones is likely necessary.  The driving force behind this project involved updates via AJAX, cURL, and standard Joomla Admin/User forms from timezones between France and Seattle.
        <Br/>As I use the tools more in a real-world environment, I'm expecting to encounter needed consideration for system, database, and Joomla timezone settings.</dd>

    <dt>JLog Entries</dt>
    <dd>The current version really only accounts for my preferences of the JLog configuration.  True and full respect of the JLog settings will be forthcoming in the near future.</dd>

    <dt>Saving custom Data and size</dt>
    <dd>Since gLogger is designed for developers, it puts no limitations on how or what you save as data, assuming the developer understands and considers the ramifications of saving too much data.  <Br/>The possibility of exhausting resources and creating a huge database is a distinct possibility if this feature is over-used.
    <br/>Better handling of data in general, specifically non-Joomla classes are also expected to be part of future releases (ideas welcome).</dd>

    <dt>Additional Features/Ideas</dt>
    <dd>As I wrote the <a href="../libraries/glogger/examples.php" target="_glogexamples">examples</a> and documented what I'd started with, I kept coming up with new ideas of ways I could use it.  As I added them, I started to see the code get a little uglier and uglier with each pass, and documentation got a little wobbly.
        <Br/>Refactoring the Class and the Helper is likely.  Any ideas are welcome at the email address at the top of the PHP files, but no promises.  I do, however, want the examples and docs to be a true representation of what is available from the gLogger library.</dd>
</dl>
<?php echo JHtml::_('bootstrap.endTab');?>
<?php echo JHtml::_('bootstrap.endTabSet');?>









