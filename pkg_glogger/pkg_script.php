<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
* Script file of HelloWorld component
*/
class pkg_gloggerInstallerScript
{
    /**
    * method to install the component
    *
    * @return void
    */
    function install($parent)
    {
        // $parent is the class calling this method
        //        $parent->getParent()->setRedirectURL('index.php?option=com_helloworld');
    }

    /**
    * method to uninstall the component
    *
    * @return void
    */
    function uninstall($parent)
    {
        // $parent is the class calling this method
        //        echo '<p>' . JText::_('COM_HELLOWORLD_UNINSTALL_TEXT') . '</p>';
    }

    /**
    * method to update the component
    *
    * @return void
    */
    function update($parent)
    {
        // $parent is the class calling this method
        //        echo '<p>' . JText::sprintf('COM_HELLOWORLD_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
    }

    /**
    * method to run before an install/update/uninstall method
    *
    * @return void
    */
    function preflight($type, $parent)
    {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        //        echo '<p>' . JText::_('COM_HELLOWORLD_PREFLIGHT_' . $type . '_TEXT') . '</p>';
    }

    /**
    * method to run after an install/update/uninstall method
    *
    * @return void
    */
    function postflight($type, $parent)
    {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        $user = JFactory::getUser();
        jimport('glogger.Formattedtext');
        $config=array();
        $gLogger = new gLogger($config);
        $gLogger->setSource(__FUNCTION__)->setTitle('gLogger Package Installation');
        $gLogger->logEntry('gLogger Package installed by '.$user->username);

        $link_example = JURI::root().'libraries/glogger/examples.php';
        $link_options = JURI::root().'administrator/index.php?option=com_config&view=component&component=com_glogger&path=';
        $link_viewer  = JURI::root().'index.php?option=com_glogger&view=glogger_details&id=1';
        $type = ucwords((strtolower( str_ireplace('_',' ', $type) )));

        echo "<p>gLogger Package {$type} for the Component and Library completed.<Br/>The installation of this package was gLogged as shown below.</p>";
        echo "<pre>".$gLogger->getTextLog().'</pre>';
        echo "<ol>Your next steps are:
            <li>Grant Permissions to User Groups that can view the logs and set other <a href='{$link_options}' target='_blank'>Component Options&nbsp;<span class='icon-out-2 small'></span></a></li>
            <li>Add a menu item for the Front-End view to see the gLog Entries: <a href='{$link_viewer}' target='_blank'>index.php?option=com_glogger&view=glogger_details&id=1&nbsp;<span class='icon-out-2 small'></span></a></li>
            <li>Experiment with gLogger using <a href='{$link_example}' target='_blank'>libraries/glogger/examples.php&nbsp;<span class='icon-out-2 small'></span></a></li>
            <li>Become the envy of your friends and family by being a full-time 'Glogger'!!</li>
        </ol>
        <p style='margin-bottom:50px;'>Seriously speaking, if this component does in fact turn out to be useful to anyone other than myself, I'd love to write it properly with suggestions and other feedback provided at <a href='mailto:gdp.extras@gmail.com'>gdp.extras@gmail.com</a></p>";

    }
}