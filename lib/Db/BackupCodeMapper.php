<?php

/**
 * @author Semih Serhat Karakaya <karakayasemi@itu.edu.tr>
 *
 * Two-factor Backup Codes
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\TwoFactorBackupCodes\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Mapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDb;
use OCP\IUser;

class BackupCodeMapper extends Mapper {

    public function __construct(IDb $db) {
        parent::__construct($db, 'twofactor_backup_codes');
    }

    /**
     * @param IUser $user
     * @param string $code
     * @throws DoesNotExistException
     * @return BackupCode
     */
    public function getBackupCode(IUser $user, $code) {
        /* @var $qb IQueryBuilder */
        $qb = $this->db->getQueryBuilder();

        $qb->select('id', 'user_id', 'code')
            ->from('twofactor_backup_codes')
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($user->getUID())))
            ->andWhere($qb->expr()->eq('code', $qb->createNamedParameter($code)));
        $result = $qb->execute();

        $row = $result->fetch();
        $result->closeCursor();
        if ($row === false) {
            throw new DoesNotExistException('Backup code does not exist');
        }
        return BackupCode::fromRow($row);
    }

    /**
     * @param IUser $user
     * @return BackupCode[]
     */
    public function getBackupCodes(IUser $user) {
        /* @var IQueryBuilder $qb */
        $qb = $this->db->getQueryBuilder();
        $qb->select('id', 'user_id', 'code')
            ->from('twofactor_backup_codes')
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($user->getUID())));
        $result = $qb->execute();
        $rows = $result->fetchAll();
        $result->closeCursor();
        return array_map(function ($row) {
            return BackupCode::fromRow($row);
        }, $rows);
    }

    /**
     * @param IUser $user
     * @throws DoesNotExistException
     */
    public function deleteBackupCodes(IUser $user) {
        /* @var $qb IQueryBuilder */
        $qb = $this->db->getQueryBuilder();

        $qb->delete('twofactor_backup_codes')
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($user->getUID())))
            ->execute();
    }

}
