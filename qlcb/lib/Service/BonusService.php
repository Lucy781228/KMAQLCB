<?php
namespace OCA\QLCB\Service;

use OCP\IDBConnection;

class BonusService {
    private $db;

    public function __construct(IDBConnection $db) {
        $this->db = $db;
    }

    public function getAllBonuses($type) {
        $query = $this->db->getQueryBuilder();
        $query->select('e.*', 'u.full_name')
            ->from('qlcb_bonus', 'e')
            ->leftJoin('e', 'qlcb_user', 'u', 'e.qlcb_uid = u.qlcb_uid')
            ->where($query->expr()->eq('e.type', $query->createNamedParameter($type)));

        $result = $query->execute();
        return $result->fetchAll();
    }

    public function getBonuses($qlcb_uid, $type) {
        $query = $this->db->getQueryBuilder();
        $query->select('*')
            ->from('qlcb_bonus')
            ->where($query->expr()->eq('qlcb_uid', $query->createNamedParameter($qlcb_uid)))
            ->andWhere($query->expr()->eq('type', $query->createNamedParameter($type)));

        $result = $query->execute();
        return $result->fetchAll();
    }

    public function createBonus($qlcb_uid, $type, $form, $time, $reason, $number_decision, $department_decision) {
        $query = $this->db->getQueryBuilder();
        $query->insert('qlcb_bonus')
            ->values([
                'qlcb_uid' => $query->createNamedParameter($qlcb_uid),
                'type' => $query->createNamedParameter($type),
                'form' => $query->createNamedParameter($form),
                'time' => $query->createNamedParameter($time),
                'reason' => $query->createNamedParameter($reason),
                'number_decision' => $query->createNamedParameter($number_decision),
                'department_decision' => $query->createNamedParameter($department_decision),
            ])
            ->execute();
    }

    public function updateBonus($qlcb_uid, $type, $form, $time, $reason, $number_decision, $department_decision, $bonus_id) {
        $query = $this->db->prepare('UPDATE `oc_qlcb_bonus` SET 
            `type` = COALESCE(?, `type`), 
            `form` = COALESCE(?, `form`),
            `time` = COALESCE(?, `time`),
            `reason` = COALESCE(?, `reason`),
            `qlcb_uid` = COALESCE(?, `qlcb_uid`),
            `number_decision` = COALESCE(?, `number_decision`),
            `department_decision` = COALESCE(?, `department_decision`)
            WHERE `bonus_id` = ?');
        $query->execute([
            $type,
            $form,
            $time,
            $reason,
            $qlcb_uid,
            $number_decision,
            $department_decision,
            $bonus_id,
        ]);
    }

    public function deleteBonus($bonus_id) {
        $query = $this->db->getQueryBuilder();
        $query->delete('qlcb_bonus')
            ->where($query->expr()->eq('bonus_id', $query->createNamedParameter($bonus_id)))
            ->execute();
    }
}