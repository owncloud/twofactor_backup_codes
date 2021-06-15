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

namespace OCA\TwoFactorBackupCodes\Provider;

use OC\App\AppManager;
use OCA\TwoFactorBackupCodes\Service\Backup;
use OCP\Authentication\TwoFactorAuth\IProvider;
use OCP\IL10N;
use OCP\IUser;
use OCP\Template;

class BackupProvider implements IProvider {

	/** @var string */
	private $appName;

	/** @var Backup */
	private $backup;

	/** @var IL10N */
	private $l10n;

	/** @var AppManager */
	private $appManager;

	/**
	 * @param string $appName
	 * @param Backup $backup
	 * @param IL10N $l10n
	 * @param AppManager $appManager
	 */
	public function __construct($appName, Backup $backup, IL10N $l10n, AppManager $appManager) {
		$this->appName = $appName;
		$this->backup = $backup;
		$this->l10n = $l10n;
		$this->appManager = $appManager;
	}

	/**
	 * Get unique identifier of this 2FA provider
	 *
	 * @return string
	 */
	public function getId() {
		return 'backup_codes';
	}

	/**
	 * Get the display name for selecting the 2FA provider
	 *
	 * @return string
	 */
	public function getDisplayName() {
		return $this->l10n->t('Backup Codes');
	}

	/**
	 * Get the description for selecting the 2FA provider
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->l10n->t('Authenticate with a backup code');
	}

	/**
	 * Get the template for rending the 2FA provider view
	 *
	 * @param IUser $user
	 * @return Template
	 */
	public function getTemplate(IUser $user) {
		return new Template('twofactor_backup_codes', 'challenge');
	}

	/**
	 * Verify the given challenge
	 *
	 * @param IUser $user
	 * @param string $challenge
	 */
	public function verifyChallenge(IUser $user, $challenge) {
		return $this->backup->validateBackupCode($user, $challenge);
	}

	/**
	 * Decides whether 2FA is enabled for the given user
	 *
	 * @param IUser $user
	 * @return boolean
	 */
	public function isTwoFactorAuthEnabledForUser(IUser $user) {
		if ($this->backup->hasBackupCode($user) === true) {
			$appIds = array_filter($this->appManager->getEnabledAppsForUser($user), function ($appId) {
				return $appId !== 'twofactor_backup_codes';
			});
			foreach ($appIds as $appId) {
				$info = $this->appManager->getAppInfo($appId);
				if (isset($info['two-factor-providers']) && \count($info['two-factor-providers']) > 0) {
					return true;
				}
			}
		}
		return false;
	}
}
