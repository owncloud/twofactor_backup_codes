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
namespace OCA\TwoFactorBackupCodes\Tests\Controller;

use OCA\TwoFactorBackupCodes\Controller\SettingsController;
use OCA\TwoFactorBackupCodes\Service\Backup;
use OCP\IRequest;
use OCP\IUser;
use OCP\IUserSession;
use Test\TestCase;
class SettingsControllerTest extends TestCase {
    /** @var IRequest|PHPUnit_Framework_MockObject_MockObject */
    private $request;
    /** @var Backup|PHPUnit_Framework_MockObject_MockObject */
    private $backup;
    /** @var IUserSession|PHPUnit_Framework_MockObject_MockObject */
    private $userSession;
    /** @var SettingsController */
    private $controller;
    protected function setUp(): void {
        parent::setUp();
        $this->request = $this->getMockBuilder(IRequest::class)->getMock();
        $this->backup = $this->getMockBuilder(Backup::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->userSession = $this->getMockBuilder(IUserSession::class)->getMock();
        $this->controller = new SettingsController('twofactor_backup_codes', $this->request, $this->userSession, $this->backup);
    }
    public function testState() {
        $user = $this->getMockBuilder(IUser::class)->getMock();
        $this->userSession->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($user));
        $this->backup->expects($this->once())
            ->method('getRemainingCodesCount')
            ->will($this->returnValue(2));

        $expected = [
            'remaining' => 2,
        ];
        $this->assertEquals($expected, $this->controller->state());
    }

    public function testGenerateBackupCodes() {
        $user = $this->getMockBuilder(IUser::class)->getMock();
        $codes = ['code1', 'code2'];
        $this->userSession->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($user));
        $this->backup->expects($this->once())
            ->method('deleteBackupCodesByUser')
            ->with($user);
        $this->backup->expects($this->once())
            ->method('generateBackupCodes')
            ->with($user)
            ->will($this->returnValue($codes));
        $this->backup->expects($this->once())
            ->method('getRemainingCodesCount')
            ->with($user)
            ->will($this->returnValue(2));
        $expected = [
            'remaining' => 2,
            'codes' => $codes,
        ];
        $this->assertEquals($expected, $this->controller->generateBackupCodes());
    }

    public function testRemoveBackupCodes() {
        $user = $this->getMockBuilder(IUser::class)->getMock();
        $this->userSession->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($user));
        $this->backup->expects($this->once())
            ->method('deleteBackupCodesByUser')
            ->with($user);
        $expected = [
            'remaining' => 0
        ];
        $this->assertEquals($expected, $this->controller->removeBackupCodes());
    }
}
