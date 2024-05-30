<?php
namespace OCA\KMAQLCB\Service;

use OCP\IDBConnection;

class PositionService {
    private $db;

    public function __construct(IDBConnection $db) {
        $this->db = $db;
    }

    public function getAllPositions() {
        $query = $this->db->getQueryBuilder();
        $query->select('*')
            ->from('kmaqlcb_position');

        $result = $query->execute();
        return $result->fetchAll();
    }

    public function createPosition($position_id, $position_name) {
        $query = $this->db->getQueryBuilder();
        $query->insert('kmaqlcb_position')
            ->values([
                'position_id' => $query->createNamedParameter($position_id),
                'position_name' => $query->createNamedParameter($position_name),
            ])
            ->execute();
    }

    public function deletePosition($position_id) {
        $query = $this->db->getQueryBuilder();
        $query->delete('kmaqlcb_position')
            ->where($query->expr()->eq('position_id', $query->createNamedParameter($position_id)))
            ->execute();
    }
}