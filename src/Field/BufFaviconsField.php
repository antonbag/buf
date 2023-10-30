<?php
/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © 2023 JTOTAL All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace Jtotal\BUF\Site\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Layout\LayoutHelper;

//no direct access
defined('_JEXEC') or die;

class BufFaviconsField extends FormField
{
    protected $type = 'BufFavicons';
    protected function getInput()
    {
        $jinput = Factory::getApplication()->input;

        $templateid = $jinput->get('id');
        $params = new Registry($this->form->getData("data")->get('params'));

        $svg_image = HTMLHelper::cleanImageURL($params->get('buf_favicon_svg', ''));

        $upload_svg = "

        function buf_favicon_svg(detelesvg='false'){

          var data =  new FormData();

          if(detelesvg=='false'){
            data.append( 'upload_file_svg', jQuery('#jform_params_buf_favicon_svg')[0].files[0]);
          }

          data.append( 'token', '" . Session::getFormToken() . "');
          data.append( 'templateid', '" . $templateid . "');
          data.append( 'buf_layout', '" . $params->get('buf_layout', 'default') . "');

          var getUrl = 'index.php?option=com_ajax&group=ajax&plugin=bufajax&format=json&action=do_get_favicon_svg&raw=true&detelesvg='+detelesvg;

          var jqxhr = jQuery.ajax({

            type: 'POST',
            url: getUrl,
            cache: false,
            dataType: 'json',
            data:data,
            processData: false,
            contentType: false,
            jsonp: false

          }).done(function(e) {
            //mapbutton();
            console.log(e.data);
            if(e.data == 'deleted'){
              jQuery('.buffavicons_thumbs_svg_img img').hide();

            }else{
              jQuery('.buffavicons_thumbs_svg_img img').attr('src',e.data).show();
            }

          })
          .fail(function(e) {
            console.log('ERROR');
            console.log(e);
          });
        }
        ";

        /** @var CMSApplication $app */
        $app = Factory::getApplication();
        $doc = $app->getDocument();
        $doc->addCustomTag('<script type="text/javascript">' . $upload_svg . '</script>');

        //check plugins
        $favicons = '';

        if (file_exists(JPATH_SITE . '/templates/buf/layouts/' . $params->get('buf_layout', 'default') . '/icons/favicon-32x32.png')) {
            $favicons .= '<div class="buffavicons_thumbs_wrapper">';

            if (file_exists(JPATH_SITE . '/' . $svg_image->url)) {
                $favicons .= '<div class="buffavicons_thumbs buffavicons_thumbs_svg">';
                $favicons .= '<strong>svg</strong><a class="btn btn_svg_destroy" onclick="buf_favicon_svg(\'true\')" style="float:right;">x</a>';
                $favicons .= '<div class="buffavicons_thumbs_svg_img">';

                $favicons .= LayoutHelper::render('joomla.html.image', ['src' => Uri::root(true) . '/' . $svg_image->url, 'alt' => 'logo favicon']);

                $favicons .= '</div>';
                $favicons .= '</div>';
            }

            $favicons .= '<div class="buffavicons_thumbs">';
            $favicons .= '<img src="../templates/buf/layouts/' . $params->get('buf_layout', 'default') . '/icons/favicon-96x96.png" title="96x96"/>';
            $favicons .= '</div>';

            $favicons .= '<div class="buffavicons_thumbs">';
            $favicons .= '<img src="../templates/buf/layouts/' . $params->get('buf_layout', 'default') . '/icons/apple-icon-57x57.png" title="57x57"/>';
            $favicons .= '</div>';

            $favicons .= '<div class="buffavicons_thumbs">';
            $favicons .= '<img src="../templates/buf/layouts/' . $params->get('buf_layout', 'default') . '/icons/favicon-16x16.png" title="16x16"/>';
            $favicons .= '</div>';

            $favicons .= '</div>';
        } else {
            $favicons .= '<div class="buffavicons_thumbs">';
            $favicons .= Text::_('TPL_BUF_FAVICON_NOT_CREATED');
            $favicons .= '</div>';
        }
        $favicons .= '<div class="buffavicons_messages">';
        $favicons .= '</div>';

        return $favicons;
    }

    public function getLabel()
    {
        //return JText::_('TPL_BUF_CURRENT_FAVICON');
    }
}
