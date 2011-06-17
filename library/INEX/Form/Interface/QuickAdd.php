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
class INEX_Form_Interface_QuickAdd extends INEX_Form
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

        
        $dbCusts = Doctrine_Query::create()
            ->from( 'Cust c' )
            ->orderBy( 'c.name ASC' )
            ->execute();

        $custs = array( '0' => '' );
        $maxId = 0;

        foreach( $dbCusts as $c )
        {
            $custs[ $c['id'] ] = "{$c['name']} [ASN{$c['autsys']}]";
            if( $c['id'] > $maxId ) $maxId = $c['id'];
        }

        $cust = $this->createElement( 'select', 'custid' );
        $cust->setMultiOptions( $custs );
        $cust->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( 'Customer' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( 'Please select a customer' ) );
        $this->addElement( $cust );
        

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




    
    
    
        
        $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='" . Zend_Controller_Front::getInstance()->getBaseUrl() . $cancelLocation . "'" ) );

        $this->addElement( 'submit', 'commit', array( 'label' => 'Add New Customer' ) );

    }

}

