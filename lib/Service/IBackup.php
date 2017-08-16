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

use OCP\IUser;

interface IBackup{

    /**
     * @param IUser $user
     * @return array of newly generated backup codes
     */
    public function generateBackupCodes(IUser $user);

    /**
     * @param IUser $user
     */
    public function deleteBackupCodesByUser(IUser $user);

    /**
     * @param IUser $user
     * @param string $code
     */
    public function deleteBackupCode(IUser $user, $code);

    /**
     * @param IUser $user
     * @return bool
     */
    public function hasBackupCode(IUser $user);

    /**
     * @param IUser $user
     * @return integer
     */
    public function getRemainingCodesCount(IUser $user);

    /**
     * @param IUser $user
     * @param string $code
     */
    public function validateBackupCode(IUser $user, $code);

}