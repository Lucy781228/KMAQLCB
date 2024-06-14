<?php
namespace OCA\QLCB\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCA\QLCB\Service\AnalystService;

class AnalystController extends Controller {
    private $analystService;

    public function __construct($AppName, IRequest $request, AnalystService $analystService) {
        parent::__construct($AppName, $request);
        $this->analystService = $analystService;
    }

    /**
     * @NoCSRFRequired
     */
    public function countUsersByTime($startDate, $endDate, $interval) {
        try {
            $data = $this->analystService->countUsersByTime($startDate, $endDate, $interval);
            return new DataResponse(['data' => $data]);
        } catch (\Exception $e) {
            return new DataResponse(['error' => $e->getMessage()], 500);
        }
    }

        /**
     * @NoCSRFRequired
     */
    public function countUsersByUnit($startDate, $endDate,) {
        try {
            $data = $this->analystService->countUsersByUnit($startDate, $endDate);
            return new DataResponse(['data' => $data]);
        } catch (\Exception $e) {
            return new DataResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @NoCSRFRequired
     */
    public function countUsersByGender($startDate, $endDate) {
        try {
            $data = $this->analystService->countUsersByGender($startDate, $endDate);
            return new DataResponse(['data' => $data]);
        } catch (\Exception $e) {
            return new DataResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @NoCSRFRequired
     */
    public function countUsersByAge($startDate, $endDate) {
        try {
            $data = $this->analystService->countUsersByAge($startDate, $endDate);
            return new DataResponse(['data' => $data]);
        } catch (\Exception $e) {
            return new DataResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @NoCSRFRequired
     */
    public function countEducationPerUnit($startDate, $endDate) {
        try {
            $data = $this->analystService->countEducationPerUnit($startDate, $endDate);
            return new DataResponse(['data' => $data]);
        } catch (\Exception $e) {
            return new DataResponse(['error' => $e->getMessage()], 500);
        }
    }
}