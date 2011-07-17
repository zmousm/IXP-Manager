<?php

/*
 * Copyright (C) 2009-2011 Internet Neutral Exchange Association Limited.
 * All Rights Reserved.
 * 
 * This file is part of IXP Manager.
 * 
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 * 
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 * 
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 * 
 * http://www.gnu.org/licenses/gpl-2.0.html
 */


/*
 *
 *
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 */

class VirtualInterfaceController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'name';
        $this->frontend['model']           = 'Virtualinterface';
        $this->frontend['name']            = 'VirtualInterface';
        $this->frontend['pageTitle']       = 'Virtual Interfaces';

        $this->frontend['columns'] = array( 'ignoreme' );

        parent::feInit();
    }

    /**
     * If deleting a virtual interface, we should also the delete the physical and vlan interfaces
     * if they exist.
     *
     */
    protected function preDelete( $object = null )
    {
        if( ( $oid = $this->getRequest()->getParam( 'id', null ) ) === null )
            return false;

        if( !( $vint = Doctrine::getTable( $this->getModelName() )->find( $oid ) ) )
            return false;

        foreach( $vint->Physicalinterface as $pi )
        {
            $this->logger->notice( "Deleting physical interface with id #{$pi->id} while deleting virtual interface #{$vint->id}" );
            $pi->delete();
        }

        foreach( $vint->Vlaninterface as $vl )
        {
            $this->logger->notice( "Deleting vlan interface with id #{$vl['id']} while deleting virtual interface #{$vint['id']}" );
            $vl->delete();
        }
    }


    //addEditPreDisplay
    function addEditPreDisplay( $form, $object )
    {
        // did we get a customer id from the provisioning controller?
        if( $this->_getParam( 'prov_cust_id', false ) )
        {
            $form->getElement( 'custid' )->setValue( $this->_getParam( 'prov_cust_id' ) );

            $form->getElement( 'cancel' )->setAttrib( 'onClick',
                "parent.location='" . $this->config['identity']['ixp']['url']
                    . '/provision/interface-overview/id/' . $this->session->provisioning_interface_active_id . "'"
            );
        }

        $dataQuery1 = Doctrine_Query::create()
	        ->from( 'Physicalinterface pi' )
	        ->leftJoin( 'pi.Switchport sp' )
	        ->leftJoin( 'sp.SwitchTable s' )
	        ->leftJoin( 's.Cabinet cb' )
	        ->leftJoin( 'cb.Location l' )
	        ->where( 'pi.Virtualinterface.id = ?', $this->getRequest()->getParam( 'id' ) );

        $this->view->phyInts = $dataQuery1->execute();

        $dataQuery2 = Doctrine_Query::create()
	        ->from( 'Vlaninterface vli' )
	        ->leftJoin( 'vli.Virtualinterface vi' )
	        ->leftJoin( 'vli.Ipv4address v4' )
	        ->leftJoin( 'vli.Ipv6address v6' )
	        ->leftJoin( 'vli.Vlan v' )
	        ->where( 'vi.id = ?', $this->getRequest()->getParam( 'id' ) );

        $this->view->vlanInts = $dataQuery2->execute();
    }


    /**
     * A generic action to list the elements of a database (as represented
     * by a Doctrine model) via Smarty templates.
     */
    public function getDataAction()
    {
        $dataQuery = Doctrine_Query::create()
	        ->from( 'Virtualinterface vi' )
	        ->leftJoin( 'vi.Cust c' )
	        ->leftJoin( 'vi.Physicalinterface pi' )
	        ->leftJoin( 'pi.Switchport sp' )
	        ->leftJoin( 'sp.SwitchTable s' )
	        ->leftJoin( 's.Cabinet cb' )
	        ->leftJoin( 'cb.Location l' )
	        ->orderBy( 'c.shortname ASC' );

        if( $this->getRequest()->getParam( 'member' ) !== NULL )
            $dataQuery->andWhere( 'c.name LIKE ?', $this->getRequest()->getParam( 'member' ) . '%' );

        if( $this->getRequest()->getParam( 'shortname' ) !== NULL )
            $dataQuery->andWhere( 'c.shortname LIKE ?', $this->getRequest()->getParam( 'shortname' ) . '%' );


        $rows = $dataQuery->execute();

        // FIXME :: below assumes a single physical interface for a virtual interface so port channels are not catered for

        $count = 0;
        $data = '';
        foreach( $rows as $row )
        {
            if( $count > 0 )
                $data .= ',';

            $count++;

            $data .= <<<END_JSON
    {
        "member":"{$row['Cust']['name']}",
        "memberid":"{$row['Cust']['id']}",
        "id":"{$row['id']}",
        "description":"{$row['description']}",
        "shortname":"{$row['Cust']['shortname']}",
        "location":"{$row['Physicalinterface'][0]['Switchport']['SwitchTable']['Cabinet']['Location']['name']}",
        "locationid":"{$row['Physicalinterface'][0]['Switchport']['SwitchTable']['Cabinet']['Location']['id']}",
        "switch":"{$row['Physicalinterface'][0]['Switchport']['SwitchTable']['name']}",
        "switchid":"{$row['Physicalinterface'][0]['Switchport']['SwitchTable']['id']}",
        "port":"{$row['Physicalinterface'][0]['Switchport']['name']}",
        "speed":"{$row['Physicalinterface'][0]['speed']}",
    }
END_JSON;

        }

        $data = <<<END_JSON
{"ResultSet":{
    "totalResultsAvailable":{$count},
    "totalResultsReturned":{$count},
    "firstResultPosition":0,
    "Result":[{$data}]}}
END_JSON;

        echo $data;

    }
    
    
    public function quickAddAction()
    {
        
        $f = new INEX_Form_Interface_QuickAdd( null, false, '/virtual-interface' );

        // Process a submitted form if it passes initial validation
        if( $this->inexGetPost( 'commit' ) !== null && $f->isValid( $_POST ) )
        {
            do
            {
                // check customer information
                if( !( $c = Doctrine_Core::getTable( 'Cust' )->find( $f->getValue( 'custid' ) ) ) ) 
                {
                    $f->getElement( 'custid' )->addError( 'Invalid customer' );
                    break;
                }
                
                // get the default vlan
                $vlan = Doctrine_Core::getTable( 'Vlan' )->find( 1 );
                
                if( !$vlan )
                {
                    $vlan = new Vlan();
                    $vlan['name']   = 'QuickAdd-Dummy-VLAN';
                    $vlan['number'] = '10';
                    $vlan->save();
                }
                
                
                // we need one of v4 or v6
                $ipv4 = (bool)$f->getValue( 'ipv4enabled' );
                $ipv6 = (bool)$f->getValue( 'ipv6enabled' );

                if( $ipv4 )
                {
                    // is the IPv4 address valid and has it been used?
                    $ipv4addr = Doctrine_Core::getTable( 'Ipv4address' )->find( $f->getValue( 'ipv4addressid' ) );
                    
                    if( !$ipv4addr )
                    {
                        $f->getElement( 'ipv4addressid' )->addError( 'The provided address is invalid' );
                        break;
                    }

                    if( Doctrine_Core::getTable( 'Vlaninterface' )->findOneByIpv4addressid( $ipv4addr['id'] ) )
                    {
                        $f->getElement( 'ipv4addressid' )->addError( 'The provided address is in use already' );
                        break;
                    }
                }
                
                if( $ipv6 )
                {
                    // is the IPv6 address valid and has it been used?
                    $ipv6addr = Doctrine_Core::getTable( 'Ipv6address' )->findOneByAddress( $f->getValue( 'ipv6address' ) );
                    
                    if( !$ipv6addr )
                    {
                        // create it
                        $ipv6addr = new Ipv6address();
                        $ipv6addr['address'] = $f->getValue( 'ipv6address' );
                        $ipv6addr['vlanid']  = $vlan['id'];
                        $ipv6addr->save();
                    }
                    else if( Doctrine_Core::getTable( 'Vlaninterface' )->findOneByIpv6addressid( $ipv6addr['id'] ) )
                    {
                        $f->getElement( 'ipv6address' )->addError( 'The provided address is in use already' );
                        break;
                    }
                }
                
                
                // create the entities
                $conn = Doctrine_Manager::connection();
                $conn->beginTransaction();
                
                try
                {
                    // virtual interface
                    $vi = new Virtualinterface();
                    $vi['custid'] = $c['id'];
                    $vi['name']   = $c['shortname'];
                    $vi->save();
                    
                    // load or create the dummy switch
                    $s = SwitchTable::getDummySwitch();
                    
                    // and we need a port
                    $sp             = new Switchport();
                    $sp['switchid'] = $s['id'];
                    $sp['type']     = Switchport::TYPE_PEERING;
                    $sp['name']     = "DUMMY-{$c['shortname']}";
                    $sp->save();
                    
                    
                    // and now a physical interface
                    $pi                       = new Physicalinterface();
                    $pi['switchportid']       = $sp['id'];
                    $pi['virtualinterfaceid'] = $vi['id'];
                    $pi['status']             = Physicalinterface::STATUS_CONNECTED;
                    $pi['speed']              = 1000;
                    $pi['duplex']             = 'full';
                    $pi['monitorindex']       = 1;
                    $pi->save();
                    
                    
                    // and lastly, the VLAN interface
                    $vli = new Vlaninterface();
                    
                    $vli['virtualinterfaceid'] = $vi['id'];
                    $vli['vlanid']             = $vlan['id'];
                    $vli['ipv4enabled']        = $ipv4;
                    
                    if( $ipv4 )
                        $vli['ipv4addressid']  = $ipv4addr['id'];
                        
                    $vli['ipv4hostname']       = $f->getValue( 'ipv4hostname' );
                    $vli['ipv6enabled']        = $ipv6;
                    
                    if( $ipv6 )
                        $vli['ipv6addressid']  = $ipv6addr['id'];

                    $vli['ipv6hostname']       = $f->getValue( 'ipv6hostname' );
                    $vli['bgpmd5secret']       = $f->getValue( 'ipv4bgpmd5secret' );
                    $vli['ipv4bgpmd5secret']   = $f->getValue( 'ipv4bgpmd5secret' );
                    $vli['ipv6bgpmd5secret']   = $f->getValue( 'ipv6bgpmd5secret' );
                    $vli['maxbgpprefix']       = $f->getValue( 'maxprefixes' );
                    $vli['rsclient']           = 1;
                    
                    $vli->save();
                    
                    $conn->commit();
                }
                catch( Exceltion $e )
                {
                    $conn->rollback();
                }
                
                $this->logger->notice( 'New \'Quick Add\' interface created' );
                $this->session->message = new INEX_Message( "New interface added", "success" );
                $this->_redirect( 'virtual-interface/list' );
                
            }while( false );
        }

        $this->view->form   = $f->render( $this->view );

        $this->view->display( 'virtual-interface' . DIRECTORY_SEPARATOR . 'quick-add.tpl' );

    }
    
    /**
     * Hook function to set a customer return.
     * 
     * We want to display the virtual interface which was added / edited.
	 *
     * @param INEX_Form_SwitchPort $f
     * @param Switchport $o
     */
    protected function _addEditSetReturnOnSuccess( $f, $o )
    {
        return 'virtual-interface/edit/id/' . $o['id'];
    }
    

}

