<?php
namespace OCA\QLCB\Service;

use OCP\IDBConnection;

class AnalystService {
    private $db;

    public function __construct(IDBConnection $db) {
        $this->db = $db;
    }

    public function countUsersByTime($startDate, $endDate, $interval) {
        $query = $this->db->getQueryBuilder();

        switch ($interval) {
            case 'week':
                $periodFormat = "CONCAT(YEAR(day_joined), '-', WEEK(day_joined))";
                break;
            case 'month':
                $periodFormat = "DATE_FORMAT(day_joined, '%Y-%m')";
                break;
            case 'quarter':
                $periodFormat = "CONCAT(YEAR(day_joined), '-Q', QUARTER(day_joined))";
                break;
            case 'year':
                $periodFormat = "DATE_FORMAT(day_joined, '%Y')";
                break;
            default:
                throw new \InvalidArgumentException("Invalid interval: $interval");
        }

        $query->selectAlias($query->createFunction("COUNT(*)"), 'user_count')
           ->addSelect($query->createFunction($periodFormat . ' AS period'))
           ->from('qlcb_user');

        if ($startDate !== null) {
            $query->andWhere($query->expr()->gte('day_joined', $query->createNamedParameter($startDate)));
        }

        if ($endDate !== null) {
            $query->andWhere($query->expr()->lte('day_joined', $query->createNamedParameter($endDate)));
        }

        $query->groupBy('period')
           ->orderBy('period', 'ASC');

        return $query->execute()->fetchAll();
    }

    public function countUsersByUnit($startDate, $endDate) {
        $query = $this->db->getQueryBuilder();
    
        $query->select('u.unit_id', 'un.unit_name', $query->func()->count('*', 'user_count'))
           ->from('qlcb_user', 'u')
           ->join('u', 'qlcb_unit', 'un', $query->expr()->eq('u.unit_id', 'un.unit_id'));
    
        if ($startDate !== null) {
            $query->andWhere($query->expr()->gte('u.day_joined', $query->createNamedParameter($startDate)));
        }
    
        if ($endDate !== null) {
            $query->andWhere($query->expr()->lte('u.day_joined', $query->createNamedParameter($endDate)));
        }
    
        $query->groupBy('u.unit_id', 'un.unit_name')
           ->orderBy('u.unit_id', 'ASC');
    
        return $query->execute()->fetchAll();
    }
    
    public function countUsersByGender($startDate, $endDate) {
        $query = $this->db->getQueryBuilder();
        
        $query->select('gender', $query->func()->count('*', 'user_count'))
            ->from('qlcb_user');
        
        if ($startDate !== null) {
            $query->andWhere($query->expr()->gte('day_joined', $query->createNamedParameter($startDate)));
        }
    
        if ($endDate !== null) {
            $query->andWhere($query->expr()->lte('day_joined', $query->createNamedParameter($endDate)));
        }
    
        $query->groupBy('gender');
    
        return $query->execute()->fetchAll();
    }
    
    public function countUsersByAge($startDate, $endDate) {
        $currentDate = new \DateTime();
        $formattedCurrentDate = $currentDate->format('Y-m-d'); // Tính toán giá trị trước
        $query = $this->db->getQueryBuilder();
        
        $ageGroupSql = "
            CASE
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, '$formattedCurrentDate') BETWEEN 18 AND 30 THEN '18-30'
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, '$formattedCurrentDate') BETWEEN 31 AND 45 THEN '31-45'
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, '$formattedCurrentDate') BETWEEN 46 AND 60 THEN '46-60'
                ELSE 'Trên 60'
            END AS age_group";
    
        $query->select($query->createFunction($ageGroupSql), $query->func()->count('*', 'user_count'))
            ->from('qlcb_user');
    
        if ($startDate !== null) {
            $query->andWhere($query->expr()->gte('day_joined', $query->createNamedParameter($startDate)));
        }
    
        if ($endDate !== null) {
            $query->andWhere($query->expr()->lte('day_joined', $query->createNamedParameter($endDate)));
        }
    
        $query->groupBy('age_group');
    
        return $query->execute()->fetchAll();
    }

    public function countEducationPerUnit($startDate, $endDate) {
        $query = $this->db->getQueryBuilder();
    
        $query->select('u.unit_id', 'un.unit_name', $query->func()->count('b.qlcb_uid', 'count'))
            ->from('qlcb_education', 'b')
            ->leftJoin('b', 'qlcb_user', 'u', $query->expr()->eq('b.qlcb_uid', 'u.qlcb_uid'))
            ->leftJoin('u', 'qlcb_unit', 'un', $query->expr()->eq('u.unit_id', 'un.unit_id'));
    
        if ($startDate !== null) {
            $query->andWhere($query->expr()->gte('b.start_date', $query->createNamedParameter($startDate)));
        }
    
        if ($endDate !== null) {
            $query->andWhere($query->expr()->lte('b.end_date', $query->createNamedParameter($endDate)));
        }
    
        $query->groupBy('u.unit_id', 'un.unit_name')
            ->orderBy('un.unit_name', 'ASC');
    
        return $query->execute()->fetchAll();
    }
}