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

$user = JFactory::getUser();
$isAllowed = $user->authorise('core.viewlogs','com_glogger');
if(!$user->authorise('core.viewlogs','com_glogger')){
    JError::raiseError(404, 'Access to gLogger is restricted');
}
jimport('glogger.Formattedtext');

require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/glogger.php';
require_once JPATH_COMPONENT_SITE.'/helpers/route.php';

$controller	= JControllerLegacy::getInstance('Glogger');
$input = JFactory::getApplication()->input;

$lang = JFactory::getLanguage();
$lang->load('joomla', JPATH_ADMINISTRATOR);

JHtml::_('bootstrap.loadCss');
JHtml::_('bootstrap.framework');

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_glogger/assets/glogger.css');

try {
    $controller->execute($input->get('task'));
} catch (Exception $e) {
    $controller->setRedirect(JURI::base(), $e->getMessage(), 'error');
}

$controller->redirect();
?>