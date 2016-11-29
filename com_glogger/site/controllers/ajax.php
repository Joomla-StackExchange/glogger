<?php

/**
* @version     1.0.0
* @package     com_pass
* @copyright   Copyright (C) 2014. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
* @author      SCCtv <compass@scctv.net> - http://www.scctv.net
*/
// No direct access
defined('_JEXEC') or die;
require_once JPATH_COMPONENT . '/controller.php';
/**
* TPC Ajax controller class.
*/
//class TpcControllerAjax extends TpcController
class GloggerControllerAjax extends JControllerForm
{
    // e.g. index.php?option=com_glogger&task=ajax.EntrySave&id=69247.....
    public function EntrySave () {
        $app            = JFactory::getApplication();
        $task_failed    = true;    // Assume failure incase something wonky happens
        $data           = array();
        if(!JSession::checkToken( 'get' )){
            $msg = 'CSRF check failed at '.__FUNCTION__;
        }else{
            $msg = __METHOD__.' initiated';
            try {
                $jinput         = JFactory::getApplication()->input;
                $data['input']  = (object) $jinput->getArray();
                $isValid        = true;

                $glogger_id     = $jinput->getInt('id',0);
                if(!$glogger_id){
                    $msg = "No ID provided - gLogger Info could not be saved";
                }else{
                    $db                 = JFactory::getDbo();
                    $object             = new stdClass();

                    if( 'on'==$jinput->getString('delete_entry','off') ){
                        $object->delete = 'on';
                        $result = $db->setQuery("DELETE FROM #__glogger WHERE id='{$glogger_id}'")->execute();
                        $task_failed    = !$result;
                        $msg            = 'gLog Entry was deleted';
                    }else{
                        $user_id    = $jinput->getInt('user_id',0);
                        $flagged_by = $jinput->getInt('flagged_by',false);
                        $ref_num    = $jinput->getString('ref_num',null);

                        $object->id         = $glogger_id;
                        $object->ref_num    = $ref_num              ? $ref_num  : '';
                        $object->flagged_by = $flagged_by!==false   ? $user_id  : 0;
                        if($object->id){
                            $result         = $db->updateObject('#__glogger', $object, 'id');
                            $task_failed    = !$result;
                            $msg            = 'gLog Entry was saved';
                            $app->enqueueMessage($msg);
                        }
                    }
                    $data['object'] = $object;
                }
            }
            catch (Exception $e) {
                $msg            = $e->getMessage();
            }
        }
        header('Content-Type: application/json');
        echo new JResponseJson($data, $msg ,$task_failed);
        $app->close();
    }
}

