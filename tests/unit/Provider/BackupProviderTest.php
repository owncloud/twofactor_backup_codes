<?php
/**
 * @author Semih Serhat Karakaya <karakayasemi@itu.edu.tr>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\TwoFactorBackupCodes\Tests\Provider;

use OC\App\AppManager;
use OCA\TwoFactorBackupCodes\Provider\BackupProvider;
use OCA\TwoFactorBackupCodes\Service\Backup;
use OCP\IL10N;
use OCP\IUser;
use OCP\Template;
use PHPUnit_Framework_MockObject_MockObject;
use Test\TestCase;

class BackupCodesProviderTest extends TestCase {
    /** @var string */
    private $appName;
    /** @var Backup|PHPUnit_Framework_MockObject_MockObject */
    private $backupService;
    /** @var IL10N|PHPUnit_Framework_MockObject_MockObject */
    private $l10n;
    /** @var AppManager|PHPUnit_Framework_MockObject_MockObject */
    private $appManager;
    /** @var BackupProvider */
    private $provider;
    protected function setUp(): void {
        parent::setUp();
        $this->appName = "twofactor_backup_codes";
        $this->backupService = $this->createMock(Backup::class);
        $this->l10n = $this->createMock(IL10N::class);
        $this->appManager = $this->createMock(AppManager::class);
        $this->provider = new BackupProvider($this->appName, $this->backupService, $this->l10n, $this->appManager);
    }

    public function testGetId() {
        $this->assertEquals('backup_codes', $this->provider->getId());
    }

    public function testGetDisplayName() {
        $this->l10n->expects($this->once())
            ->method('t')
            ->with('Backup Codes')
            ->will($this->returnValue('l10n backup code'));
        $this->assertSame('l10n backup code', $this->provider->getDisplayName());
    }

    public function testGetDescription() {
        $this->l10n->expects($this->once())
            ->method('t')
            ->with('Authenticate with a backup code')
            ->will($this->returnValue('l10n authenticate with a backup code'));
        $this->assertSame('l10n authenticate with a backup code', $this->provider->getDescription());
    }

    public function testGetTempalte() {
        $user = $this->getMockBuilder(IUser::class)->getMock();
        $expected = new Template('twofactor_backup_codes', 'challenge');
        $this->assertEquals($expected, $this->provider->getTemplate($user));
    }

    public function testVerfiyChallenge() {
        $user = $this->getMockBuilder(IUser::class)->getMock();
        $challenge = 'CHALLANGE';
        $this->backupService->expects($this->once())
            ->method('validateBackupCode')
            ->with($user, $challenge)
            ->will($this->returnValue(false));
        $this->assertFalse($this->provider->verifyChallenge($user, $challenge));
    }

    public function testIsTwoFactorEnabledForEnabled() {
        $user = $this->getMockBuilder(IUser::class)->getMock();
        $this->backupService->expects($this->once())
            ->method('hasBackupCode')
            ->with($user)
            ->will($this->returnValue(true));
        $this->appManager->expects($this->once())
            ->method('getEnabledAppsForUser')
            ->with($user)
            ->willReturn(['twofactor_backup_codes' , 'twofactor_totp']);
        $this->appManager->expects($this->once())
            ->method('getAppInfo')
            ->with('twofactor_totp')
            ->willReturn([
                'two-factor-providers' => [
                    'OCA\TwoFactor_Totp\Provider\TotpProvider'
                ],
            ]);
        $this->assertTrue($this->provider->isTwoFactorAuthEnabledForUser($user));
    }

    public function testIsTwoFactorEnabledForUserForNoCode() {
        $user = $this->getMockBuilder(IUser::class)->getMock();
        $this->backupService->expects($this->once())
            ->method('hasBackupCode')
            ->with($user)
            ->will($this->returnValue(false));
        $this->assertFalse($this->provider->isTwoFactorAuthEnabledForUser($user));
    }

    public function testIsTwoFactorEnabledForUserForNoProvider() {
        $user = $this->getMockBuilder(IUser::class)->getMock();
        $this->backupService->expects($this->once())
            ->method('hasBackupCode')
            ->with($user)
            ->will($this->returnValue(true));
        $this->appManager->expects($this->once())
            ->method('getEnabledAppsForUser')
            ->with($user)
            ->willReturn(['twofactor_backup_codes' , 'federation']);
        $this->appManager->expects($this->once())
            ->method('getAppInfo')
            ->with('federation')
            ->willReturn([
                'two-factor-providers' => []
            ]);
        $this->assertFalse($this->provider->isTwoFactorAuthEnabledForUser($user));
    }
}