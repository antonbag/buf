<?php
/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © 2023 JTOTAL All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 * @version         5.0.0
*/

namespace Jtotal\BUF\Site\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Jtotal\BUF\BufHelper;

//no direct access
defined('_JEXEC') or die;

//LOAD JTFW
if (is_file(JPATH_PLUGINS . '/system/jtframework/autoload.php')) {
    require_once JPATH_PLUGINS . '/system/jtframework/autoload.php';
} else {
    $app = Factory::getApplication();
    Factory::getLanguage()->load('plg_system_jtframework', JPATH_PLUGINS . '/system/jtframework/language');
    $app->enqueueMessage(Text::_('JT_FW_NOT_FOUND'), 'error');
    return;
}

class BufInitField extends FormField
{
    protected $type = 'BufInit';

    protected function getInput()
    {

        include_once JPATH_SITE . '/templates/buf/src/bufhelper.php';


        $app = property_exists($this, 'app') ? $this->app : Factory::getApplication();

        $doc = $app->getDocument();
        $app = Factory::getApplication();
        $buf_path = URI::root(true) . '/templates/buf/backend';

        $scriptDeclaration = "var bufpluginbufajax = '{$this->getVersion()}';";
        //$doc->addScriptDeclaration("var bufpluginbufajax = '{$this->getVersion()}';");

        $tpath_real = realpath(JPATH_SITE . '/templates/');
        $tpath = str_replace('\\', '\\\\', $tpath_real);
        $tpath .= '/';

        $scriptDeclaration .= "var tpath = '{$tpath}';";
        //$doc->addScriptDeclaration("var tpath = '{$tpath}';");

        $jversion = BufHelper::getJVersion();

        $params = $this->form->getValue('params');
        $layout = $params->buf_layout;
        if ($layout == '') {
            $layout = 'default';
        }

        $scriptDeclaration .= "var buf_layout = '{$layout}';";

        //JOOMLA 3
        /*
        if ($jversion == "3") {

            $doc->addStyleSheet($buf_path . '/css/bufadmin.css');
            //Fa5::getFaCDN();
            Fa::getFa5CDN();
            $scriptDeclaration .= "var jversion = '3';";
        }*/

        //JOOMLA 4
        //$scriptDeclaration .= "var jversion = '4';";

        $wa = $doc->getWebAssetManager();
        $wa->registerAndUseStyle('bufadmin4.css', 'templates/buf/backend/css/bufadmin4.css');

        
        if ($jversion == 5) {
            $wa->registerAndUseScript('bufadmin5.js', 'templates/buf/backend/js/bufadminv5.js', [], ['defer' => true], ['webcomponent.editor-codemirror']);
        } else {
            $wa->registerAndUseScript('bufadmin4.js', 'templates/buf/backend/js/bufadmin.js', [], ['defer' => true], []);
        }


        $input = $app->input;
        $template_id = $input->get('id', 0, 'INT');

        //init variables
        //$doc->addScriptDeclaration("var templateid = '{$template_id}';");

        $scriptDeclaration .= "var templateid = '{$template_id}';";
        $wa->addInlineScript($scriptDeclaration);


        /**************** */
        /****VERSION**** */
        /**************** */
        $ext_versions = '';

        $ext_versions .= '<div >';
        $ext_versions .= '<img class="buf_minilogo_bar" src="../templates/buf/images/buf_logos/logo_buf_64.png" width="32" height="32"/>';

        $ext_versions .= '<ul class="breadcrumb">';

        //* bufajax
        if (PluginHelper::isEnabled('ajax', 'bufajax')) {
            $ext_versions .= '<li><strong>Buf ajax:</strong> <span class="badge badge-info bg-light text-dark">' . $this->getVersion() . '</span></li>';
            $ext_versions .= '<li><span class="divider">/</span></li>';
        }

        //* JTFRAMEWORK
        $check_jtfw = $this->getExtensionVersion('jtframework', '');
        if (!$check_jtfw) {
            $ext_versions .= '<li><strong>JT Framework:</strong> <span class="badge badge-important bg-light text-dark">Unknown</span></li>';
            $ext_versions .= '<li><span class="divider">/</span></li>';
        } elseif ($check_jtfw == '1.0.0') {
            $ext_versions .= '<li><strong>JT Framework:</strong> <span class="badge badge-important bg-light text-dark">Update required</span></li>';
            $ext_versions .= '<li><span class="divider">/</span></li>';
        } else {
            $ext_versions .= '<li><strong>JT Framework:</strong> <span class="badge badge-info bg-light text-dark">' . $check_jtfw . '</span></li>';
            $ext_versions .= '<li><span class="divider">/</span></li>';
        }

        //* JT LIBS
        $check_jtlibs = $this->getExtensionVersion('jtlibs', '');
        if (!$check_jtlibs) {
            $ext_versions .= '<li><strong>JT libs:</strong> <span class="badge badge-important bg-light text-dark">Unknown</span></li>';
            $ext_versions .= '<li><span class="divider">/</span></li>';
        } elseif ($check_jtlibs == '1.0.0') {
            $ext_versions .= '<li><strong>JT libs:</strong> <span class="badge badge-info bg-light text-dark">update required</span></li>';
            $ext_versions .= '<li><span class="divider">/</span></li>';
        } else {
            $ext_versions .= '<li><strong>JT libs:</strong> <span class="badge badge-info bg-light text-dark">' . $check_jtlibs . '</span></li>';
            $ext_versions .= '<li><span class="divider">/</span></li>';
        }

        $ext_versions .= '</ul>';
        $ext_versions .= '</div>';

        /**************** */
        /****FILES CHECK**** */
        /**************** */
        $template_init = '<div>';

        //CHECK FILES
        //check bufajax
        if (is_dir(JPATH_SITE . '/plugins/ajax/bufajax')) {
            //plugin exists
            if (!PluginHelper::isEnabled('ajax', 'bufajax')) {
                $template_init .= '<span class="alert alert-warning buf_ajax_not_enabled"><i class="fas fa-exclamation-triangle"></i> PLUGIN NOT ENABLED. Buf ajax plugin is present but not enabled. ';
                //$template_init .= '<span class="badge badge-danger">PLUGIN NOT INSTALLED</span>';
                $template_init .= '<a href="index.php?option=com_plugins&view=plugins&filter[search]=buf" class="btn btn-default">Enable buf ajax plugin</a> </span>';
            }
        } else {
            $template_init .= '<span class="alert alert-error buf_ajax_not_installed"><i class="fas fa-exclamation-triangle"></i> PLUGIN NOT INSTALLED. Buf ajax plugin is required. ';
            //$template_init .= '<span class="badge badge-danger">PLUGIN NOT INSTALLED</span>';
            $template_init .= '<a href="https://jtotal.org/joomla/templates/buf-template" target="_blank" class="btn btn-default">Download buf ajax plugin</a> </span>';
        }

        //* check JTFRAMEWORK
        if (!$check_jtfw || $check_jtfw == '1.0.0') {
            $template_init .= ' <div class="alert alert-warning" role="alert">
          <strong>JT Framework required.</strong> Please, <a href="index.php?option=com_installer&view=update" class="btn btn-default">update</a> or <a href="https://users.jtotal.org/SOFT/framework/JTframework/pkg_jtfw_current.zip" target="_blank" class="btn btn-default">Download</a> </span>
          </div>';
        }

        //check LIBS
        if (is_dir(JPATH_LIBRARIES . '/jtlibs')) {
          //check libs dummy
            $template_init .= '<div>';

            if (!$check_jtlibs || $check_jtlibs == '1.0.0') {
                $template_init .= ' <div class="alert alert-warning" role="alert">
                <strong>JT libs required.</strong>
                Please, <a href="index.php?option=com_installer&view=update" class="btn btn-default">update</a>
                or
                <a href="https://users.jtotal.org/SOFT/framework/JTlibs/jtlibs_current.zip" target="_blank" class="btn btn-default">Download</a> </span>
                </div>';
            }

            $template_init .= '<nav aria-label="breadcrumb"><ul class="breadcrumb">';
            $scan = scandir(JPATH_LIBRARIES . '/jtlibs');

            foreach ($scan as $file) {
                if (is_dir(JPATH_LIBRARIES . '/jtlibs/' . $file) && $file != '.' && $file != '..') {
                    $template_init .= '/ <li><span class="label badge bg-light text-dark" >' . $file . '</span><span class="divider">  </span></li>';
                    //$template_init .= '<span class="label"> '.$file.' </span><span class="divider">/</span>';
                }
            }
            $template_init .= '</ul>';
            $template_init .= '</div>';
        } else {
            $template_init .= '<div>';
            $template_init .= '<span class="alert alert-error buf_ajax_not_installed"><i class="fas fa-exclamation-triangle"></i> Libraries not installed. ';
            //$template_init .= '<span class="badge badge-danger">PLUGIN NOT INSTALLED</span>';
            $template_init .= '<a href="https://users.jtotal.org/SOFT/framework/JTlibs/jtlibs_current.zip" target="_blank" class="btn btn-default">Download JT libraries</a> </span>';

            $template_init .= '</div>';
        }

        $template_init .= '</div>';

        $template_init = $ext_versions . $template_init;
        //check plugins
        return $template_init;
    }

