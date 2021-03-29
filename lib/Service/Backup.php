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

namespace OCA\TwoFactorBackupCodes\Service;

use OCA\TwoFactorBackupCodes\Db\BackupCode;
use OCA\TwoFactorBackupCodes\Db\BackupCodeMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\IUser;
use OCP\Security\ISecureRandom;

class Backup implements IBackup {

    private static $CODE_LENGTH = 8;

    /** @var BackupCodeMapper */
    private $backupCodeMapper;

    /** @var ISecureRandom */
    private $random;

    public function __construct(BackupCodeMapper $backupCodeMapper,  ISecureRandom $random) {
        $this->backupCodeMapper = $backupCodeMapper;
        $this->random = $random;
    }

    public function generateBackupCodes(IUser $user, $number = 8) {
        $backupCodes = array();
        while (count($backupCodes) < $number) {
            $code = $this->random->generate(self::$CODE_LENGTH, ISecureRandom::CHAR_UPPER . ISecureRandom::CHAR_DIGITS);
            if (in_array($code, $backupCodes) === false) {
                $dbBackupCode = new BackupCode();
                $dbBackupCode->setUserId($user->getUID());
                $dbBackupCode->setCode($code);
                $this->backupCodeMapper->insert($dbBackupCode);
                array_push($backupCodes, $code);
            }
        }
        return $backupCodes;
    }

    /**
     * @param IUser $user
     * @param string $code
     */
    public function deleteBackupCode(IUser $user, $code) {
        try {
            $dbBackupCode = $this->backupCodeMapper->getBackupCode($user, $code);
            $this->backupCodeMapper->delete($dbBackupCode);
        } catch (DoesNotExistException $ex) {
			// It is OK to delete a backup code that does not exist
        }
    }

    /**
     * @param IUser $user
     * @param string $code
     */
    public function deleteBackupCodesByUser(IUser $user) {
        $this->backupCodeMapper->deleteBackupCodes($user);
    }

    /**
     * @param IUser $user
     * @return bool
     */
    public function hasBackupCode(IUser $user) {
        return count($this->backupCodeMapper->getBackupCodes($user)) > 0;
    }

    /**
     * @param IUser $user
     * @return integer
     */
    public function getRemainingCodesCount(IUser $user) {
        return count($this->backupCodeMapper->getBackupCodes($user));
    }

    /**
     * @param IUser $user
     * @param string $code
     */
    public function validateBackupCode(IUser $user, $code) {
        try {
            $dbBackupCode = $this->backupCodeMapper->getBackupCode($user, $code);
            $this->backupCodeMapper->delete($dbBackupCode);
            return true;
        } catch (DoesNotExistException $ex) {
            return false;
        }
    }

}