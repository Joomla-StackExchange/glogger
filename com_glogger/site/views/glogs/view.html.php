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
	protected $toolbar;

	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$this->items 		 = $this->get('Items');
		$this->state 		 = $this->get('State');
		$this->pagination 	 = $this->get('Pagination');
		$this->user		 	 = JFactory::getUser();
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$active = $app->getMenu()->getActive();
		if ($active)
		{
			$this->params = $active->params;
		}
		else
		{
			$this->params = new JRegistry();
		}

		// Prepare the data.
		foreach ($this->items as $item)
		{

			$active = $app->getMenu()->getActive();
			$item->params = clone($this->params);
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
			return false;
		}

		parent::display($tpl);
	}
}
?>