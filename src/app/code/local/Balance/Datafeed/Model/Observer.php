<?php

class Balance_Datafeed_Model_Observer {

    /**
     * Cronjob expression configuration
     */
    public function scheduledGenerateFeeds($schedule) {

        $errors = array();
        $report = "*** Data feed - Scheduled task report ***" . "\n\n";
        $debug = "*** Data feed - DEBUG ***" . "<br><br>";
        ;
        $collection = Mage::getModel('datafeed/datafeed')->getCollection();
        $cnt = 0;
        foreach ($collection as $datafeed) {

            try {

                $debug.= "Data feed : " . $datafeed->getFeedName() . ' (' . $datafeed->getFeedId() . ')' . "<br>";


                $cron['curent']['date'] = Mage::getSingleton('core/date')->date('l Y-m-d H:i:s');
                $cron['curent']['time'] = strtotime(Mage::getSingleton('core/date')->date('Y-m-d H:i:s'));
                $cron['offset']['time'] = strtotime(Mage::getSingleton('core/date')->date('Y-m-d H:i:s')) - strtotime(Mage::getSingleton('core/date')->gmtdate('Y-m-d H:i:s'));
                $cron['datafeed']['time'] = strtotime($datafeed->getFeedUpdatedAt()) + $cron['offset']['time'];
                $cron['datafeed']['date'] = date('l Y-m-d H:i:s', $cron['datafeed']['time']);




                $debug.='Execution : ' . ($cron['curent']['date']) . '<br>';
                $debug.='Last update : ' . ($cron['datafeed']['date']) . '<br>';

                $cronExpr = json_decode($datafeed->getCronExpr());
                $i = 0;
                $done = false;

                foreach ($cronExpr->days as $d) {

                    foreach ($cronExpr->hours as $h) {
                        $time = explode(':', $h);
                        if (date('l', $cron['curent']['time']) == $d) {
                            $cron['tasks'][$i]['time'] = strtotime(Mage::getSingleton('core/date')->date('Y-m-d')) + ($time[0] * 60 * 60) + ($time[1] * 60);
                            $cron['tasks'][$i]['date'] = date('l Y-m-d H:i:s', $cron['tasks'][$i]['time']);
                        } else {
                            $cron['tasks'][$i]['time'] = strtotime("last " . $d, $cron['curent']['time']) + ($time[0] * 60 * 60) + ($time[1] * 60);
                            $cron['tasks'][$i]['date'] = date('l Y-m-d H:i:s', $cron['tasks'][$i]['time']);
                        }
                        $debug.='Scheduled date ' . $i . ' : ' . ($cron['tasks'][$i]['date']);

                        if ($cron['tasks'][$i]['time'] >= $cron['datafeed']['time'] && $cron['tasks'][$i]['time'] <= $cron['curent']['time'] && $done != true) {
                            if ($datafeed->generateFile()) {
                                $done = true;
                                $cnt++;
                                $report.='--------------------------' . "\n";
                                $report.="Data feed : " . $datafeed->getFeedName() . ' (' . $datafeed->getFeedId() . ')' . "\n";
                                $report.="Scheduled : " . date('l \a\t H:i', $cron['tasks'][$i]['time']) . "\n";
                                $report.="Executed : " . date('l d M Y \a\t H:i', $cron['curent']['time']) . "\n";
                            }
                            $debug.=' <<<<<<<<<<<<<<<< RUN !<br>';
                        }
                        else
                            $debug.='<br>';
                        $i++;
                    }
                }
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
                $report.="Error : " . ($e->getMessage()) . "\n";
            }
            $debug.='--------------------------' . "<br>";
        }

        if (Mage::getStoreConfig("datafeedmanager/setting/enable_report")) {
            foreach (explode(',', Mage::getStoreConfig("datafeedmanager/setting/emails")) as $email) {
                try {
                    if (!$cnt) {
                        $report.='--------------------------' . "\n";
                        $report.="Executed : " . date('l d M Y \a\t H:i', $cron['curent']['time']) . "\n";
                        $report.="No data feed generated." . "\n\n\n";
                        ;
                    } else {
                        $report.='--------------------------' . "\n\n\n";
                    }

                    mail($email, Mage::getStoreConfig("datafeedmanager/setting/report_title"), $report);
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
        };
        if (isset($_GET['debug']))
            echo $debug;
        if (Mage::getStoreConfig("datafeedmanager/setting/report_debug"))
            echo nl2br($report);
    }

}
