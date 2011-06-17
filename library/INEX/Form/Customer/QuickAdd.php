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

/**
 *
 * @package INEX_Form
 */
class INEX_Form_Customer_QuickAdd extends INEX_Form
{

    public function __construct( $options = null, $isEdit = false, $cancelLocation )
    {
        parent::__construct( $options );

        $this->setAttrib( 'accept-charset', 'UTF-8' );
        $this->setAttrib( 'class', 'form' );

        $this->setElementDecorators(
        array(
                'ViewHelper',
                'Errors',
                array( 'HtmlTag', array( 'tag' => 'dd' ) ),
                array( 'Label', array( 'tag' => 'dt' ) ),
            )
        );

        ////////////////////////////////////////////////
        // Create and configure name element
        ////////////////////////////////////////////////

        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setRequired( true )
            ->setLabel( 'Name' )
            ->setAttrib( 'size', 60 )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $name  );

        $shortname = $this->createElement( 'text', 'shortname' );
        $shortname->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->addValidator('alnum')
            ->addValidator( 'regex', false, array('/^[a-z0-9]+/' ) )
            ->setRequired( true )
            ->setLabel( 'Short Name' )
            ->addFilter( 'StringToLower' )
            ->addFilter( 'StringTrim' );

        $this->addElement( $shortname  );

