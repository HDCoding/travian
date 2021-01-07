<?php

namespace Travian\Automations;

use Exception;
use Travian\Libs\Database;

class Automation
{
    public $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function doProcess($name, $interval = 0)
    {
        $mutex = new Mutex($name);

        if ($mutex->controlLock($interval)) {

        }

        $processes = [
            $this->auctionAuto() => 300,
            $this->auctionComplete() => 1,
            $this->buildComplete() => 0,
            $this->celebrationComplete() => 5,
            $this->checkDB() => 0,
            $this->clearAndDeleting() => 0,
            $this->culturePoints() => 1800,
            $this->demolitionComplete() => 8,
            $this->inviteChecker() => 300,
            $this->loyaltyRegeneration() => 3600,
            $this->marketComplete() => 0,
            $this->masterBuilder() => 0,
            $this->medals() => 120,
            $this->natarsJobs() => 10,
            $this->reportBox() => 0,
            $this->researchComplete() => 5,
            $this->returnUnitsComplete() => 0,
            $this->sendAdventuresComplete() => 0,
            $this->sendSettlersComplete() => 0,
            $this->sendReinfunitsComplete() => 0,
            $this->sendUnitsComplete() => 0,
            $this->tradeRoute() => 500,
            $this->trainingComplete() => 0,
            $this->updateHero() => 3,
            $this->zeroPopedVillages() => 0,
        ];

    }

    private function saveLog($log = '')
    {
        file_put_contents(LOG_PATH . "automation.log", "[" . date("Y-M-D H:i:s") . "] " . $log . PHP_EOL, FILE_APPEND);
    }

    public function eEE(Exception $exception)
    {
        return 'Message(' . $exception->getMessage() . ') File(' . $exception->getFile() . ') Line(' . $exception->getLine() . ')';
    }


    public function auctionComplete()
    {
        $time = time();
        $q = "SELECT `owner`,`uid`,`silver`,`btype`,`type`,`maxsilver`,`silver`,`num`,`id` FROM auction where finish = 0 and time <= $time LIMIT 100";
        $dataarray = $database->query_return($q);
        foreach ($dataarray as $data) {
            $ownerID = $data['owner'];
            $biderID = $data['uid'];
            $silver = $data['silver'];
            $btype = $data['btype'];
            $type = $data['type'];
            $silverdiff = $data['maxsilver'] - $data['silver'];
            if ($silverdiff < 0) $silverdiff = 0;
            if ($biderID != 0) {
                $id = $database->checkHeroItem($biderID, $btype, $type);
                if ($id) {
                    $database->modifyHeroItem($id, 'num', $data['num'], 1);
                    $database->modifyHeroItem($id, 'proc', 0, 0);
                } else {
                    $database->addHeroItem($biderID, $data['btype'], $data['type'], $data['num']);
                }
                $database->setSilver($biderID, $silverdiff, 1);
                $q = 'UPDATE users SET bidsilver=bidsilver-' . $silverdiff . ' WHERE id=' . $biderID;
                mysql_query($q);
            }
            $database->setSilver($ownerID, $silver, 1);
            $q = 'UPDATE users SET ausilver=ausilver+' . $silver . ' WHERE id=' . $ownerID;
            mysql_query($q);
            $q = "UPDATE auction set finish=1 where id = " . $data['id'];
            $database->query($q);
    }

    public function auctionAuto()
    {
    }

    public function buildComplete()
    {
    }

    public function celebrationComplete()
    {
    }

    public function checkDB()
    {
    }

    public function clearAndDeleting()
    {
    }

    public function culturePoints()
    {
    }

    public function demolitionComplete()
    {
    }

    public function invitechecker()
    {
    }

    public function loyaltyRegeneration()
    {
    }

    public function marketComplete()
    {
    }

    public function masterBuilder()
    {
    }

    public function medals()
    {
    }

    public function natarsJobs()
    {
    }

    public function reportbox()
    {
    }

    public function researchComplete()
    {
    }

    public function returnunitsComplete()
    {
    }

    public function sendAdventuresComplete()
    {
    }

    public function sendSettlersComplete()
    {
    }

    public function sendreinfunitsComplete()
    {
    }

    public function sendUnitsComplete()
    {
    }

    public function tradeRoute()
    {
    }

    public function trainingComplete()
    {
    }

    public function updateHero()
    {
    }

    public function zeroPopedVillages()
    {
    }
}