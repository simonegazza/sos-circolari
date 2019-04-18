<?php
defined('_JEXEC') or die('Restricted access');

class SosCircolariViewHelloWorld extends JViewLegacy
{
    function display($tpl = null)
    {
        $this->msg = 'SOS Circolari';
        parent::display($tpl);
    }
}
