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
return [
	'routes' => [
		[
			'name' => 'settings#state',
			'url' => '/settings/state',
			'verb' => 'GET'
		],
		[
			'name' => 'settings#generateBackupCodes',
			'url' => '/settings/generateBackupCodes',
			'verb' => 'POST'
		],
		[
			'name' => 'settings#removeBackupCodes',
			'url' => '/settings/removeBackupCodes',
			'verb' => 'DELETE'
		],
	]
];
