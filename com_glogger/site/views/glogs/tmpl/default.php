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

$aPri = array(
    1   =>'Emergency',
    2   =>'Alert',
    4   =>'Critical',
    8   =>'Error',
    16  =>'Warning',
    32  =>'Notice',
    64  =>'Info',
    128 =>'Debug'
);

// necessary libraries
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('jquery.ui');

// sort ordering and direction
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$user = JFactory::getUser();

$document = JFactory::getDocument();
$document->addStyleSheet(JUri::base(true).'/components/com_glogger/assets/qtips/jquery.qtip.min.css');
$document->addScript(    JUri::base(true).'/components/com_glogger/assets/qtips/jquery.qtip.min.js');
function human_filesize($bytes, $dec = 2)
{
    $size   = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    //    $size   = array('b', 'kb', 'mb', 'gb', 'tb', 'pb', 'eb', 'zb', 'yb');
    $factor = floor((strlen($bytes) - 1) / 3);

    return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

?>
<style>
    .glog-Emergency {background-color:red;  font-weight:bold;}
    .glog-Alert     {color:red;             font-weight:normal;}
    .glog-Critical  {color:orangered;       font-weight:bold;}
    .glog-Error     {color:orangered;       font-weight:normal;}
    .glog-Warning   {color:orange;          font-weight:normal;}
    .glog-Notice    {color:inherit;         font-weight:normal;}
    .glog-Info      {color:inherit;         font-weight:normal;}
    .glog-Debug     {color:inherit;         font-weight:normal;}
    .table-condensed th, .table-condensed td { padding: 2px 3px; }
    td.hotcell, .filter-click { cursor: pointer; cursor: hand; }
    .gLogText {
        color:black !important;
        min-width:1000px !important;
        font-size:12px;
        line-height:14px;
    }
    .qtip-content { font-family:monospace !important; }
</style>
<h2><?php echo JText::_('COM_GLOGGER_GLOGGER_VIEW_GLOGS_TITLE'); ?></h2>
<form action="<?php JRoute::_('index.php?option=com_mythings&view=mythings'); ?>" method="post" name="adminForm" id="adminForm">
    <?php
    // Search tools bar
    echo JLayoutHelper::render('joomla.searchtools.default',  array('view' => $this                   ));   // per jDeveloper
    //    echo '--->'.JLayoutHelper::render('default_filter',              array('view' => $this), dirname(__FILE__));  // per Component-Creator

    ?>
    <!--    <div style="width:45%; float:left;"> -->
    <div>
        <table id="gloggers" class="glogger_list table table-bordered table-hover table-condensed">
            <thead>
                <tr>
                    <th id="itemlist_header_title"><?php echo JHtml::_('grid.sort', 'COM_GLOGGER_GLOGGER_FIELD_ID_LABEL', 'a.id', $listDirn, $listOrder); ?></th>
                    <th class="nowrap left"><?php echo JHtml::_('grid.sort', 'Created', 'a.logtime', $listDirn, $listOrder) ?></th>
                    <th id="itemlist_header_title"><?php echo JHtml::_('grid.sort', 'COM_GLOGGER_GLOGGER_FIELD_CREATED_BY_LABEL', 'b.username', $listDirn, $listOrder); ?></th>
                    <th class="nowrap left"><?php echo JHtml::_('grid.sort', JText::_('COM_GLOGGER_GLOGGER_FIELD_SOURCE_LABEL'), 'a.source', $listDirn, $listOrder) ?></th>
                    <th id="itemlist_header_title"><?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?></th>
                    <th class="nowrap left" colspan="2"><?php echo JHtml::_('grid.sort', 'Table/ID#', 'a.table_name', $listDirn, $listOrder) ?></th>
                    <th class="nowrap hasTooltip" colspan="3" title="Number Log Entries, Data Entries<Br/>and Size of data">Logs/Datas</th>
                    <th class="nowrap hasTooltip" title="Highest Prioritiy Logged">Priority</th>
                    <th class="nowrap left" colspan="2"><?php echo JHtml::_('grid.sort', 'RefNum/Flagged', 'a.ref_num', $listDirn, $listOrder) ?></th>
                    <th class="nowrap left"><?php echo JHtml::_('grid.sort', JText::_('COM_GLOGGER_GLOGGER_FIELD_IDENTIFIER_LABEL'), 'a.source', $listDirn, $listOrder) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->items as $i => $item) :
//echo 'Line '.__LINE__.' of '.__FILE__.'<pre>'.print_r($item,true).'</pre>';die;
                    $glog = unserialize($item->data);
                    //                    $datasize = human_filesize($item->databytes,0);
                    $datasize = $item->databytes<500000 ? human_filesize($item->databytes,0) : '<span class="text-error"><strong>'.human_filesize($item->databytes,0).'</strong></span>';
                    $lowestPriority = 999999;
                    foreach ( $glog->gLogEntries as $k=>$v ){
                        if($v->priority < $lowestPriority )$lowestPriority = $v->priority;
                    }
                    $flagged = $item->flagged_by ? ' <i title="Flagged by '.$item->flag_user.'" class="hasTooltip icon-flag pull-right" aria-hidden="true"></i>' : '';

                    ?>
                    <tr data-glog_id="<?php echo $item->id; ?>" class="glog-row row<?php echo $i % 2; ?>">
                        <td class="hotcell"><?php echo $this->escape($item->id); ?></td>
                        <td class="hotcell"><?php
                            $dt = new DateTime($item->logtime);
                            echo $glog->create_time->format('M d H:i:s T');
                            ?>
                        </td>
