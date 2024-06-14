<?php
namespace OCA\QLCB\Service;

use OCP\IDBConnection;

class EducationService {
    private $db;

    public function __construct(IDBConnection $db) {
        $this->db = $db;
    }

    public function getAllEducations() {
        $query = $this->db->getQueryBuilder();
        $query->select('e.*', 'd.*', 'u.full_name')
            ->from('qlcb_education', 'e')
            ->leftJoin('e', 'qlcb_diploma', 'd', 'e.diploma_id = d.diploma_id')
            ->leftJoin('e', 'qlcb_user', 'u', 'e.qlcb_uid = u.qlcb_uid');

        $result = $query->execute();
        return $result->fetchAll();
    }

    public function getEducations($qlcb_uid) {
        $query = $this->db->getQueryBuilder();
        $query->select('e.*', 'd.*')
            ->from('qlcb_education', 'e')
            ->leftJoin('e', 'qlcb_diploma', 'd', 'e.diploma_id = d.diploma_id')
            ->where($query->expr()->eq('e.qlcb_uid', $query->createNamedParameter($qlcb_uid)));

        $result = $query->execute();
        return $result->fetchAll();
    }

    public function createEducation($qlcb_uid, $diploma_id, $start_date, $end_date, $training_unit, $specialization, $result) {
        $query = $this->db->getQueryBuilder();
        $query->insert('qlcb_education')
            ->values([
                'qlcb_uid' => $query->createNamedParameter($qlcb_uid),
                'diploma_id' => $query->createNamedParameter($diploma_id),
                'start_date' => $query->createNamedParameter($start_date),
                'end_date' => $query->createNamedParameter($end_date),
                'training_unit' => $query->createNamedParameter($training_unit),
                'specialization' => $query->createNamedParameter($specialization),
                'result' => $query->createNamedParameter($result),
            ])
            ->execute();
    }

    public function updateEducation($qlcb_uid, $diploma_id, $start_date, $end_date, $training_unit, $specialization, $result, $education_id) {
        $query = $this->db->prepare('UPDATE `oc_qlcb_education` SET 
            `diploma_id` = COALESCE(?, `diploma_id`), 
            `start_date` = COALESCE(?, `start_date`),
            `end_date` = COALESCE(?, `end_date`),
            `training_unit` = COALESCE(?, `training_unit`),
            `specialization` = COALESCE(?, `specialization`),
            `result` = COALESCE(?, `result`),
            `qlcb_uid` = COALESCE(?, `qlcb_uid`)
            WHERE `education_id` = ?');
        $query->execute([
            $diploma_id,
            $start_date,
            $end_date,
            $training_unit,
            $specialization,
            $result,
            $qlcb_uid,
            $education_id,
        ]);
    }

    public function deleteEducation($education_id) {
        $query = $this->db->getQueryBuilder();
        $query->delete('qlcb_education')
            ->where($query->expr()->eq('education_id', $query->createNamedParameter($education_id)))
            ->execute();
    }
}