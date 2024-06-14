<?php
namespace OCA\QLCB\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCA\QLCB\Service\PositionService;

class PositionController extends Controller {
    private $positionService;

    public function __construct($AppName, IRequest $request, PositionService $positionService) {
        parent::__construct($AppName, $request);
        $this->positionService = $positionService;
    }

    /**
     * @NoCSRFRequired
     */
    public function getAllPositions() {
        $positions = $this->positionService->getAllPositions();
        return ['positions' => $positions];
    }

    /**
     * @NoCSRFRequired
     */
    public function createPosition($position_id, $position_name) {
        $this->positionService->createPosition($position_id, $position_name);
        return new DataResponse(['status' => 'success']);
    }

    /**
     * @NoCSRFRequired
     */
    public function deletePosition($position_id) {
        $this->positionService->deletePosition($position_id);
        return new DataResponse(['status' => 'success']);
    }
}