    public function getLabel()
    {
        /*
        $content = '
        <p><img src="../templates/buf/images/buf_logos/logo_buf_init.png"/> Buf ajax plugin:<p>
        ';
        $content .= '<p><img src="../templates/buf/images/buf_logos/logo_buf_init.png"/> JT Libraries:<p>';
        $content .= '<p><img src="../templates/buf/images/buf_logos/logo_buf_init.png"/> JT Framework:<p>';
         */

        $content = '
      <p><img src="../templates/buf/images/buf_logos/logo_buf_init.png"/> Buf extensions:<p>
      ';
        return $content;
    }

    private function getVersion()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $element = 'bufajax';
        $folder = 'ajax';
        $query
            ->select(array('*'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
            ->where($db->quoteName('element') . ' = ' . $db->quote($element))
            ->where($db->quoteName('folder') . ' = ' . $db->quote($folder));
        $db->setQuery($query);
        $result = $db->loadObject();

        $manifest_cache = json_decode($result->manifest_cache);

        //var_dump($result->enabled);
        if (isset($manifest_cache->version)) {
            return $manifest_cache->version;
        }
        return;
    }

    //get version of plugin
    private function getExtensionVersion($element = false, $folder = false)
    {

        if (!$element) {
            return;
        }

        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select(array('*'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . ' = ' . $db->quote($element));
        if ($folder) {
            $query->where($db->quoteName('folder') . ' = ' . $db->quote($folder));
        }

        $db->setQuery($query);
        $result = $db->loadObject();

        if (!$result) {
            return false;
        }

        $manifest_cache = json_decode($result->manifest_cache);

        //var_dump($result->enabled);
        if (isset($manifest_cache->version)) {
            return $manifest_cache->version;
        }

        return;
    }
}
