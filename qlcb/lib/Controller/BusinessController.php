<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Lucy <ct040407@actv.edu.vn>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\QLCB\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\OCS\OCSNotFoundException;

use OCA\QLCB\Service\BusinessService;

class BusinessController extends Controller
{
    private $businessService;

    public function __construct(
        $AppName,
        IRequest $request,
        BusinessService $businessService,
    ) {
        parent::__construct($AppName, $request);
        $this->businessService = $businessService;
    }

    /**
     * @NoCSRFRequired
     */
    public function createBusiness(
        $qlcb_uid,
        $start_date,
        $end_date,
        $unit,
        $position
    ) {
        $this->businessService->createBusiness($qlcb_uid, $start_date, $end_date, $unit, $position);
        return new JSONResponse(["status" => "success"]);
    }

    /**
     * @NoCSRFRequired
     */
    public function getBusinesses($qlcb_uid)
    {
        $businesses = $this->businessService->getBusinesses($qlcb_uid);
        return ["businesses" => $businesses];
    }

    /**
     * @NoCSRFRequired
     */
    public function getAllBusinesses()
    {
        $businesses = $this->businessService->getAllBusinesses();
        return ["businesses" => $businesses];
    }

    /**
     * @NoCSRFRequired
     */
    public function updateBusiness(
        $qlcb_uid,
        $start_date,
        $end_date,
        $unit,
        $position,
        $business_id
    ) {
        $this->businessService->updateBusiness($qlcb_uid, $start_date, $end_date, $unit, $position, $business_id);
        return new JSONResponse(["status" => "success"]);
    }

    /**
     * @NoCSRFRequired
     */
    public function deleteBusiness($business_id)
    {
        $this->businessService->deleteBusiness($business_id);
        return new JSONResponse(["status" => "success"]);
    }
}