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

use OCA\QLCB\Service\RelationService;

class RelationController extends Controller
{
    private $relationService;

    public function __construct(
        $AppName,
        IRequest $request,
        RelationService $relationService,
    ) {
        parent::__construct($AppName, $request);
        $this->relationService = $relationService;
    }

    /**
     * @NoCSRFRequired
     */
    public function createRelation(
        $qlcb_uid,
        $full_name,
        $date_of_birth,
        $phone,
        $address,
        $relationship
    ) {
        $this->relationService->createRelation($qlcb_uid, $full_name, $date_of_birth, $phone, $address, $relationship);
        return new JSONResponse(["status" => "success"]);
    }

    /**
     * @NoCSRFRequired
     */
    public function getAllRelations($qlcb_uid)
    {
        $relations = $this->relationService->getAllRelations($qlcb_uid);
        return ["relations" => $relations];
    }

    /**
     * @NoCSRFRequired
     */
    public function getTypes($qlcb_uid)
    {
        $types = $this->relationService->getTypes($qlcb_uid);
        return ["types" => $types];
    }

    /**
     * @NoCSRFRequired
     */
    public function updateRelation(
        $qlcb_uid,
        $full_name,
        $date_of_birth,
        $phone,
        $address,
        $relationship,
        $relation_id
    ) {
        $this->relationService->updateRelation($qlcb_uid, $full_name, $date_of_birth, $phone, $address, $relationship, $relation_id);
        return new JSONResponse(["status" => "success"]);
    }

    /**
     * @NoCSRFRequired
     */
    public function deleteRelation($relation_id)
    {
        $this->relationService->deleteRelation($relation_id);
        return new JSONResponse(["status" => "success"]);
    }
}