        $corpwww = $this->createElement( 'text', 'corpwww' );
        $corpwww->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setLabel( 'Corporate Website' )
            ->setAttrib( 'size', 60 )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $corpwww );

        $autsys = $this->createElement( 'text', 'autsys' );
        $autsys->addValidator('int')
            ->addValidator( 'greaterThan', false, array( -1 ) )
            ->setRequired( false )
            ->setLabel( 'AS Number' );
        $this->addElement( $autsys  );

        $maxprefixes = $this->createElement( 'text', 'maxprefixes' );
        $maxprefixes->addValidator('int')
            ->addValidator( 'greaterThan', false, array( -1 ) )
            ->setRequired( false )
            ->setLabel( 'Max Prefixes' );
        $this->addElement( $maxprefixes  );

        $peeringemail = $this->createElement( 'text', 'peeringemail' );
        $peeringemail->addValidator('emailAddress' )
            ->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( true )
            ->setAttrib( 'size', 60 )
            ->setLabel( 'Peering E-Mail' );
        $this->addElement( $peeringemail );

        $this->addDisplayGroup(
            array(
            	'name', 'shortname', 'corpwww', 'autsys', 'maxprefixes', 'peeringemail'
            ),
    		'peeringDisplayGroup'
            );
        $this->getDisplayGroup( 'peeringDisplayGroup' )->setLegend( 'Customer Details' );

        $nocphone = $this->createElement( 'text', 'nocphone' );
        $nocphone->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setLabel( 'NOC Phone' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $nocphone );

        $noc24hphone = $this->createElement( 'text', 'noc24hphone' );
        $noc24hphone->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setLabel( 'NOC 24h Phone' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $noc24hphone );

        $nocfax = $this->createElement( 'text', 'nocfax' );
        $nocfax->addValidator( 'stringLength', false, array( 0, 40 ) )
            ->setRequired( false )
            ->setLabel( 'NOC Fax' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $nocfax );

        $nocemail = $this->createElement( 'text', 'nocemail' );
        $nocemail->addValidator('emailAddress' )
            ->setAttrib( 'size', 60 )
            ->addValidator( 'stringLength', false, array( 0, 40 ) )
            ->setRequired( true )
            ->setLabel( 'NOC E-Mail' );
        $this->addElement( $nocemail );

        $nochours = $this->createElement( 'text', 'nochours' );
        $nochours->addValidator( 'stringLength', false, array( 0, 40 ) )
            ->setRequired( false )
            ->setLabel( 'NOC Hours' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $nochours );

        $nocwww = $this->createElement( 'text', 'nocwww' );
        $nocwww->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setLabel( 'NOC WWW' )
            ->setAttrib( 'size', 60 )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $nocwww );

        $this->addDisplayGroup(
            array( 'nocphone', 'noc24hphone', 'nocfax', 'nocemail', 'nochours', 'nocwww' ),
        	'nocDisplayGroup'
        );
        $this->getDisplayGroup( 'nocDisplayGroup' )->setLegend( 'NOC Details' );

        


        
        
        
        $ipv4enabled = $this->createElement( 'checkbox', 'ipv4enabled' );
        $ipv4enabled->setLabel( 'IPv4 Enabled?' )
            ->setCheckedValue( '1' );
        $this->addElement( $ipv4enabled );

        $ipv4addressid = $this->createElement( 'select', 'ipv4addressid' );

        $collection = Doctrine_Query::create()
            ->from( 'Ipv4address ipv4' )
            ->leftJoin( 'ipv4.Vlaninterface vli' )
            ->leftJoin( 'ipv4.Vlan v' )
            ->where( 'vli.id IS NULL' )
            ->orWhere( 'vli.id = ?', Zend_Controller_Front::getInstance()->getRequest()->getParam( 'id' ) )
            ->orderBy( 'ipv4.address ASC, ipv4.vlanid ASC' )
            ->execute();

        $options = array( '0' => '' );
        $maxId = 0;

        foreach( $collection as $c )
        {
            $options[ $c['id'] ] = "VLAN {$c['Vlan']['number']} - {$c['address']}";

            if( $c['id'] > $maxId ) $maxId = $c['id'];
        }


        $ipv4addressid->setMultiOptions( $options )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'IPv4 Address' )
            ->addValidator( 'between', false, array( 0, $maxId ) )
            ->setErrorMessages( array( 'Please select a IPv4 address' ) );
        $this->addElement( $ipv4addressid );



        $ipv4hostname = $this->createElement( 'text', 'ipv4hostname' );
        $ipv4hostname->addValidator( 'stringLength', false, array( 1, 64 ) )
            ->setLabel( 'IPv4 Hostname' )
            ->setAttrib( 'size', 60 )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $ipv4hostname  );

        $ipv4bgpmd5secret = $this->createElement( 'text', 'ipv4bgpmd5secret' );
        $ipv4bgpmd5secret->addValidator( 'stringLength', false, array( 1, 64 ) )
            ->setLabel( 'IPv4 BGP MD5 Secret' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $ipv4bgpmd5secret  );



        $this->addDisplayGroup(
        array( 'ipv4enabled', 'ipv4addressid', 'ipv4hostname', 'ipv4bgpmd5secret' ),
            'ipv4DisplayGroup'
            );
            $this->getDisplayGroup( 'ipv4DisplayGroup' )->setLegend( 'IPv4 Details' );


        $ipv6enabled = $this->createElement( 'checkbox', 'ipv6enabled' );
        $ipv6enabled->setLabel( 'IPv6 Enabled?' )
            ->setCheckedValue( '1' );
        $this->addElement( $ipv6enabled );


        $ipv6address = $this->createElement( 'text', 'ipv6address' );
        $ipv6address->addValidator( 'stringLength', false, array( 9, 39 ) )
            ->setLabel( 'IPv6 Address' )
            ->setAttrib( 'size', 60 )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $ipv6address  );

        $ipv6bgpmd5secret = $this->createElement( 'text', 'ipv6bgpmd5secret' );
        $ipv6bgpmd5secret->addValidator( 'stringLength', false, array( 1, 64 ) )
            ->setLabel( 'IPv6 BGP MD5 Secret' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $ipv6bgpmd5secret  );
        
        

        $ipv6hostname = $this->createElement( 'text', 'ipv6hostname' );
        $ipv6hostname->addValidator( 'stringLength', false, array( 1, 64 ) )
            ->setLabel( 'IPv6 Hostname' )
            ->setAttrib( 'size', 60 )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $ipv6hostname  );


        $this->addDisplayGroup(
            array( 'ipv6enabled', 'ipv6address', 'ipv6hostname', 'ipv6bgpmd5secret' ),
            	'ipv6DisplayGroup'
            );
        $this->getDisplayGroup( 'ipv6DisplayGroup' )->setLegend( 'IPv6 Details' );




    
    
    
        
        $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='" . Zend_Controller_Front::getInstance()->getBaseUrl() . "{$cancelLocation}'" ) );

        $this->addElement( 'submit', 'commit', array( 'label' => 'Add New Customer' ) );

    }

}

