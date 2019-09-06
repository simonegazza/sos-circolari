<?php
defined('_JEXEC') or die('Restricted access');

/*
 * Joomla non consente di chiamare le view utilizzando nomi scritti in
 * "camel case", pertanto il nome di questa view è tutto minuscolo
 */

class SosCircolariViewNewCircolare extends JViewLegacy
{
    function display($tpl = null)
    {
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode('<br />', $errors));

            return false;
        }

        $this->addToolBar();

        parent::display($tpl);
    }

    protected function addToolBar()
    {
        JToolBarHelper::title(JText::_('SOS Circolari'));
    }
}