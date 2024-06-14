<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Lucy <ct040407@actv.edu.vn>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\QLCB\Controller;

use OCP\IRequest;
use OCP\IDBConnection;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\OCS\OCSNotFoundException;

use OCA\QLCB\Service\BonusService;

class BonusController extends Controller
{
    private $bonusService;

    public function __construct(
        $AppName,
        IRequest $request,
        BonusService $bonusService,
    ) {
        parent::__construct($AppName, $request);
        $this->bonusService = $bonusService;
    }

    /**
     * @NoCSRFRequired
     */
    public function createBonus(
        $qlcb_uid,
        $type,
        $form,
        $time,
        $reason,
        $number_decision,
        $department_decision
    ) {
        $this->bonusService->createBonus($qlcb_uid, $type, $form, $time, $reason, $number_decision, $department_decision);
        return new JSONResponse(["status" => "success"]);
    }

    /**
     * @NoCSRFRequired
     */
    public function getAllBonuses($type)
    {
        $bonuses = $this->bonusService->getAllBonuses($type);
        return ["bonuses" => $bonuses];
    }

    /**
     * @NoCSRFRequired
     */
    public function getBonuses($qlcb_uid, $type)
    {
        $bonuses = $this->bonusService->getBonuses($qlcb_uid, $type);
        return ["bonuses" => $bonuses];
    }

    /**
     * @NoCSRFRequired
     */
    public function updateBonus(
        $qlcb_uid,
        $type,
        $form,
        $time,
        $reason,
        $number_decision,
        $department_decision,
        $bonus_id
    ) {
        $this->bonusService->updateBonus($qlcb_uid, $type, $form, $time, $reason, $number_decision, $department_decision, $bonus_id);
        return new JSONResponse(["status" => "success"]);
    }

    /**
     * @NoCSRFRequired
     */
    public function deleteBonus($bonus_id)
    {
        $this->bonusService->deleteBonus($bonus_id);
        return new JSONResponse(["status" => "success"]);
    }
}