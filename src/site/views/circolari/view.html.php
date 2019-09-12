<?php
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.controller' );

class SosCircolariViewCircolari extends JViewLegacy
{
    function display($tpl = null)
    {
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');

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
        JToolBarHelper::addNew('circolari.add');
        JToolBarHelper::title(JText::_("SOS Circolari"));
        JToolBarHelper::addNew("newcircolare.add");
    }
}