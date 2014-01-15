<?php
namespace NethServer\Module\Dashboard;

/*
 * Copyright (C) 2013 Nethesis S.r.l.
 *
 * This script is part of NethServer.
 *
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Retrieve printer list and status
 *
 * @author Giacomo Sanchietti
 */
class Printers extends \Nethgui\Controller\AbstractController
{

    public $sortId = 30;
 
    private $printers = array();
   
    private function readPrinters() {
        $pipp = new \NethServer\Tool\PrintIPP();
        $printers = array();
        $ipp = new \CupsPrintIPP();
        $ipp->setLog('/dev/null','file');
        $ipp->setUserName('admin');
        $ipp->getPrinters();
        foreach ($ipp->available_printers as $uri) {
            $ipp->setPrinterURI($uri);
            $ipp->getPrinterAttributes();
            $printers[basename($uri)] = array(
                 "enabled" => $ipp->printer_attributes->printer_is_accepting_jobs->_value0, # true or false
                 "state" => $ipp->printer_attributes->printer_state->_value0, # idle or stopped
             );
         }
         return $printers;
    } 
 
    public function process()
    {
        $this->printers = $this->readPrinters();
    }
 
    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        if (!$this->printers) {
            $this->printers = $this->readPrinters();
        }
        
        $view['printers'] = $this->printers;
    }
}
