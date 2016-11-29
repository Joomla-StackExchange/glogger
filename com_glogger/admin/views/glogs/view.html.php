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

require_once JPATH_COMPONENT.'/helpers/glogger.php';

/**
 * GLogs list view class.
 *
 * @package     Glogger
 * @subpackage  Views
 */
class GloggerViewGLogs extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
			return false;
		}

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
		}

		parent::display($tpl);
	}

	/**
	 *	Method to add a toolbar
	 */
	protected function addToolbar()
	{
		$state	= $this->get('State');
		$canDo	= GloggerHelper::getActions();
//        $canDo->set('core.admin',0);
//        echo 'Line '.__LINE__.' of '.__FILE__.'<pre>'.print_r($canDo,true).'</pre>';
		$user	= JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolBarHelper::title(JText::_('COM_GLOGGER_GLOGGER_VIEW_GLOGS_TITLE'));

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_glogger');
		}
	}
}
?>