<?php
namespace OCA\QLCB\Service;

use OCP\IDBConnection;

class RelationService {
    private $db;

    public function __construct(IDBConnection $db) {
        $this->db = $db;
    }

    public function createRelation($qlcb_uid, $full_name, $date_of_birth, $phone, $address, $relationship) {
        $query = $this->db->getQueryBuilder();
        $query->insert('qlcb_relation')
            ->values([
                'qlcb_uid' => $query->createNamedParameter($qlcb_uid),
                'full_name' => $query->createNamedParameter($full_name),
                'date_of_birth' => $query->createNamedParameter($date_of_birth),
                'phone' => $query->createNamedParameter($phone),
                'address' => $query->createNamedParameter($address),
                'relationship' => $query->createNamedParameter($relationship),
            ])
            ->execute();
    }

    public function getAllRelations($qlcb_uid) {
        $query = $this->db->getQueryBuilder();
        $query->select('*')
            ->from('qlcb_relation')
            ->where($query->expr()->eq('qlcb_uid', $query->createNamedParameter($qlcb_uid)));

        $result = $query->execute();
        return $result->fetchAll();
    }

    public function getTypes($qlcb_uid) {
        $query = $this->db->getQueryBuilder();
        $query->select('relationship')
            ->from('qlcb_relation')
            ->where($query->expr()->eq('qlcb_uid', $query->createNamedParameter($qlcb_uid)))
            ->groupBy('relationship');

        $result = $query->execute();
        return $result->fetchAll();
    }

    public function updateRelation($qlcb_uid, $full_name, $date_of_birth, $phone, $address, $relationship, $relation_id) {
        $query = $this->db->prepare('UPDATE `oc_qlcb_relation` SET 
            `full_name` = COALESCE(?, `full_name`), 
            `date_of_birth` = COALESCE(?, `date_of_birth`),
            `phone` = COALESCE(?, `phone`),
            `address` = COALESCE(?, `address`),
            `relationship` = COALESCE(?, `relationship`),
            `qlcb_uid` = COALESCE(?, `qlcb_uid`)
            WHERE `relation_id` = ?');
        $query->execute([
            $full_name,
            $date_of_birth,
            $phone,
            $address,
            $relationship,
            $qlcb_uid,
            $relation_id,
        ]);
    }

    public function deleteRelation($relation_id) {
        $query = $this->db->getQueryBuilder();
        $query->delete('qlcb_relation')
            ->where($query->expr()->eq('relation_id', $query->createNamedParameter($relation_id)))
            ->execute();
    }
}