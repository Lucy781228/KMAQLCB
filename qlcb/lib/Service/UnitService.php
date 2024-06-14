<?php
namespace OCA\QLCB\Service;

use OCP\IDBConnection;

class UnitService {
    private $db;

    public function __construct(IDBConnection $db) {
        $this->db = $db;
    }

    public function getAllUnits() {
        $query = $this->db->getQueryBuilder();
        $query->select('*')
            ->from('qlcb_unit');

        $result = $query->execute();
        return $result->fetchAll();
    }

    public function createUnit($unit_id, $unit_name) {
        $query = $this->db->getQueryBuilder();
        $query->insert('qlcb_unit')
            ->values([
                'unit_id' => $query->createNamedParameter($unit_id),
                'unit_name' => $query->createNamedParameter($unit_name),
            ])
            ->execute();
    }

    public function deleteUnit($unit_id) {
        $query = $this->db->getQueryBuilder();
        $query->delete('qlcb_unit')
            ->where($query->expr()->eq('unit_id', $query->createNamedParameter($unit_id)))
            ->execute();
    }
}