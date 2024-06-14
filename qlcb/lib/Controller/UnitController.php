<?php
namespace OCA\QLCB\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCA\QLCB\Service\UnitService;

class UnitController extends Controller {
    private $unitService;

    public function __construct($AppName, IRequest $request, UnitService $unitService) {
        parent::__construct($AppName, $request);
        $this->unitService = $unitService;
    }

    /**
     * @NoCSRFRequired
     */
    public function getAllUnits() {
        $units = $this->unitService->getAllUnits();
        return ['units' => $units];
    }

    /**
     * @NoCSRFRequired
     */
    public function createUnit($unit_id, $unit_name) {
        $this->unitService->createUnit($unit_id, $unit_name);
        return new DataResponse(['status' => 'success']);
    }

    /**
     * @NoCSRFRequired
     */
    public function deleteUnit($unit_id) {
        $this->unitService->deleteUnit($unit_id);
        return new DataResponse(['status' => 'success']);
    }
}