<?php if(!empty($item->username)) { ?>
                        <td><?php echo $this->escape($item->username); ?></td>
<?php }else{ ?>
                        <td class="hasTooltip" title="Click to Lookup IP"><a target="whatismyipaddress" href="http://whatismyipaddress.com/ip/<?php echo $this->escape($item->remote_addr); ?>"><?php echo $this->escape($item->remote_addr); ?></a></td>
<?php } ?>

                        <!-- Filter on click cells                       -->
                        <?php if($item->source) { ?>
                            <td class="filter-click filter_source hasTooltip" title="Click to filter this Source"><?php echo $this->escape($item->source); ?></td>
                            <?php }else{ ?>
                            <td><?php echo $item->source; ?></td>
                            <?php } ?>
                        <td headers="itemlist_header_title" class="hotcell list-title"><?php echo $this->escape($item->title); ?></td>
                        <?php
                        $table_id  = $item->table_name ?  "<div class='table-name'>{$item->table_name}</div>" : '';
                        if(isset($item->table_id)){
                            $table_id .= $item->table_id==0 ? ' <div class="table-id glog-Alert">#'.$item->table_id.'</div>' : ' <div class="table-id">#'.$item->table_id.'</div>';
                        }
                        ?>
                        <?php if($item->table_name) { ?>
                            <td class="filter-click filter_table_name hasTooltip" title="Click to filter this Table"><?php echo $item->table_name; ?></td>
                            <?php }else{ ?>
                            <td><?php echo $item->table_name; ?></td>
                            <?php } ?>
                        <?php if($item->table_id) { ?>
                            <td class="filter-click filter_table_id hasTooltip" title="Click to filter this Table/ID"><?php echo $item->table_id; ?></td>
                            <?php }else{ ?>
                            <td><?php echo $item->table_id; ?></td>
                            <?php } ?>
                        <td class=""><?php echo $item->logs_count ?></td>
                        <td class=""><?php echo $item->data_count ?></td>
                        <td class=""><?php echo $datasize ?></td>
                        <td class=""><?php echo "<span class='glog-{$aPri[$lowestPriority]}'>{$aPri[$lowestPriority]}</span>"; ?></td>
                        <?php if($item->ref_num) { ?>
                            <td class="filter-click filter_refnum hasTooltip" title="Click to filter this Ref#"><?php echo $item->ref_num; ?></td>
                            <?php }else{ ?>
                            <td><?php echo $item->ref_num; ?></td>
                            <?php } ?>
                        <td class="flagged"><?php echo $flagged ?></td>
                        <?php if($item->identifier) { ?>
                            <td class="filter-click filter_identifier hasTooltip" title="Click to filter this Identifier"><?php echo $item->identifier; ?></td>
                            <?php }else{ ?>
                            <td><?php echo $item->identifier; ?></td>
                            <?php } ?>



                        <td class="td-log" data-gLogTitle="#<?php echo "{$item->id}, Remote Address {$item->remote_addr}" ?>"><?php echo $item->textlog; ?></td>
                    </tr>
                    <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <div style="display:none;">
        <pre id="log_viewer" class="gLog"></pre>
    </div>
    <div>
        <?php echo $this->pagination->getListFooter(); ?>
        <input type="hidden" name="task" value=" " />
        <input type="hidden" name="boxchecked" value="0" />
        <!-- Sortierkriterien -->
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
<script type="text/javascript">
    (function($) {
        $(document).on("click", "#gloggers tr td.hotcell", function(e) {
            window.location.href = 'index.php?option=com_glogger&view=glogger_details&id=' + jQuery(this).parent().data('glog_id');
        });

        $('.hotcell').each(function() { // Grab all elements with a title attribute,and set "this"
            var td  = $( $(this) ).siblings( ".td-log" )
            $(this).qtip({ //
                content: {
                    text: td.text().trim().replace(/(?:\r\n|\r|\n)/g, '<br />'),
                    title: td.attr('data-gLogTitle')
                },
                style: { classes: 'gLogText' },
                position: {
                    target: 'mouse', // Track the mouse as the positioning target
                    adjust: { x: -5, y: 15 } // Offset it slightly from under the mouse
                }
            });
        });


        $('.filter-click').click(function (){
            $(document.body).css({'cursor' : 'wait'});
            if( $(this).hasClass('filter_source')               ){
                $('#filter_source').val( $(this).text() );
            }else if( $(this).hasClass('filter_table_name')     ){
                $('#filter_table_name').val( $(this).text() );
            }else if( $(this).hasClass('filter_table_id')       ){
                $('#filter_table_name').val( $( $(this) ).siblings( ".filter_table_name" ).text() );
                $('#filter_search').val( $(this).text() );
            }else if( $(this).hasClass('filter_refnum')         ){
                $('#filter_ref_num').val( $(this).text() );
            }else if( $(this).hasClass('filter_identifier')     ){
                $('#filter_search').val( $(this).text() );
            }
            $('form#adminForm').submit();
        });

    })(jQuery);
</script>
