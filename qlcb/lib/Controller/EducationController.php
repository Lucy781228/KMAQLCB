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

use OCA\QLCB\Service\EducationService;

class EducationController extends Controller
{
    private $educationService;

    public function __construct(
        $AppName,
        IRequest $request,
        EducationService $educationService,
    ) {
        parent::__construct($AppName, $request);
        $this->educationService = $educationService;
    }

    /**
     * @NoCSRFRequired
     */
    public function createEducation(
        $qlcb_uid,
        $diploma_id,
        $start_date,
        $end_date,
        $training_unit,
        $specialization,
        $result
    ) {
        $this->educationService->createEducation($qlcb_uid, $diploma_id, $start_date, $end_date, $training_unit, $specialization, $result);
        return new JSONResponse(["status" => "success"]);
    }

    /**
     * @NoCSRFRequired
     */
    public function getEducations($qlcb_uid)
    {
        $educations = $this->educationService->getEducations($qlcb_uid);
        return ["educations" => $educations];
    }

    /**
     * @NoCSRFRequired
     */
    public function getAllEducations()
    {
        $educations = $this->educationService->getAllEducations();
        return ["educations" => $educations];
    }

    /**
     * @NoCSRFRequired
     */
    public function updateEducation(
        $qlcb_uid,
        $diploma_id,
        $start_date,
        $end_date,
        $training_unit,
        $specialization,
        $result,
        $education_id
    ) {
        $this->educationService->updateEducation($qlcb_uid, $diploma_id, $start_date, $end_date, $training_unit, $specialization, $result, $education_id);
        return new JSONResponse(["status" => "success"]);
    }

    /**
     * @NoCSRFRequired
     */
    public function deleteEducation($education_id)
    {
        $this->educationService->deleteEducation($education_id);
        return new JSONResponse(["status" => "success"]);
    }
}