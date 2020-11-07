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

namespace OCA\TwoFactorBackupCodes\Controller;

use OCA\TwoFactorBackupCodes\Service\IBackup;
use OCP\AppFramework\Controller;
use OCP\IRequest;
use OCP\IUserSession;

class SettingsController extends Controller {

    /** @var IBackup */
    private $backup;

    /** @var IUserSession */
    private $userSession;

    /**
     * @param string $appName
     * @param IRequest $request
     * @param IUserSession $userSession
     * @param IBackup $backupCode
     */
    public function __construct($appName, IRequest $request, IUserSession $userSession, IBackup $backup) {
        parent::__construct($appName, $request);
        $this->userSession = $userSession;
        $this->backup = $backup;
    }

    /**
     * @NoAdminRequired
     * @return array
     */
    public function state() {
        $user = $this->userSession->getUser();
        return [
            'remaining' => $this->backup->getRemainingCodesCount($user),
        ];
    }

    /**
     * @NoAdminRequired
     * @return array
     */
    public function generateBackupCodes() {
        $user = $this->userSession->getUser();
        $this->backup->deleteBackupCodesByUser($user);
        $codes = $this->backup->generateBackupCodes($user);
        return [
            'remaining' => $this->backup->getRemainingCodesCount($user),
            'codes' => $codes,
        ];
    }

    /**
     * @NoAdminRequired
     * @return array
     */
    public function removeBackupCodes() {
        $user = $this->userSession->getUser();
        $this->backup->deleteBackupCodesByUser($user);
        return [
            'remaining' => 0
        ];
    }

}
