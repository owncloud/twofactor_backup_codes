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
namespace OCA\TwoFactorBackupCodes\Tests\Service;

use OCA\TwoFactorBackupCodes\Db\BackupCode;
use OCA\TwoFactorBackupCodes\Db\BackupCodeMapper;
use OCA\TwoFactorBackupCodes\Service\Backup;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\IUser;
use OCP\Security\ISecureRandom;
use PHPUnit_Framework_MockObject_MockObject;
use Test\TestCase;

class BackupCodeStorageTest extends TestCase {
    /** @var BackupCodeMapper|PHPUnit_Framework_MockObject_MockObject */
    private $mapper;
    /** @var ISecureRandom|PHPUnit_Framework_MockObject_MockObject */
    private $random;
    /** @var Backup */
    private $backupService;

    protected function setUp() {
        parent::setUp();
        $this->mapper = $this->createMock(BackupCodeMapper::class);
        $this->random = $this->createMock(ISecureRandom::class);
        $this->backupService = new Backup($this->mapper, $this->random);
    }

    public function testGenerateBackupCodes() {
        $user = $this->createMock(IUser::class);
        $number = 3;
        $user->expects($this->any())
            ->method('getUID')
            ->will($this->returnValue('test1'));
        $this->random->expects($this->exactly($number))
            ->method('generate')
            ->willReturnOnConsecutiveCalls('1', '2', '3');
        $this->mapper->expects($this->exactly($number))
            ->method('insert');
        $codes = $this->backupService->generateBackupCodes($user, $number);
        $this->assertCount($number, $codes);
        $expectedCodes = ['1', '2', '3'];
        for ($i=0; $i<$number; $i++) {
            $this->assertEquals($expectedCodes[$i], $codes[$i]);
        }
    }

    public function testDeleteBackupCode() {
        $user = $this->createMock(IUser::class);
        $code = 'CODE';
        $dbCode = $this->createMock(BackupCode::class);
        $this->mapper->expects($this->once())
            ->method('getBackupCode')
            ->with($user, $code)
            ->will($this->returnValue($dbCode));
        $this->mapper->expects($this->once())
            ->method('delete')
            ->with($dbCode);
        $this->backupService->deleteBackupCode($user, $code);
    }

    public function testHasBackupCodeNoCode() {
        $user = $this->createMock(IUser::class);
        $codes = [];
        $this->mapper->expects($this->once())
            ->method('getBackupCodes')
            ->with($user)
            ->will($this->returnValue($codes));
        $this->assertFalse($this->backupService->hasBackupCode($user));
    }

    public function testHasBackupCodeWithCode() {
        $user = $this->createMock(IUser::class);
        $codes = ['1', '2'];
        $this->mapper->expects($this->once())
            ->method('getBackupCodes')
            ->with($user)
            ->will($this->returnValue($codes));
        $this->assertTrue($this->backupService->hasBackupCode($user));
    }

    public function testGetRemainingCodesCount() {
        $user = $this->createMock(IUser::class);
        $codes = ['1', '2'];
        $this->mapper->expects($this->once())
            ->method('getBackupCodes')
            ->with($user)
            ->will($this->returnValue($codes));
        $this->assertEquals(2, $this->backupService->getRemainingCodesCount($user));
    }

    public function testValidateCode() {
        $user = $this->createMock(IUser::class);
        $code = 'CODE';
        $dbCode = $this->createMock(BackupCode::class);
        $this->mapper->expects($this->once())
            ->method('getBackupCode')
            ->with($user, $code)
            ->will($this->returnValue($dbCode));
        $this->mapper->expects($this->once())
            ->method('delete')
            ->with($dbCode);
        $this->assertTrue($this->backupService->validateBackupCode($user, $code));
    }
    public function testValidateWrongCode() {
        $user = $this->createMock(IUser::class);
        $ex = $this->createMock(DoesNotExistException::class);
        $code = 'CODE';
        $this->mapper->expects($this->once())
            ->method('getBackupCode')
            ->with($user, $code)
            ->will($this->throwException($ex));
        $this->assertFalse($this->backupService->validateBackupCode($user, $code));
    }
}