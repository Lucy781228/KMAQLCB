<?php
namespace OCA\QLCB\Service;

use OCP\IDBConnection;

class BusinessService {
    private $db;

    public function __construct(IDBConnection $db) {
        $this->db = $db;
    }

    public function getAllBusinesses() {
        $query = $this->db->getQueryBuilder();
        $query->select('e.*', 'u.full_name')
            ->from('qlcb_business', 'e')
            ->leftJoin('e', 'qlcb_user', 'u', 'e.qlcb_uid = u.qlcb_uid');

        $result = $query->execute();
        return $result->fetchAll();
    }

    public function getBusinesses($qlcb_uid) {
        $query = $this->db->getQueryBuilder();
        $query->select('*')
            ->from('qlcb_business')
            ->where($query->expr()->eq('qlcb_uid', $query->createNamedParameter($qlcb_uid)));

        $result = $query->execute();
        return $result->fetchAll();
    }

    public function createBusiness($qlcb_uid, $start_date, $end_date, $unit, $position) {
        $query = $this->db->getQueryBuilder();
        $query->insert('qlcb_business')
            ->values([
                'qlcb_uid' => $query->createNamedParameter($qlcb_uid),
                'start_date' => $query->createNamedParameter($start_date),
                'end_date' => $query->createNamedParameter($end_date),
                'unit' => $query->createNamedParameter($unit),
                'position' => $query->createNamedParameter($position),
            ])
            ->execute();
    }

    public function updateBusiness($qlcb_uid, $start_date, $end_date, $unit, $position, $business_id) {
        $query = $this->db->prepare('UPDATE `oc_qlcb_business` SET 
            `start_date` = COALESCE(?, `start_date`), 
            `end_date` = COALESCE(?, `end_date`),
            `unit` = COALESCE(?, `unit`),
            `position` = COALESCE(?, `position`),
            `qlcb_uid` = COALESCE(?, `qlcb_uid`)
            WHERE `business_id` = ?');
        $query->execute([
            $start_date,
            $end_date,
            $unit,
            $position,
            $qlcb_uid,
            $business_id,
        ]);
    }

    public function deleteBusiness($business_id) {
        $query = $this->db->getQueryBuilder();
        $query->delete('qlcb_business')
            ->where($query->expr()->eq('business_id', $query->createNamedParameter($business_id)))
            ->execute();
    }
}