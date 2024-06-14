<?php
namespace OCA\QLCB\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCA\QLCB\Service\DiplomaService;

class DiplomaController extends Controller {
    private $diplomaService;

    public function __construct($AppName, IRequest $request, DiplomaService $diplomaService) {
        parent::__construct($AppName, $request);
        $this->diplomaService = $diplomaService;
    }

    /**
     * @NoCSRFRequired
     */
    public function getAllDiplomas() {
        $diplomas = $this->diplomaService->getAllDiplomas();
        return new DataResponse(['diplomas' => $diplomas]);
    }

    /**
     * @NoCSRFRequired
     */
    public function createDiploma($diploma_id, $diploma_name) {
        $this->diplomaService->createDiploma($diploma_id, $diploma_name);
        return new DataResponse(['status' => 'success']);
    }

    /**
     * @NoCSRFRequired
     */
    public function deleteDiploma($diploma_id) {
        $this->diplomaService->deleteDiploma($diploma_id);
        return new DataResponse(['status' => 'success']);
    }
}