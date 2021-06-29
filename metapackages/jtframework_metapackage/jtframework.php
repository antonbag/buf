<?php
/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © 2020 JTOTAL All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

//jimport('joomla.filesystem.file');

use Joomla\String\StringHelper;


// Initialize
//require_once __DIR__ . '/autoload.php';

class plgSystemJTFramework extends JPlugin
{
	/**
	 *  Auto load plugin language 
	 *
	 *  @var  boolean
	 */
	protected $autoloadLanguage = true;
	
	/**
	 *  The Joomla Application object
	 *
	 *  @var  object
	 */
	protected $app;

 	/**
     *  Plugin constructor
     *
     *  @param  mixed   &$subject
     *  @param  array   $config
     */
    public function __construct(&$subject, $config = array())
    {
        // Declare extension logger
        JLog::addLogger(
            array('text_file' => 'plg_system_jtframework.php'),
            JLog::ALL, 
            array('jtframework')
        );

        // execute parent constructor
		parent::__construct($subject, $config);
	}
	
	public function onAfterInitialise()
    {
		//JLoader::registerPrefix('JTFramework', JPATH_PLUGINS . '/system/jtframework/JTFramework');
		//JLoader::registerNamespace('JTFramework', JPATH_PLUGINS . '/system/jtframework/JTFramework');
		//require_once __DIR__ . '/autoload.php';

    }


    public function onAjaxPluginParams()
	{
		
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		return json_encode("allright");


	}


}
