<?php
/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright   Copyright (C) 2025 Jtotal. All rights reserved.
 * @license     GNU General Public License version 2 or later; see https://jtotal.org/LICENSE.txt
 */

namespace Jtotal\BUF\Site\Field;

use Joomla\CMS\Form\Field\FilelistField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\Path;

defined('_JEXEC') or die;

/**
 * Supports an HTML select list of SCSS files from elements subdirectory based on buf_layout selection
 *
 * @since  1.0.0
 */
class BufElementListField extends FilelistField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $type = 'BufElementList';

    /**
     * The layout field name to watch for changes.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $layoutField = 'buf_layout';

    /**
     * The base layouts directory.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $layoutsDirectory = '/templates/buf/layouts';

    /**
     * The subdirectory within each layout where elements are stored.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $elementsSubdirectory = 'elements';

    /**
     * Method to set certain otherwise inaccessible properties of the form field object.
     *
     * @param   string  $name   The property name for which to set the value.
     * @param   mixed   $value  The value of the property.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case 'layoutField':
            case 'layoutsDirectory':
            case 'elementsSubdirectory':
                $this->$name = (string) $value;
                break;

            default:
                parent::__set($name, $value);
        }
    }

    /**
     * Method to get certain otherwise inaccessible properties from the form field object.
     *
     * @param   string  $name  The property name for which to get the value.
     *
     * @return  mixed  The property value or null.
     *
     * @since   1.0.0
     */
    public function __get($name)
    {
        switch ($name) {
            case 'layoutField':
            case 'layoutsDirectory':
            case 'elementsSubdirectory':
                return $this->$name;
        }

        return parent::__get($name);
    }

    /**
     * Method to attach a Form object to the field.
     *
     * @param   \SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
     * @param   mixed              $value    The form field value to validate.
     * @param   string             $group    The field name group control value.
     *
     * @return  boolean  True on success.
     *
     * @since   1.0.0
     */
    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $return = parent::setup($element, $value, $group);

        if ($return) {
            // Get the layout field name (default is 'buf_layout')
            $this->layoutField = (string) $this->element['layoutfield'] ?: 'buf_layout';
            
            // Get the layouts directory (default is '/templates/buf/layouts')
            $this->layoutsDirectory = (string) $this->element['layoutsdirectory'] ?: '/templates/buf/layouts';

            // Get the elements subdirectory (default is 'elements')
            $this->elementsSubdirectory = (string) $this->element['elementssubdirectory'] ?: 'elements';

            // Set default file filter for SCSS files if not specified
            if (empty($this->fileFilter)) {
                $this->fileFilter = '\.scss$';
            }
        }

        return $return;
    }

    /**
     * Method to get the list of SCSS files from elements subdirectory based on buf_layout selection.
     *
     * @return  object[]  The field option objects.
     *
     * @since   1.0.0
     */
    protected function getOptions()
    {
        $options = [];

        // Get the buf_layout value from the form
        $layoutValue = $this->getBufLayoutValue();
        
        // Build the path based on buf_layout selection (includes elements subdirectory)
        $path = $this->buildBufLayoutElementsPath($layoutValue);

        if (!$path || !is_dir($path)) {
            // If no valid path, return only default options
            return $this->getDefaultOptions();
        }

        $path = Path::clean($path);

        // Prepend default options based on field attributes
        if (!$this->hideNone) {
            $options[] = HTMLHelper::_('select.option', '-1', Text::alt('JOPTION_DO_NOT_USE', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
        }

        if (!$this->hideDefault) {
            $options[] = HTMLHelper::_('select.option', '', Text::alt('JOPTION_USE_DEFAULT', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
        }

        // Get a list of SCSS files in the elements directory
        $files = Folder::files($path, $this->fileFilter);

        // Build the options list from the list of files
        if (\is_array($files)) {
            foreach ($files as $file) {
                // Check to see if the file is in the exclude mask
                if ($this->exclude) {
                    if (preg_match(\chr(1) . $this->exclude . \chr(1), $file)) {
                        continue;
                    }
                }

                // If the extension is to be stripped, do it
                if ($this->stripExt) {
                    $file = File::stripExt($file);
                }

                $options[] = HTMLHelper::_('select.option', $file, $file);
            }
        }

        // Merge any additional options in the XML definition
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

    /**
     * Get the buf_layout value from the form.
     *
     * @return  string  The buf_layout value.
     *
     * @since   1.0.0
     */
    protected function getBufLayoutValue()
    {
        $layoutValue = '';

        // Try to get the buf_layout value from the form
        if ($this->form && $this->layoutField) {
            // Get from current form data
            $layoutField = $this->form->getField($this->layoutField, $this->group);
            if ($layoutField) {
                $layoutValue = $layoutField->value;
            }

            // If not found, try to get from form data object
            if (!$layoutValue && $this->form->getData()) {
                $data = $this->form->getData();
                if (isset($data->{$this->layoutField})) {
                    $layoutValue = $data->{$this->layoutField};
                }
            }

            // Try to get from input data (for AJAX requests or form submissions)
            if (!$layoutValue) {
                $input = \Joomla\CMS\Factory::getApplication()->input;
                $formData = $input->get('jform', [], 'array');
                if (isset($formData[$this->layoutField])) {
                    $layoutValue = $formData[$this->layoutField];
                }
            }
        }

        return $layoutValue;
    }

    /**
     * Build the path to the elements subdirectory based on buf_layout selection.
     *
     * @param   string  $layoutValue  The buf_layout folder value.
     *
     * @return  string  The constructed path to elements subdirectory.
     *
     * @since   1.0.0
     */
    protected function buildBufLayoutElementsPath($layoutValue)
    {
        if (!$layoutValue) {
            return null;
        }

        // Construct the full path: layoutsDirectory/selectedLayout/elements/
        $fullPath = $this->layoutsDirectory . '/' . $layoutValue . '/' . $this->elementsSubdirectory;

        // If it's not an absolute path, prepend JPATH_ROOT
        if (!is_dir($fullPath)) {
            $fullPath = JPATH_ROOT . $fullPath;
        }

        return $fullPath;
    }

    /**
     * Get default options when no buf_layout is selected or elements path is invalid.
     *
     * @return  object[]  The default field option objects.
     *
     * @since   1.0.0
     */
    protected function getDefaultOptions()
    {
        $options = [];

        // Add default options
        if (!$this->hideNone) {
            $options[] = HTMLHelper::_('select.option', '-1', Text::alt('JOPTION_DO_NOT_USE', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
        }

        if (!$this->hideDefault) {
            $options[] = HTMLHelper::_('select.option', '', Text::alt('JOPTION_USE_DEFAULT', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
        }

        // Add a message indicating buf_layout selection is needed or no elements found
        $options[] = HTMLHelper::_('select.option', '', Text::_('-- Select Layout First or No Elements Found --'));

        return $options;
    }
}
