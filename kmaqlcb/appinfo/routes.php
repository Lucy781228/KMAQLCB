<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Lucy <ct040407@actvn.edu.vn>
// SPDX-License-Identifier: AGPL-3.0-or-later

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\QLCB\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
        
		#Page
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],

		#User
		['name' => 'User#getAllUsers', 'url' => '/all_users', 'verb' => 'GET'],
		['name' => 'User#getUsersByType', 'url' => '/filter', 'verb' => 'GET'],
		['name' => 'User#getNCUsers', 'url' => '/nc_users', 'verb' => 'GET'],
		['name' => 'User#createUser', 'url' => '/create_user', 'verb' => 'POST'],
		['name' => 'User#getUser', 'url' => '/user/{qlcb_uid}', 'verb' => 'GET'],
        ['name' => 'User#deleteUser', 'url' => '/delete_user/{qlcb_uid}', 'verb' => 'DELETE'],
		['name' => 'User#updateUser', 'url' => '/update_user', 'verb' => 'PUT'],

		#Unit
		['name' => 'Unit#getAllUnits', 'url' => '/units', 'verb' => 'GET'],
        ['name' => 'Unit#deleteUnit', 'url' => '/delete_unit/{unit_id}', 'verb' => 'DELETE'],
		['name' => 'Unit#createUnit', 'url' => '/create_unit', 'verb' => 'POST'],

		#Position
		['name' => 'Position#getAllPositions', 'url' => '/positions', 'verb' => 'GET'],
        ['name' => 'Position#deletePosition', 'url' => '/delete_position/{position_id}', 'verb' => 'DELETE'],
		['name' => 'Position#createPosition', 'url' => '/create_position', 'verb' => 'POST'],

		#Diploma
		['name' => 'Diploma#getAllDiplomas', 'url' => '/diplomas', 'verb' => 'GET'],
        ['name' => 'Diploma#deleteDiploma', 'url' => '/delete_diploma/{diploma_id}', 'verb' => 'DELETE'],
		['name' => 'Diploma#createDiploma', 'url' => '/create_diploma', 'verb' => 'POST'],

		#Education
		['name' => 'Education#getEducations', 'url' => '/educations/{qlcb_uid}', 'verb' => 'GET'],
		['name' => 'Education#getAllEducations', 'url' => '/all_educations', 'verb' => 'GET'],
		['name' => 'Education#createEducation', 'url' => '/create_education', 'verb' => 'POST'],
        ['name' => 'Education#deleteEducation', 'url' => '/delete_education/{education_id}', 'verb' => 'DELETE'],
		['name' => 'Education#updateEducation', 'url' => '/update_education', 'verb' => 'PUT'],

		#Business
		['name' => 'Business#getBusinesses', 'url' => '/businesses/{qlcb_uid}', 'verb' => 'GET'],
		['name' => 'Business#getAllBusinesses', 'url' => '/all_businesses', 'verb' => 'GET'],
		['name' => 'Business#createBusiness', 'url' => '/create_business', 'verb' => 'POST'],
        ['name' => 'Business#deleteBusiness', 'url' => '/delete_business/{business_id}', 'verb' => 'DELETE'],
		['name' => 'Business#updateBusiness', 'url' => '/update_business', 'verb' => 'PUT'],

		#Bonus
		['name' => 'Bonus#getBonuses', 'url' => '/bonuses/{qlcb_uid}/{type}', 'verb' => 'GET'],
		['name' => 'Bonus#getAllBonuses', 'url' => '/all_bonuses/{type}', 'verb' => 'GET'],
		['name' => 'Bonus#createBonus', 'url' => '/create_bonus', 'verb' => 'POST'],
        ['name' => 'Bonus#deleteBonus', 'url' => '/delete_bonus/{bonus_id}', 'verb' => 'DELETE'],
		['name' => 'Bonus#updateBonus', 'url' => '/update_bonus', 'verb' => 'PUT'],

		#Relation
		['name' => 'Relation#getAllRelations', 'url' => '/relations/{qlcb_uid}', 'verb' => 'GET'],
		['name' => 'Relation#getTypes', 'url' => '/relation_types/{qlcb_uid}', 'verb' => 'GET'],
		['name' => 'Relation#createRelation', 'url' => '/create_relation', 'verb' => 'POST'],
        ['name' => 'Relation#deleteRelation', 'url' => '/delete_relation/{relation_id}', 'verb' => 'DELETE'],
		['name' => 'Relation#updateRelation', 'url' => '/update_relation', 'verb' => 'PUT'],


    ],
];
