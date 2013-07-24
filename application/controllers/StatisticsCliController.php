<?php

/*
 * Copyright (C) 2009-2013 Internet Neutral Exchange Association Limited.
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
 * Controller: Statistics CLI Actions
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2013, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StatisticsCliController extends IXP_Controller_CliAction
{

    /**
     * This function looks for members who are reaching or exceeding 80% port utilisation
     */
    public function emailPortUtilisationAction()
    {
        $custs = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->getCurrentActive( false, true, false );
    
        $mail = $this->getMailer();
        $mail->setFrom( $this->_options['cli']['port_utilisation']['from_email'], $this->_options['cli']['traffic_differentials']['from_name'] )
            ->setSubject( $this->_options['cli']['port_utilisation']['subject'] )
            ->setType( Zend_Mime::MULTIPART_RELATED );
    
        foreach( $this->_options['cli']['port_utilisation']['recipients'] as $r )
            $mail->addTo( $r );
    
        $this->view->threshold = $this->_options['cli']['port_utilisation']['threshold'];
        $mailHtml = $this->view->render( 'statistics-cli/email/util-header.phtml' );
    
        $numIntsWithExcessUtil = 0;
    
        foreach( $custs as $c )
        {
            foreach( $c->getVirtualInterfaces() as $vi )
            {
                foreach( $vi->getPhysicalInterfaces() as $pi )
                {
                    if( $pi->getStatus() != \Entities\PhysicalInterface::STATUS_CONNECTED )
                        continue;
                    
                    $speed = $pi->getSpeed() * 1024 * 1024;
    
                    $mrtg = new IXP_Mrtg(
                            IXP_Mrtg::getMrtgFilePath( $this->_options['mrtg']['path'] . '/members',
                                    'LOG', $pi->getMonitorindex(), IXP_Mrtg::CATEGORY_BITS,
                                    $c->getShortname()
                            )
                    );
    
                    $stats = $mrtg->getValues( IXP_Mrtg::PERIOD_WEEK, IXP_Mrtg::CATEGORY_BITS, false );
    
                    $maxIn  = $stats['maxin'] * 8.0;
                    $maxOut = $stats['maxout'] * 8.0;
    
                    $switch_port = $pi->getSwitchport()->getSwitcher()->getName() . ' :: ' . $pi->getSwitchport()->getName();
    
                    $utilIn  = $maxIn  / $speed;
                    $utilOut = $maxOut / $speed;
    
                    if( $this->isVerbose() || $this->isDebug() )
                    {
                        echo $c->getName() . "\n";
                        printf( "\tIN %0.2f%%\tOUT: %0.2f%%\n", $utilIn * 100.0, $utilOut * 100.0 );
                    }
    
                    if( $utilIn > $this->_options['cli']['port_utilisation']['threshold'] || $utilOut > $this->_options['cli']['port_utilisation']['threshold'] )
                    {
                        $this->view->cust       = $c;
                        $this->view->utilIn     = $utilIn;
                        $this->view->utilOut    = $utilOut;
                        $this->view->switchport = $switch_port;
    
                        $mrtg = $mail->createAttachment(
                            file_get_contents(
                                IXP_Mrtg::getMrtgFilePath(
                                    $this->_options['mrtg']['path'] . '/members',
                                    'PNG',
                                    $pi->getMonitorindex(),
                                    IXP_Mrtg::CATEGORY_BITS,
                                    $c->getShortname(),
                                    IXP_Mrtg::PERIOD_WEEK
                                )
                            ),
                            "image/png",
                            Zend_Mime::DISPOSITION_INLINE,
                            Zend_Mime::ENCODING_BASE64,
                            "{$c->getShortname()}-{$pi->getMonitorindex()}.png"
                        );
    
                        $this->view->mrtg_id = $mrtg->id = "{$c->getShortname()}-{$pi->getMonitorindex()}";
    
                        $mailHtml .= $this->view->render( 'statistics-cli/email/util-member.phtml' );
    
                        $numIntsWithExcessUtil++;
                    }
                }
            }
        }
    
        $this->view->numWithExcessUtil = $numIntsWithExcessUtil;
    
        $mailHtml .= $this->view->render( 'statistics-cli/email/util-footer.phtml' );
    
        $mail->setBodyHtml( $mailHtml  );
        $mail->send();
    }
    
    
    /**
     * This function looks for members who have changed their traffic patterns significantly
     * when comparing 'yesterday' to the last month.
     */
    public function emailTrafficDeltasAction()
    {
        $custs = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->getCurrentActive( false, true, true );
    
        $mail = $this->getMailer();
        $mail->setFrom( $this->_options['cli']['traffic_differentials']['from_email'], $this->_options['cli']['traffic_differentials']['from_name'] )
            ->setSubject( $this->_options['cli']['traffic_differentials']['subject'] )
            ->setType( Zend_Mime::MULTIPART_RELATED );
    
        foreach( $this->_options['cli']['traffic_differentials']['recipients'] as $r )
            $mail->addTo( $r );
    
        $mailHtml = $this->view->render( 'statistics-cli/email/diff-header.phtml' );
    
        $numWithExceededThreshold = 0;
    
        foreach( $custs as $c )
        {
            $tds = $this->getD2EM()->getRepository( '\\Entities\\TrafficDaily' )
                ->getAsArray( $c, $this->_options['cli']['traffic_differentials']['stddev_calc_length'] + 1, IXP_Mrtg::CATEGORY_BITS );
    
            $firstDone = false;
            $meanIn  = 0.0; $stddevIn  = 0.0;
            $meanOut = 0.0; $stddevOut = 0.0;
            $count = 0.0;
    
            foreach( $tds as $t )
            {
                if( !$firstDone )
                {
                    $todayAvgIn  = $t['day_avg_in'];
                    $todayAvgOut = $t['day_avg_out'];
                    $firstDone = true;
                    continue;
                }
    
                $count     += 1.0;
                $meanIn    += $t['day_avg_in'];
                $meanOut   += $t['day_avg_out'];
            }
    
            if( $count > 1 )
            {
                $meanIn  /= $count;
                $meanOut /= $count;
    
                foreach( $tds as $t )
                {
                    $stddevIn  += ( $t['day_avg_in']  - $meanIn  ) * ( $t['day_avg_in']  - $meanIn  );
                    $stddevOut += ( $t['day_avg_out'] - $meanOut ) * ( $t['day_avg_out'] - $meanOut );
                }
    
                $stddevIn  = sqrt( $stddevIn  / ( $count - 1 ) );
                $stddevOut = sqrt( $stddevOut / ( $count - 1 ) );
            }
    
            // so, is yesterday's traffic outside of the standard deviation? And is it an increase or decrease?
            $sIn  = ( $todayAvgIn  - $meanIn   ) > 0 ? 'increase' : 'decrease';
            $sOut = ( $todayAvgOut - $meanOut  ) > 0 ? 'increase' : 'decrease';
            $dIn  = abs( $todayAvgIn  - $meanIn  );
            $dOut = abs( $todayAvgOut - $meanOut );
    
            $thresholdIn  = 1.5*$stddevIn;
            $thresholdOut = 1.5*$stddevOut;
    
            if( $this->isVerbose() || $this->isDebug() )
            {
                echo $c->getName() . "\n";
                printf( "\tIN  M: %d\tSD: %d\tDiff: %d\tT: %d\tR: %s\n",
                    intval( $meanIn ), intval( $stddevIn ), intval( $dIn ), $thresholdIn, ( $dIn > $thresholdIn ? 'OUT' : 'IN' )
                );
                printf( "\tOUT M: %d\tSD: %d\tDiff: %d\tT: %d\tR: %s\n\n",
                    intval( $meanOut ), intval( $stddevOut ), intval( $dOut ), $thresholdOut, ( $dOut > $thresholdOut ? 'OUT' : 'IN' )
                );
            }
    
            if( $dIn > $thresholdIn || $dOut > $thresholdOut )
            {
                $this->view->cust          = $c;
                $this->view->in            = $todayAvgIn;
                $this->view->out           = $todayAvgOut;
                $this->view->stddevIn      = $stddevIn;
                $this->view->stddevOut     = $stddevOut;
                $this->view->meanIn        = $meanIn;
                $this->view->meanOut       = $meanOut;
                $this->view->dIn           = $dIn;
                $this->view->dOut          = $dOut;
                $this->view->sIn           = $sIn;
                $this->view->sOut          = $sOut;
                $this->view->threasholdIn  = $thresholdIn;
                $this->view->threasholdOut = $thresholdOut;
                $this->view->percentIn     = $meanIn  ? intval( ( $dIn  / $meanIn  ) * 100 ) : 'NONE';
                $this->view->percentOut    = $meanOut ? intval( ( $dOut / $meanOut ) * 100 ) : 'NONE';
                $this->view->days          = $this->_options['cli']['traffic_differentials']['stddev_calc_length'];
    
                $mrtg = $mail->createAttachment(
                    @file_get_contents(
                        IXP_Mrtg::getMrtgFilePath(
                            $this->_options['mrtg']['path'] . '/members',
                            'PNG',
                            'aggregate',
                            'bits',
                            $c->getShortname(),
                            'month'
                        )
                    ),
                    "image/png",
                    Zend_Mime::DISPOSITION_INLINE,
                    Zend_Mime::ENCODING_BASE64,
                    $c->getShortname() . ".png"
                );
                $mrtg->id = $c->getShortname();
    
                $mailHtml .= $this->view->render( 'statistics-cli/email/diff-member.phtml' );
    
                $numWithExceededThreshold++;
            }
    
        }
    
        $this->view->numWithExceededThreshold = $numWithExceededThreshold;
    
        $mailHtml .= $this->view->render( 'statistics-cli/email/diff-footer.phtml' );
    
        $mail->setBodyHtml( $mailHtml  );
        $mail->send();
    }
    
    
    public function uploadTrafficStatsToDbAction()
    {
        // This should only be done once a day and if values already exist for 'today',
        // just delete them.
        $day = date( 'Y-m-d' );
        $this->getD2EM()->getRepository( '\\Entities\\TrafficDaily' )->deleteForDay( $day );
    
        $custs = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->getCurrentActive( false, true, true );
    
        foreach( $custs as $cust )
        {
            $stats = array();
    
            foreach( IXP_Mrtg::$CATEGORIES as $category )
            {
                $mrtg = new IXP_Mrtg(
                        IXP_Mrtg::getMrtgFilePath( $this->_options['mrtg']['path'] . '/members',
                                'LOG', 'aggregate', $category,
                                $cust->getShortname()
                        )
                );
    
                $td = new \Entities\TrafficDaily();
                $td->setDay( new DateTime( $day ) );
                $td->setCategory( $category );
                $td->setCustomer( $cust );
    
                foreach( IXP_Mrtg::$PERIODS as $name => $period )
                {
                    $stats = $mrtg->getValues( $period, $category, false );
    
                    $fn = "set{$name}AvgIn";  $td->$fn( $stats['averagein']  );
                    $fn = "set{$name}AvgOut"; $td->$fn( $stats['averageout'] );
                    $fn = "set{$name}MaxIn";  $td->$fn( $stats['maxin']      );
                    $fn = "set{$name}MaxOut"; $td->$fn( $stats['maxout']     );
                    $fn = "set{$name}TotIn";  $td->$fn( $stats['totalin']    );
                    $fn = "set{$name}TotOut"; $td->$fn( $stats['totalout']   );
                }
    
                $this->getD2EM()->persist( $td );
            }
            $this->getD2EM()->flush();
        }
    }
    
    
    
}

