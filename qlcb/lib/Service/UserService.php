<?php
namespace OCA\QLCB\Service;

use OCP\IDBConnection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
class UserService {
    private $db;

    public function __construct(IDBConnection $db) {
        $this->db = $db;
    }

    public function createUser($qlcb_uid, $full_name, $date_of_birth, $gender, $phone, $address, $id_number, $email, $position_id, $coefficients_salary, $tax, $communist_party_joined, $unit_id, $day_joined) {
        $query = $this->db->getQueryBuilder();
        try {
            $query->insert('qlcb_user')
                ->values([
                    'qlcb_uid' => $query->createNamedParameter($qlcb_uid),
                    'full_name' => $query->createNamedParameter($full_name),
                    'date_of_birth' => $query->createNamedParameter($date_of_birth),
                    'gender' => $query->createNamedParameter($gender),
                    'phone' => $query->createNamedParameter($phone),
                    'address' => $query->createNamedParameter($address),
                    'id_number' => $query->createNamedParameter($id_number),
                    'email' => $query->createNamedParameter($email),
                    'position_id' => $query->createNamedParameter($position_id),
                    'coefficients_salary' => $query->createNamedParameter($coefficients_salary),
                    'tax' => $query->createNamedParameter($tax),
                    'communist_party_joined' => $query->createNamedParameter($communist_party_joined),
                    'unit_id' => $query->createNamedParameter($unit_id),
                    'day_joined' => $query->createNamedParameter($day_joined),
                ])
                ->execute();
        } catch (UniqueConstraintViolationException $e) {
            if (strpos($e->getMessage(), 'unique_email') !== false) {
                throw new \Exception('Email đã tồn tại.');
            } elseif (strpos($e->getMessage(), 'unique_phone') !== false) {
                throw new \Exception('Số điện thoại đã tồn tại.');
            } elseif (strpos($e->getMessage(), 'unique_id_number') !== false) {
                throw new \Exception('CCCD/CMND đã tồn tại.');
            } else {
                throw new \Exception('Lỗi không xác định.');
            }
        }
    }

    public function getUser($qlcb_uid) {
        $query = $this->db->getQueryBuilder();
        $query->select('*')
            ->from('qlcb_user')
            ->where($query->expr()->eq('qlcb_uid', $query->createNamedParameter($qlcb_uid)));

        $result = $query->execute();
        return $result->fetch();
    }

    public function getNCUsers() {
        $query = $this->db->getQueryBuilder();
        $query->select('qlcb_uid')->from('qlcb_user');
        $result = $query->execute();
        $rows = $result->fetchAll();
        $qlcbUids = array_column($rows, 'qlcb_uid');
    
        $query = $this->db->getQueryBuilder();
        $query->select('uid', 'displayname')->from('users');
        $result = $query->execute();
        $allUsers = $result->fetchAll();
    
        $filteredUsers = array_filter($allUsers, function ($user) use ($qlcbUids) {
            return !in_array($user['uid'], $qlcbUids);
        });
    
        return array_map(function ($user) {
            return [
                'user_id' => $user['uid'],
                'display_name' => $user['displayname'],
            ];
        }, $filteredUsers);
    }

    public function getAllUsers() {
        $query = $this->db->getQueryBuilder();
        $query->select('u.*', 'unit.unit_name', 'position.position_name')
            ->from('qlcb_user', 'u')
            ->leftJoin('u', 'qlcb_unit', 'unit', $query->expr()->eq('u.unit_id', 'unit.unit_id'))
            ->leftJoin('u', 'qlcb_position', 'position', $query->expr()->eq('u.position_id', 'position.position_id'));

        $result = $query->execute();
        return $result->fetchAll();
    }

    public function updateUser($qlcb_uid, $full_name, $date_of_birth, $gender, $phone, $address, $id_number, $email, $position_id, $coefficients_salary, $tax, $communist_party_joined, $unit_id, $day_joined) {
        $query = $this->db->prepare('UPDATE `oc_qlcb_user` SET 
            `full_name` = COALESCE(?, `full_name`), 
            `date_of_birth` = COALESCE(?, `date_of_birth`),
            `gender` = COALESCE(?, `gender`),
            `phone` = COALESCE(?, `phone`),
            `address` = COALESCE(?, `address`),
            `id_number` = COALESCE(?, `id_number`),
            `email` = COALESCE(?, `email`),
            `position_id` = COALESCE(?, `position_id`),
            `coefficients_salary` = COALESCE(?, `coefficients_salary`),
            `tax` = COALESCE(?, `tax`),
            `communist_party_joined` = COALESCE(?, `communist_party_joined`),
            `unit_id` = COALESCE(?, `unit_id`),
            `day_joined` = COALESCE(?, `day_joined`)
            WHERE `qlcb_uid` = ?');
        try {
            $query->execute([
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
                $day_joined,
                $qlcb_uid,
            ]);
        } catch (UniqueConstraintViolationException $e) {
            if (strpos($e->getMessage(), 'unique_email') !== false) {
                throw new \Exception('Email đã tồn tại.');
            } elseif (strpos($e->getMessage(), 'unique_phone') !== false) {
                throw new \Exception('Số điện thoại đã tồn tại.');
            } elseif (strpos($e->getMessage(), 'unique_id_number') !== false) {
                throw new \Exception('CCCD/CMND đã tồn tại.');
            } else {
                throw new \Exception('Lỗi không xác định.');
            }
        }
    }

    public function deleteUser($qlcb_uid) {
        $query = $this->db->getQueryBuilder();
        $query->delete('qlcb_user')
            ->where($query->expr()->eq('qlcb_uid', $query->createNamedParameter($qlcb_uid)))
            ->execute();
    }
}