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

$document = JFactory::getDocument();
$document->addStyleSheet(JUri::base(true).'/components/com_glogger/assets/jstree/dist/themes/default/style.min.css');
$document->addScript(    JUri::base(true).'/components/com_glogger/assets/jstree/dist/jstree.min.js');
$document->addScript(    JUri::base(true).'/components/com_glogger/views/glogger_details/tmpl/default.js');
$user = JFactory::getUser();
?>
<style type="text/css">
    .component-content h1, h2 {
        font-size: 100%;
        text-transform:unset;
    }
    h2 span {font-family:monospace;}

    .divTable{
        display:table;
        width:100%;
        /*        background-color:#eee;*/
        border-bottom:1px solid  #666666;
        border-spacing:5px;/*cellspacing:poor IE support for  this*/
        margin-bottom:10px;
    }
    .divRow{
        display:table-row;
        width:auto;
        clear:both;
    }
    .divCell{
        float:left;/*fix for  buggy browsers*/
        display:table-column;
        padding-right: 25px;
    }
</style>
<div class="divTable">
    <form id="save-form">
        <div class="divRow">
            <div class="divCell">
                <span id="btn-back" class="btn-ajax btn btn-mini btn-primary" >Back to List</span>
            </div>
            <div class="divCell">
                Delete this Entry: <input type="checkbox"  name="delete_entry">
            </div>
            <div class="divCell">
                Flag this Entry: <input type="checkbox" name="flagged_by" <?php echo $this->item->flagged_by ? 'checked="checked"' : ''; ?> value="<?php echo $this->escape($this->item->flagged_by); ?>">
            </div>
            <div class="divCell">
                Reference Number: <input type="text" name="ref_num" value="<?php echo $this->escape($this->item->ref_num);?>" placeholder="Reference Number">
            </div>
            <div class="divCell">
                <span id="btn-save" class="btn-ajax btn btn-mini btn-success">Apply</span>
            </div>
            <div class="divCell">
            </div>
        </div>
        <input type="hidden" id="href-back" value="<?php echo JUri::base() ?>index.php?option=com_glogger&view=glogs&filter_order=a.logtime&filter_order_Dir=desc">
        <input type="hidden" name="user_id" value="<?php echo $user->id ?>">
        <input type="hidden" name="id" value="<?php echo $this->item->id ?>">
        <input type="hidden" id="token" value="<?php echo JSession::getFormToken(); ?>">
    </form>
</div>



<?php echo JHtml::_('bootstrap.startTabSet', 'gLogger-admin-tabs', array('active' => 'tabs_details') );?>

<?php echo JHtml::_('bootstrap.addTab', 'gLogger-admin-tabs', 'tabs_details', 'gLog Details'); ?>
<table class="table table-condensed">
    <tbody>
        <tr>
            <td>ID</td>
            <td id="item-id"><?php echo $this->escape($this->item->id); ?></td>
        </tr>
        <tr>
            <td>Title</td>
            <td id="item-title"><?php echo $this->escape($this->item->title); ?></td>
        </tr>
        <tr>
            <td>Source</td>
            <td id="item-source"><?php echo $this->escape($this->item->source); ?></td>
        </tr>
        <tr>
            <td>Log Time</td>
            <td><?php echo $this->escape($this->item->logtime); ?></td>
        </tr>
        <tr>
            <td>Logged By</td>
            <td id="item-username"><?php echo $this->escape($this->item->user->username); ?></td>
        </tr>
        <tr>
            <td>Remote IP</td>
            <td id="item-remote_addr"><?php echo $this->escape($this->item->remote_addr); ?></td>
        </tr>
        <tr>
            <td>Identifier</td>
            <td><?php echo $this->escape($this->item->identifier); ?></td>
        </tr>
        <tr>
            <td>Table</td>
            <td><?php echo $this->escape($this->item->table_name); ?></td>
        </tr>
        <tr>
            <td>Table Record ID</td>
            <td><?php echo $this->escape($this->item->table_id); ?></td>
        </tr>
        <tr>
            <td>Textlog</td>
            <td><pre class="gLog"><?php echo $this->escape($this->item->textlog); ?></pre></td>
        </tr>
    </tbody>
</table>
<?php echo JHtml::_('bootstrap.endTab');?>

<?php
if(count($this->item->entries)){
    echo JHtml::_('bootstrap.addTab', 'gLogger-admin-tabs', 'tabs_glogs', 'gLog Entries'); ?>
    <div id="using_json_2"></div>
    <span class="btn btn-link open-window" id="print-logs" title="<?php echo count($this->item->entries) ?> gLog Entries">View gLog Entry Objects <span class="icon-out-2 small"></span></span>
    <pre class="gLogs" id="print-logs-html" style="display:none;">
        <?php
        foreach($this->item->entries as $k=>$entry){
            echo '<pre class="logged-item">'.print_r($entry,true).'</pre>';
        }
    ?></pre>
    <?php echo JHtml::_('bootstrap.endTab');
} ?>

<?php
if(count($this->item->datas)){
    echo JHtml::_('bootstrap.addTab', 'gLogger-admin-tabs', 'tabs_datas', 'Data Elements');  ?>
    <span class="btn btn-link open-window" id="print-data" title="<?php echo count($this->item->entries) ?> Data Entries">Open all <?php echo count($this->item->datas) ?> item(s) in new Window <span class="icon-out-2 small"></span></span>
    <pre class="gLogs" id="print-data-html">
        <?php
        foreach($this->item->datas as $k=>$data){
            echo '<pre class="logged-item">'.print_r($data,true).'</pre>';
        }
    ?></pre>
    <?php echo JHtml::_('bootstrap.endTab');
} ?>

<?php echo JHtml::_('bootstrap.addTab', 'gLogger-admin-tabs', 'tabs_gLogger', 'gLogger Object'); ?>
<pre class="gLog"><?php print_r($this->item->data); ?></pre>
<?php echo JHtml::_('bootstrap.endTab');?>

<?php echo JHtml::_('bootstrap.endTabSet');?>

<script type="text/javascript">
    (function ($){
        var list = [];
        $('.btn-ajax').click(function (){
            //                            var jmsgs = ['result.message'];  // You can stack multiple messages of the same type
            //                                Joomla.renderMessages({'success': jmsgs });
            // Go back to list
            if( $(this).attr('id')=='btn-back' ){
                $(location).attr('href', $('#href-back').val() )
            }

            // Save
            $.ajax({
                url: 'index.php?option=com_glogger&task=ajax.EntrySave&'+$('#token').val() + '=1',
                type: 'post',
                data: $("#save-form").serialize(),
                dataType: 'json',
                success: function(result) {
                    $(location).attr('href', $('#href-back').val() )
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });

        });
    })(jQuery);


</script>
