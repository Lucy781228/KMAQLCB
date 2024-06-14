<?php
namespace OCA\QLCB\Service;

use OCP\IDBConnection;

class DiplomaService {
    private $db;

    public function __construct(IDBConnection $db) {
        $this->db = $db;
    }

    public function getAllDiplomas() {
        $query = $this->db->getQueryBuilder();
        $query->select('*')
            ->from('qlcb_diploma');

        $result = $query->execute();
        return $result->fetchAll();
    }

    public function createDiploma($diploma_id, $diploma_name) {
        $query = $this->db->getQueryBuilder();
        $query->insert('qlcb_diploma')
            ->values([
                'diploma_id' => $query->createNamedParameter($diploma_id),
                'diploma_name' => $query->createNamedParameter($diploma_name),
            ])
            ->execute();
    }

    public function deleteDiploma($diploma_id) {
        $query = $this->db->getQueryBuilder();
        $query->delete('qlcb_diploma')
            ->where($query->expr()->eq('diploma_id', $query->createNamedParameter($diploma_id)))
            ->execute();
    }
}