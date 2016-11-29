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

/**
* GLogger_Details item view class.
*
* @package     Glogger
* @subpackage  Views
*/
class GloggerViewGLogger_Details extends JViewLegacy
{
    protected $item;
    protected $form;
    protected $state;

    public function display($tpl = null)
    {
        $this->state 	= $this->get('State');
        $this->item 	= $this->get('Item');
        // Check if item is empty
        if (empty($this->item)) {
            $app->redirect(JRoute::_('index.php?option=com_glogger&view=glogs'), JText::_('JERROR_NO_ITEMS_SELECTED'));
        }
        // Is the user allowed to create an item?
        if (!$this->item->id && !$user->authorise("core.create", "com_glogger")) {
            throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $this->item->textlog    = strip_tags($this->item->textlog);
        $this->item->data       = unserialize($this->item->data);
        $this->item->data->set('forceSave',0);  // Turn this off, or the gLogger instance will save itself again
        $this->item->entries    = $this->item->data->gLogEntries;
        $this->getLogsTree      = $this->LogsTree();

        $this->item->datas = $this->item->data->gLogDatas;
        unset( $this->item->data->gLogEntries );
        unset( $this->item->data->gLogDatas );

        $this->item->user = jfactory::getUser( $this->item->data->created_by );
        $this->form 	= $this->get('Form');

        $app = JFactory::getApplication();
        $user = JFactory::getUser();

        // Get menu params
        $menu = $app->getMenu();
        $active = $menu->getActive();

        if (is_object($active))
        {
            $this->state->params = $active->params;
        }

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new Exception(implode("\n", $errors));
            return false;
        }

        // Increment hits
        $model = $this->getModel();
        $model->hit($this->item->id);##{end_hits}##

        parent::display($tpl);
    }

    public function LogsTree()
    {
        // Create JSON nodes for gLog Entries jsTree
        $nodes = array();
        $node = new stdClass();
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
echo 'Line '.__LINE__.' of '.__FILE__.'<pre>'.print_r($this,true).'</pre>';die;
        foreach( $this->item->data->gLogEntries as $k=>$entry){
            $le = __FUNCTION__.'_'.$k.'_';
            unset($node->icon);

            // Message
            $node->id = $le.'title';
            $node->parent = '#';
            $node->text =  "<span class='node_createtime node_fw'>{$entry->date->format('M d H:i:s T')}</span><div class='node_pri glog-{$aPri[$entry->priority]}'>{$aPri[$entry->priority]}</div><span class='node_message'>{$entry->message}</span>";
            $nodes[] = clone $node;

            // Category (text)
            if(empty($entry->category)){
                $cat_text = "No Category" ;
            }else{
                $cat_text = "Category '{$entry->category}'" ;
            }
            $node->id = $le.'category';
            $node->parent = $le.'title';
            $node->text = $cat_text ;
            $node->icon = 'false';
            $nodes[] = clone $node;

            // Callstack
            $node->id = $le.'callStack';
            $node->parent = $le.'title';
            $node->text = 'Callstack...';
            $node->icon = null;
            $nodes[] = clone $node;
//            unset($entry->callStack[0]);    // Remove of gLogger::__construct() callstack entry
            foreach($entry->callStack as $c=>$stack){
                $node->id = $le.'callStack_'.$c;
                $node->parent = $le.'callStack';
                $node->icon = null;
                if(!empty($stack['file'])){
                    $node->text = "<span class='node_fw'>".basename($stack['file'])."</span> at line {$stack['line']}";
                    $nodes[] = clone $node;
                    $node->icon = 'false';
                }else{
                    $node->text = "<span class='node_fw'>{$stack['class']}::{$stack['function']}</span>";
                    $nodes[] = clone $node;
                    $node->icon = 'false';
                }

                if(!empty($stack['file'])){
                    $node->id = $le.'callStack_'.$c.'_path';
                    $node->parent = $le.'callStack_'.$c;
                    $node->text = "<span class='node_fw'>{$stack['file']}</span>";
                    $nodes[] = clone $node;

                    $node->id = $le.'callStack_'.$c.'_line';
                    $node->parent = $le.'callStack_'.$c;
                    $node->text = "Line <span class='node_fw'>{$stack['line']}</span>";
                    $nodes[] = clone $node;
                }

                if(!empty($stack['function'])){
                    $node->id = $le.'callStack_'.$c.'_function';
                    $node->parent = $le.'callStack_'.$c;
                    $node->text = "Function <span class='node_fw'>{$stack['function']}</span>";
                    $nodes[] = clone $node;
                }

                if(!empty($stack['class'])){
                    $node->id = $le.'callStack_'.$c.'_class';
                    $node->parent = $le.'callStack_'.$c;
                    $node->text = "Class <span class='node_fw'>{$stack['class']}</span>";
                    $nodes[] = clone $node;
                }

                if(!empty($stack['type'])){
                    $node->id = $le.'callStack_'.$c.'_type';
                    $node->parent = $le.'callStack_'.$c;
                    $node->text = "<span class='node_type node_fw'>Type: {$stack['type']}</span>";
                    $nodes[] = clone $node;
                }

                if(isset($stack['args'])){
                    $node->id = $le.'callStack_'.$c.'_args';
                    $node->parent = $le.'callStack_'.$c;
                    $node->text = "<span class='node_args node_fw'>Arguments</span>";
                    unset($node->icon);
                    $nodes[] = clone $node;
                    if(!is_array($stack['args'])) $stack['args'] = array($stack['args']);
                    $node->icon = 'false';
                    foreach( $stack['args'] as $k1=>$v1){
                        $node->id = $le.'callStack_'.$c.'_args_'.$k1;
                        $node->parent = $le.'callStack_'.$c.'_args';
                        $node->text = "<span class='node_args node_fw'>{$k1} : {$v1}</span>";
                        $nodes[] = clone $node;
                    }
                }
            }

        }
        $LogsTree = json_encode($nodes);

        // Add Javascript
        $document = JFactory::getDocument();
        $document->addScriptDeclaration('var LogsTree = '.$LogsTree);

        return $LogsTree;
    }
}
?>