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
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use OCA\QLCB\Service\UserService;

class UserController extends Controller
{
    private $userService;

    public function __construct(
        $AppName,
        IRequest $request,
        UserService $userService,
    ) {
        parent::__construct($AppName, $request);
        $this->userService = $userService;
    }

 /**
     * @NoCSRFRequired
     */
    public function createUser(
        $qlcb_uid,
        $full_name,
        $date_of_birth,
        $gender,
        $phone,
        $address,
        $id_number,
        $email,
        $position_id,
        $coefficients_salary,
        $tax,
        $communist_party_joined,
        $unit_id,
        $day_joined
    ) {
        try {
            $this->userService->createUser(
                $qlcb_uid,
                $full_name,
                $date_of_birth,
                $gender,
                $phone,
                $address,
                $id_number,
                $email,
                $position_id,
                $coefficients_salary,
                $tax,
                $communist_party_joined,
                $unit_id,
                $day_joined
            );
            return new JSONResponse(["status" => "success"]);
        } catch (\Exception $e) {
            if ($e instanceof UniqueConstraintViolationException) {
                if (strpos($e->getMessage(), 'unique_email') !== false) {
                    return new JSONResponse(["status" => "error", "message" => "Email đã tồn tại."], 400);
                } elseif (strpos($e->getMessage(), 'unique_phone') !== false) {
                    return new JSONResponse(["status" => "error", "message" => "Số điện thoại đã tồn tại."], 400);
                } elseif (strpos($e->getMessage(), 'unique_id_number') !== false) {
                    return new JSONResponse(["status" => "error", "message" => "CCCD/CMND đã tồn tại."], 400);
                } else {
                    return new JSONResponse(["status" => "error", "message" => "Lỗi không xác định."], 500);
                }
            } else {
                return new JSONResponse(["status" => "error", "message" => $e->getMessage()], 500);
            }
        }
    }

    /**
     * @NoCSRFRequired
     */
    public function getUser($qlcb_uid)
    {
        $data = $this->userService->getUser($qlcb_uid);
        return ["user" => $data];
    }

    /**
     * @NoCSRFRequired
     */
    public function getNCUsers()
    {
        $userList = $this->userService->getNCUsers();
        return ["nc_users" => $userList];
    }

    /**
     * @NoCSRFRequired
     */
    public function getAllUsers()
    {
        $data = $this->userService->getAllUsers();
        return ["users" => $data];
    }

    /**
     * @NoCSRFRequired
     */
    public function updateUser(
        $qlcb_uid,
        $full_name,
        $date_of_birth,
        $gender,
        $phone,
        $address,
        $id_number,
        $email,
        $position_id,
        $coefficients_salary,
        $tax,
        $communist_party_joined,
        $unit_id,
        $day_joined
    ) {
        try {
            $this->userService->updateUser(
                $qlcb_uid,
                $full_name,
                $date_of_birth,
                $gender,
                $phone,
                $address,
                $id_number,
                $email,
                $position_id,
                $coefficients_salary,
                $tax,
                $communist_party_joined,
                $unit_id,
                $day_joined
            );
            return new JSONResponse(["status" => "success"]);
        } catch (\Exception $e) {
            if ($e instanceof UniqueConstraintViolationException) {
                if (strpos($e->getMessage(), 'unique_email') !== false) {
                    return new JSONResponse(["status" => "error", "message" => "Email đã tồn tại."], 400);
                } elseif (strpos($e->getMessage(), 'unique_phone') !== false) {
                    return new JSONResponse(["status" => "error", "message" => "Số điện thoại đã tồn tại."], 400);
                } elseif (strpos($e->getMessage(), 'unique_id_number') !== false) {
                    return new JSONResponse(["status" => "error", "message" => "CCCD/CMND đã tồn tại."], 400);
                } else {
                    return new JSONResponse(["status" => "error", "message" => "Lỗi không xác định."], 500);
                }
            } else {
                return new JSONResponse(["status" => "error", "message" => $e->getMessage()], 500);
            }
        }
    }

    /**
     * @NoCSRFRequired
     */
    public function deleteUser($qlcb_uid)
    {
        $this->userService->deleteUser($qlcb_uid);
        return new JSONResponse(["status" => "success"]);
    }
}