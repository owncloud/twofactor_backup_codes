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
namespace OCA\TwoFactorBackupCodes\Tests\Db;
use OCA\TwoFactorBackupCodes\Db\BackupCode;
use OCA\TwoFactorBackupCodes\Db\BackupCodeMapper;
use OCP\IDBConnection;
use OCP\IUser;
use Test\TestCase;
/**
 * @group DB
 */
class BackupCodeMapperTest extends TestCase {
    /** @var IDBConnection */
    private $db;
    /** @var BackupCodeMapper */
    private $mapper;
    /** @var string */
    private $testUID = 'test1';
    private function resetDB() {
        $qb = $this->db->getQueryBuilder();
        $qb->delete($this->mapper->getTableName())
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($this->testUID)));
        $qb->execute();
    }

    private function createTestCodeEntry($uid, $code) {
        $dbCode = new BackupCode();
        $dbCode->setUserId($uid);
        $dbCode->setCode($code);
        $dbEntity = $this->mapper->insert($dbCode);
        $dbCode->setId($dbEntity->getId());
        $this->assertEquals($dbCode, $dbEntity);
        return $dbEntity;
    }

    protected function setUp(): void {
        parent::setUp();
        $this->db = \OC::$server->getDatabaseConnection();
        $this->mapper = \OC::$server->query(BackupCodeMapper::class);
        $this->resetDB();
    }
    protected function tearDown(): void {
        parent::tearDown();
        $this->resetDB();
    }

    public function testGetBackupCode() {
        $entity1 = $this->createTestCodeEntry($this->testUID, '1');
        $user = $this->getMockBuilder(IUser::class)->getMock();
        $user->expects($this->once())
            ->method('getUID')
            ->will($this->returnValue($this->testUID));
        $dbCode = $this->mapper->getBackupCode($user, '1');
        $this->assertInstanceOf(BackupCode::class, $dbCode);
        $this->assertEquals($entity1->getId(), $dbCode->getId());
    }

    public function testGetBackupCodes() {
        $entity1 = $this->createTestCodeEntry($this->testUID, '1');
        $entity2 = $this->createTestCodeEntry($this->testUID, '2');
        $user = $this->getMockBuilder(IUser::class)->getMock();
        $user->expects($this->once())
            ->method('getUID')
            ->will($this->returnValue($this->testUID));
        $dbCodes = $this->mapper->getBackupCodes($user);
        $this->assertCount(2, $dbCodes);
        $this->assertInstanceOf(BackupCode::class, $dbCodes[0]);
        $this->assertInstanceOf(BackupCode::class, $dbCodes[1]);
        $this->assertEquals($entity1->getId(), $dbCodes[0]->getId());
        $this->assertEquals($entity2->getId(), $dbCodes[1]->getId());
    }
    public function testDeleteCodes() {
        $this->createTestCodeEntry($this->testUID, '1');
        $user = $this->getMockBuilder(IUser::class)->getMock();
        $user->expects($this->any())
            ->method('getUID')
            ->will($this->returnValue($this->testUID));
        $this->assertCount(1, $this->mapper->getBackupCodes($user));
        $this->mapper->deleteBackupCodes($user);
        $this->assertCount(0, $this->mapper->getBackupCodes($user));
    }
}