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

class CustomerController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'name';
        $this->frontend['model']           = 'Cust';
        $this->frontend['name']            = 'Customer';
        $this->frontend['pageTitle']       = 'IXP Members';

        $this->frontend['columns'] = array(

            'displayColumns' => array(
                'id', 'name', 'autsys', 'shortname', 'peeringemail', 'nocphone'
            ),

            // Customer can update:
            'updatableColumns' => array( 'peeringemail', 'noc24hphone', 'nocphone', 'nocemail', 'nocfax', 'nochours', 'nocwww',
                'billingContact', 'billingAddress1', 'billingAddress2', 'billingCity', 'billingCountry', 'corpwww'
            ),

            'viewPanelRows'  => array( 'name', 'type', 'status', 'shortname', 'autsys', 'peeringemail', 'nocphone', 'nocemail', 'noc24hphone', 'nocfax',
                'nochours', 'nocwww', 'irrdb', 'peeringmacro', 'peeringpolicy', 'maxprefixes',
                'billingContact', 'billingAddress1', 'billingAddress2', 'billingCity', 'billingCountry',
                'corpwww', 'datejoin', 'dateleave', 'activepeeringmatrix', 'notes'
            ),

            'viewPanelTitle' => 'name',

            'id' => array(
                'label' => 'ID',
                'hidden' => true
            ),


            'name' => array(
                'label' => 'Member',
                'sortable' => true,
                'searchable' => true,
                'search' => array(
                    'type' => 'text',
                    'beginsWith' => true
                )
            ),

            'type' => array(
                'label' => 'Type',
                'sortable' => false,
                'type' => 'xlate',
                'xlator' => Cust::$CUST_TYPES_TEXT
            ),

            'status' => array(
                'label' => 'Status',
                'sortable' => false,
                'type' => 'xlate',
                'xlator' => Cust::$CUST_STATUS_TEXT
            ),

            'shortname' => array(
                'label' => 'Short Name',
                'sortable' => true,
                'searchable' => true,
                'search' => array(
                    'type' => 'text',
                    'beginsWith' => true
                )
            ),

            'autsys' => array(
                'label' => 'AS',
                'sortable' => true,
                'searchable' => true,
                'search' => array(
                    'type' => 'text',
                    'beginsWith' => true
                )
            ),

            'peeringemail' => array(
                'label' => 'Peering E-Mail',
                'sortable' => false
            ),

            'nocphone' => array(
                'label' => 'NOC Phone'
            ),

            'noc24hphone' => array(
                'label' => 'NOC 24hr Phone'
            ),

            'nocemail' => array(
                'label' => 'NOC E-mail'
            ),

            'nocfax' => array(
                'label' => 'NOC Fax'
            ),

            'nochours' => array(
                'label' => 'NOC Hours'
            ),

            'nocwww' => array(
                'label' => 'NOC Website'
            ),

            'irrdb' => array(
                'type' => 'hasOne',
                'model' => 'Irrdbconfig',
                'controller' => 'irrdb-config',
                'field' => 'source',
                'label' => 'IRRDB',
                'sortable' => true
            ),

            'peeringmacro' => array(
                'label' => 'Peering Macro'
            ),

            'peeringpolicy' => array(
                'label' => 'Peering Policy'
            ),

            'maxprefixes' => array(
                'label' => 'Max Prefixes'
            ),

            'billingContact' => array(
                'label' => 'Billing Contact'
            ),

            'billingAddress1' => array(
                'label' => 'Billing Address 1'
            ),

            'billingAddress2' => array(
                'label' => 'Billing Address 2'
            ),

            'billingCity' => array(
                'label' => 'Billing City'
            ),

            'billingCountry' => array(
                'label' => 'Billing Country'
            ),

            'corpwww' => array(
                'label' => 'Corporate Website'
            ),

            'datejoin' => array(
                'label' => 'Date Joined'
            ),

            'dateleave' => array(
                'label' => 'Date Left'
            ),

            'activepeeringmatrix' => array(
                'label' => 'Active Peering Matrix'
            ),

            'notes' => array(
                'label' => 'Notes'
            )

        );


        // Override global auth level requirement for specific actions
        $this->frontend['authLevels'] = array(
            'update-attribute' => User::AUTH_CUSTUSER
        );

        parent::feInit();
    }


    /**
     * Additional checks when a new object is being added.
     */
    protected function formValidateForAdd( $form )
    {

        if( Doctrine::getTable( $this->getModelName() )->findOneByShortname( $form->getValue( 'shortname' ) ) )
        {
            $form->getElement( 'shortname' )->addError( 'This short name is not available' );
            return false;
        }
    }



    /**
     * A generic action to list the elements of a database (as represented
     * by a Doctrine model) via Smarty templates.
     */
    public function getDataAction()
    {
        $dataQuery = Doctrine_Query::create()
        ->from( $this->getModelName() . ' x' )
        ->orderBy( 'x.shortname ASC' );

        if( $this->getRequest()->getParam( 'member' ) !== NULL )
        $dataQuery->andWhere( 'x.name LIKE ?', $this->getRequest()->getParam( 'member' ) . '%' );

        if( $this->getRequest()->getParam( 'shortname' ) !== NULL )
        $dataQuery->andWhere( 'x.shortname LIKE ?', $this->getRequest()->getParam( 'shortname' ) . '%' );


        $rows = $dataQuery->execute();

        $count = 0;
        $data = '';
        foreach( $rows as $row )
        {
            if( $count > 0 )
            $data .= ',';

            $count++;

            $data .= <<<END_JSON
    {
        "member":"{$row['name']}",
        "id":"{$row['id']}",
        "autsys":"{$row['autsys']}",
        "shortname":"{$row['shortname']}",
        "peeringemail":"{$row['peeringemail']}",
        "nocphone":"{$row['nocphone']}"
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


    /**
     * Function to allow members to alter their own details
     */
    public function updateAttributeAction()
    {
        // let's get the user's details sorted before everything else
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $customer = Doctrine::getTable( 'Cust' )->find( $identity['user']['custid'] );

        if( in_array( $this->getRequest()->getParam( 'attribute' ), $this->frontend['columns']['updatableColumns'] ) )
        {
            try
            {
                $customer[$this->getRequest()->getParam( 'attribute' )] = stripslashes( trim( $this->getRequest()->getParam( 'newValue' ) ) );
                $customer->save();
                echo( '1:Member details sucessfully updated.' );
            }
            catch( DoctrineException $e )
            {
                echo( '0:Error updating member details. Please contact ' . $this->_config['identity']['email'] . '.' );
            }
        }
        else
        {
            $this->logger->alert( "{$identity['user']['username']} tried to alter " . $this->getRequest()->getParam( 'attribute' ) . " via updateAttributeAction" );
            echo( '0:Bad request. This action has been logged. Please contact ' . $this->_config['identity']['email'] . ' for assistance if necessary.' );
        }

    }


    /**
     * Send the member an operations welcome mail
     *
     */
    public function sendWelcomeEmailAction()
    {
        // Is the customer ID valid?
        if( $this->getRequest()->getParam( 'id' ) !== NULL && is_numeric( $this->getRequest()->getParam( 'id' ) ) )
        {
            if( !( $customer = Doctrine::getTable( 'Cust' )->find( $this->getRequest()->getParam( 'id' ) ) ) )
            {
                $this->view->message = new INEX_Message( 'Invalid Member ID', "error" );
                return( $this->_forward( 'list' ) );
            }

            $this->view->customer = $customer;
        }
        else
        {
            $this->view->message = new INEX_Message( 'Invalid Member ID', "error" );
            return( $this->_forward( 'list' ) );
        }


        $cancelLocation = 'http' . ( isset( $_SERVER['HTTPS'] ) ? 's' : '' ) . '://'
            . $_SERVER['SERVER_NAME'] . Zend_Controller_Front::getInstance()->getBaseUrl()
            . '/' . $this->getRequest()->getParam( 'controller' ) . '/list';

        $form = new INEX_Form_Customer_SendWelcomeEmail( null, false, $cancelLocation );

        $form->getElement( 'to' )->setValue( $customer['nocemail'] );

        $userEmails = array();
        foreach( $customer['User'] as $user )
        {
            if( Zend_Validate::is( $user['email'], 'EmailAddress' ) )
                $userEmails[] = $user['email'];
        }

        $form->getElement( 'cc' )->setValue( implode( ',', $userEmails ) );

        $form->getElement( 'bcc' )->setValue( $this->_config['identity']['email'] );
        $form->getElement( 'subject' )->setValue( $this->_config['identity']['name'] . ' :: Welcome Mail' );

        // Let's get the information we need for the welcome mail from the database.

        $this->view->networkInfo = Networkinfo::toStructuredArray();

        $this->view->connections = Doctrine_Query::create()
	        ->from( 'Virtualinterface vi' )
	        ->leftJoin( 'vi.Cust c' )
	        ->leftJoin( 'vi.Physicalinterface pi' )
	        ->leftJoin( 'vi.Vlaninterface vli' )
	        ->leftJoin( 'vli.Ipv4address v4' )
	        ->leftJoin( 'vli.Ipv6address v6' )
	        ->leftJoin( 'vli.Vlan v' )
	        ->leftJoin( 'pi.Switchport sp' )
	        ->leftJoin( 'sp.SwitchTable s' )
	        ->leftJoin( 's.Cabinet cb' )
	        ->leftJoin( 'cb.Location l' )
	        ->where( 'c.id = ?', $customer['id'] )
	        ->orderBy( 'pi.monitorindex' )
	        ->execute()
	        ->toArray( true );

	    $this->logger->debug( print_r( $this->view->connections, true ) );

        $form->getElement( 'message' )->setValue( $this->view->render( 'customer' . DIRECTORY_SEPARATOR . 'welcomeEmail.tpl' ) );


        // Process a submitted form if it passes initial validation
        if( $this->inexGetPost( 'commit' ) !== null && $form->isValid( $_POST ) )
        {
            $validForm = true;
            // Validate all e-mail addresses
            foreach( array( 'to', 'cc', 'bcc' ) as $recipient )
            {
                if( $form->getValue( $recipient ) != '' )
                {
	                foreach( explode( ',', $form->getElement( $recipient )->getValue() ) as $email )
	                {
	                    if( !Zend_Validate::is( $email, 'EmailAddress' ) )
	                    {
	                        $form->getElement( $recipient )->addError( 'Invalid e-mail address: ' . $email );
	                        $validForm = false;
	                    }
	                }
                }
            }

            if( $validForm )
            {
                $mail = new Zend_Mail();
                $mail->setBodyText( $form->getValue( 'message' ) );
                $mail->setFrom( $this->_config['identity']['email'], $this->_config['identity']['name'] );
                $mail->setSubject( $form->getValue( 'subject' ) );

                foreach( array( 'To', 'Cc', 'Bcc' ) as $recipient )
                    if( $form->getValue( strtolower( $recipient ) ) != '' )
                        foreach( explode( ',', $form->getElement( strtolower( $recipient ) )->getValue() ) as $email )
	                        if( Zend_Validate::is( $email, 'EmailAddress' ) )
	                        {
	                            $fn = "add$recipient";
                                $mail->$fn( $email );
	                        }

                if( $mail->send() )
                {
                    $this->logger->info( "Welcome email sent for {$customer['name']}" );
                    $this->view->message = new INEX_Message( "Welcome email successfully sent to {$customer['name']}", "success" );
                    $this->_forward( 'list' );
                    return true;
                }
                else
                {
                    $this->logger->err( "Could not sent welcome email for {$customer['name']}: " . print_r( $mail, true ) );
                    $this->view->message = new INEX_Message( "Welcome email could not be sent to {$customer['name']}. Please see logs for more verbose output.", "error" );
                }

            }
        }

        $this->view->form   = $form->render( $this->view );

        $this->view->display( 'customer' . DIRECTORY_SEPARATOR . 'sendWelcomeEmail.tpl' );
    }


    public function leagueTableAction()
    {
        $metrics = array(
            'Total'   => 'data',
            'Max'     => 'max',
            'Average' => 'average'
        );

        $metric = $this->_request->getParam( 'metric', $metrics['Data'] );
        if( !in_array( $metric, $metrics ) )
            $metric = $metrics['Data'];

        $day = $this->_request->getParam( 'day', date( 'Y-m-d' ) );
        if( !Zend_Date::isDate( $day, 'Y-m-d' ) )
            $day = date( 'Y-m-d' );

        $category = $this->_request->getParam( 'category', INEX_Mrtg::$CATEGORIES['Bits'] );

        if( !in_array( $category, INEX_Mrtg::$CATEGORIES ) )
            $category = INEX_Mrtg::$CATEGORIES['Bits'];

        // load values from the database
        $this->view->trafficDaily = Doctrine_Query::create()
            ->from( 'TrafficDaily td' )
            ->where( 'day = ?', $day )
            ->andWhere( 'category = ?', $category )
            ->execute();

        $this->view->day        = $day;
        $this->view->category   = $category;
        $this->view->categories = INEX_Mrtg::$CATEGORIES;
        $this->view->metric     = $metric;
        $this->view->metrics    = $metrics;
        $this->view->display( 'customer' . DIRECTORY_SEPARATOR . 'leagueTable.tpl' );
    }

    public function ninetyFifthAction()
    {
        $month = $this->_request->getParam( 'month', date( 'Y-m-01' ) );

        $cost = $this->_request->getParam( 'cost', "20.00" );
        if( !is_numeric( $cost ) )
            $cost = "20.00";
        $this->view->cost = $cost;

        $months = array();
        for( $year = 2010; $year <= date( 'Y' ); $year++ )
            for( $mth = ( $year == 2010 ? 4 : 1 ); $mth <= ( $year == 2010 ? date('n') : 12 ); $mth++ )
            {
                $ts = mktime( 0, 0, 0, $mth, 1, $year );
                $months[date( 'M Y', $ts )] = date( 'Y-m-01', $ts );
            }

        $this->view->months = $months;

        if( in_array( $month, array_values( $months ) ) )
            $this->view->month = $month;
        else
            $this->view->month = date( 'Y-m-01' );

        // load values from the database
        $traffic95thMonthly = Doctrine_Query::create()
            ->from( 'Traffic95thMonthly tf' )
            ->leftJoin( 'tf.Cust c' )
            ->where( 'month = ?', $month )
            ->execute()
            ->toArray();

        foreach( $traffic95thMonthly as $index => $row )
            $traffic95thMonthly[$index]['cost'] = sprintf( "%0.2f", $row['max_95th'] / 1024 / 1024 * $cost );

        $this->view->traffic95thMonthly = $traffic95thMonthly;

        $this->view->display( 'customer' . DIRECTORY_SEPARATOR . 'ninety-fifth.tpl' );
    }

    public function statisticsOverviewAction()
    {
        $category = $this->_request->getParam( 'category', INEX_Mrtg::$CATEGORIES['Bits'] );

        if( !in_array( $category, INEX_Mrtg::$CATEGORIES ) )
            $category = INEX_Mrtg::$CATEGORIES['Bits'];

        $period = $this->_request->getParam( 'period', INEX_Mrtg::$PERIODS['Day'] );

        if( !in_array( $period, INEX_Mrtg::$PERIODS ) )
            $period = INEX_Mrtg::$PERIODS['Day'];

        $this->view->custs = Doctrine_Query::create()
            ->select( 'c.shortname' )
            ->addSelect( 'c.name' )
            ->from( 'Cust c' )
            ->whereIn( 'c.type', array( Cust::TYPE_FULL, Cust::TYPE_INTERNAL, Cust::TYPE_PROBONO ) )
            ->andWhere( 'c.status = ?', array( Cust::STATUS_NORMAL ) )
            ->andWhere( 'c.dateleave = 0 or c.dateleave IS NULL' )
            ->andWhereIn( 'c.shortname', array( 'inex', 'routeservers' ), true )
            ->orderBy( 'c.name' )
            ->fetchArray();

        $this->view->category   = $category;
        $this->view->categories = INEX_Mrtg::$CATEGORIES;
        $this->view->period     = $period;
        $this->view->periods    = INEX_Mrtg::$PERIODS;
        $this->view->display( 'customer' . DIRECTORY_SEPARATOR . 'statistics-overview.tpl' );
    }

    public function statisticsByLanAction()
    {
        $category = $this->_request->getParam( 'category', INEX_Mrtg::$CATEGORIES['Bits'] );

        if( !in_array( $category, INEX_Mrtg::$CATEGORIES ) )
            $category = INEX_Mrtg::$CATEGORIES['Bits'];

        $period = $this->_request->getParam( 'period', INEX_Mrtg::$PERIODS['Day'] );

        if( !in_array( $period, INEX_Mrtg::$PERIODS ) )
            $period = INEX_Mrtg::$PERIODS['Day'];

        $lanTag = $this->_request->getParam( 'lan', 10 );

        $lan = Doctrine_Core::getTable( 'Vlan' )->findOneByNumber( $lanTag );

        if( !$lan )
            $lan = Doctrine_Core::getTable( 'Vlan' )->findOneByNumber( 10 );

        $this->view->lan = $lan;

        $this->view->ints = Doctrine_Query::create()
            ->from( 'Vlaninterface vl' )
            ->leftJoin( 'vl.Virtualinterface vi' )
            ->leftJoin( 'vi.Cust c' )
            ->leftJoin( 'vi.Physicalinterface pi' )
            ->leftJoin( 'pi.Switchport sp' )
            ->leftJoin( 'sp.SwitchTable s' )
            ->whereIn( 'c.type', array( Cust::TYPE_FULL, Cust::TYPE_INTERNAL, Cust::TYPE_PROBONO ) )
            ->andWhere( 'c.status = ?', array( Cust::STATUS_NORMAL ) )
            ->andWhere( 'c.dateleave = 0 or c.dateleave IS NULL' )
            ->andWhere( 'vl.vlanid = ?', $lan['id'] )
            ->andWhereIn( 'c.shortname', array( 'inex', 'routeservers' ), true )
            ->orderBy( 'c.name' )
            ->fetchArray();

        $this->view->category   = $category;
        $this->view->categories = INEX_Mrtg::$CATEGORIES;
        $this->view->period     = $period;
        $this->view->periods    = INEX_Mrtg::$PERIODS;
        $this->view->display( 'customer' . DIRECTORY_SEPARATOR . 'statistics-by-lan.tpl' );
    }

    public function statisticsListAction()
    {
        $this->view->custs = Doctrine_Query::create()
            ->select( 'c.shortname' )
            ->addSelect( 'c.name' )
            ->from( 'Cust c' )
            ->whereIn( 'c.type', array( Cust::TYPE_FULL, Cust::TYPE_INTERNAL, Cust::TYPE_PROBONO ) )
            ->andWhere( 'c.status = ?', array( Cust::STATUS_NORMAL ) )
            ->andWhere( 'c.dateleave = 0 or c.dateleave IS NULL' )
            ->andWhereIn( 'c.shortname', array( 'inex', 'routeservers' ), true )
            ->orderBy( 'c.name' )
            ->fetchArray();

        $this->view->display( 'customer' . DIRECTORY_SEPARATOR . 'statistics-list.tpl' );
    }
    
    
    
    public function quickAddAction()
    {
        
        $f = new INEX_Form_Customer_QuickAdd( null, false, $cancelLocation );

        // Process a submitted form if it passes initial validation
        if( $this->inexGetPost( 'commit' ) !== null && $f->isValid( $_POST ) )
        {
            do
            {
                // check customer information
                if( Doctrine_Core::getTable( 'Cust' )->findOneByShortname( $f->getValue( 'shortname' ) ) 
                    || Doctrine_Core::getTable( 'User' )->findOneByUsername( $f->getValue( 'shortname' ) )
                )
                {
                    $f->getElement( 'shortname' )->addError( 'The provided shortname / username already exists' );
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
                    // create the customer
                    $c = new Cust();
                    $c['name']                = $f->getValue( 'name' );
                    $c['shortname']           = $f->getValue( 'shortname' );
                    $c['type']                = Cust::TYPE_FULL;
                    $c['autsys']              = $f->getValue( 'autsys' );
                    $c['maxprefixes']         = $f->getValue( 'maxprefixes' );
                    $c['peeringemail']        = $f->getValue( 'peeringemail' );
                    $c['corpwww']             = $f->getValue( 'corpwww' );
                    $c['nocphone']            = $f->getValue( 'nocphone' );
                    $c['noc24hphone']         = $f->getValue( 'noc24hphone' );
                    $c['nocfax']              = $f->getValue( 'nocfax' );
                    $c['nocemail']            = $f->getValue( 'nocemail' );
                    $c['nochours']            = $f->getValue( 'nochours' );
                    $c['nocwww']              = $f->getValue( 'nocwww' );
                    $c['irrdb']               = $f->getValue( 'irrdb' );
                    $c['peeringpolicy']       = $f->getValue( 'open' );
                    $c['datejoin']            = date( 'Y-m-d' );
                    $c['status']              = Cust::STATUS_NORMAL;
                    $c['activepeeringmatrix'] = 1;
                    $c['creator']             = $this->user['username'];
                    $c->save();
                    
                    // create a user
                    $u = new User();
                    $u['username'] = $c['shortname'];
                    $u['password'] = UserTable::createRandomPassword( 8 );
                    $u['email']    = $c['nocemail'];
                    $u['custid']   = $c['id'];
                    $u['privs']    = User::AUTH_CUSTADMIN;
                    $u['creator']  = $this->user['username'];
                    $u->save();
                    
                    // virtual interface
                    $vi = new Virtualinterface();
                    $vi['custid'] = $c['id'];
                    $vi['name']   = $c['shortname'];
                    $vi->save();
                    
                    // load or create the dummy switch
                    $s = Doctrine_Core::getTable( 'SwitchTable' )->findOneByName( 'QuickAdd-Dummy-Switch' );
                    
                    if( !$s )
                    {
                        // get the cabinet
                        $cab = Doctrine_Core::getTable( 'Cabinet' )->findOneByName( 'QuickAdd-Dummy-Cabinet' );
                        
                        if( !$cab )
                        {
                            // get the location
                            $loc = Doctrine_Core::getTable( 'Location' )->findOneByName( 'QuickAdd-Dummy-Location' );
                            
                            if( !$loc )
                            {
                                $loc = new Location();
                                $loc['name']      = 'QuickAdd-Dummy-Location';
                                $loc['shortname'] = 'qa-dummy-loc';
                                $loc->save();
                            }
                            
                            $cab = new Cabinet();
                            $cab['locationid'] = $loc['id'];
                            $cab['name'] = 'QuickAdd-Dummy-Cabinet';
                            $cab->save();
                        }
                        
                        // get the vendor 
                        $vendor = Doctrine_Core::getTable( 'Vendor' )->findOneByName( 'QuickAdd-Dummy-Vendor' );
                            
                        if( !$vendor )
                        {
                            $vendor = new Vendor();
                            $vendor['name'] = 'QuickAdd-Dummy-Vendor';
                            $vendor->save();
                        }
                            
                        $s = new SwitchTable();
                        $s['name']       = 'QuickAdd-Dummy-Switch';
                        $s['cabinetid']  = $cab['id'];
                        $s['switchtype'] = SwitchTable::SWITCHTYPE_SWITCH;
                        $s['model']      = 'DUMMY';
                        $s['vendorid']   = $vendor['id'];
                        $s->save();
                    }
                    
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
                
                
                
            }while( false );
        }

        $this->view->form   = $f->render( $this->view );

        $this->view->display( 'customer' . DIRECTORY_SEPARATOR . 'quick-add.tpl' );
    }


}

?>