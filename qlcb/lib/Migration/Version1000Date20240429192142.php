<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2024 Your name <your@email.com>
 *
 * @author Your name <your@email.com>
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\QLCB\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version1000Date20240429192142 extends SimpleMigrationStep
{
    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(
        IOutput $output,
        Closure $schemaClosure,
        array $options
    ): ?ISchemaWrapper {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        //qlcb_diploma

        if (!$schema->hasTable("qlcb_diploma")) {
            $table = $schema->createTable("qlcb_diploma");
            $table->addColumn("diploma_id", "smallint", [
                "autoincrement" => true,
                "notnull" => true,
                "unsigned" => true,
            ]);
            $table->addColumn("diploma_name", "string", [
                "notnull" => true,
                "length" => 255,
            ]);

            $table->setPrimaryKey(["diploma_id"]);
        }

        //qlcb_position

        if (!$schema->hasTable("qlcb_position")) {
            $table = $schema->createTable("qlcb_position");
            $table->addColumn("position_id", "string", [
                "notnull" => true,
                "length" => 64,
                "default" => "",
            ]);
            $table->addColumn("position_name", "string", [
                "notnull" => true,
                "length" => 255,
            ]);
            $table->setPrimaryKey(["position_id"]);
        }

        //qlcb_unit

        if (!$schema->hasTable("qlcb_unit")) {
            $table = $schema->createTable("qlcb_unit");
            $table->addColumn("unit_id", "string", [
                "notnull" => true,
                "length" => 64,
                "default" => "",
            ]);
            $table->addColumn("unit_name", "string", [
                "notnull" => true,
                "length" => 255,
            ]);
            $table->setPrimaryKey(["unit_id"]);
        }

        // qlcb_user

        if (!$schema->hasTable("qlcb_user")) {
            $table = $schema->createTable("qlcb_user");
            $table->addColumn("qlcb_uid", "string", [
                "notnull" => true,
                "length" => 64,
            ]);
            $table->addColumn("full_name", "string", [
                "notnull" => true,
                "length" => 64,
            ]);
            $table->addColumn("date_of_birth", "date", [
                "notnull" => true,
            ]);
            $table->addColumn("gender", "boolean", [
                "notnull" => false,
            ]);
            $table->addColumn("phone", "string", [
                "notnull" => true,
                "length" => 10,
            ]);
            $table->addColumn("address", "string", [
                "notnull" => true,
                "length" => 255,
            ]);
            $table->addColumn("id_number", "string", [
                "notnull" => true,
                "length" => 12,
            ]);
            $table->addColumn("email", "string", [
                "notnull" => true,
                "length" => 255,
            ]);
            $table->addColumn("position_id", "string", [
                "notnull" => true,
                "length" => 64,
            ]);
            $table->addColumn("coefficients_salary", "decimal", [
                "notnull" => false,
                "precision" => 4,
                "scale" => 2,
            ]);
            $table->addColumn("tax", "smallint", [
                "unsigned" => true,
                "notnull" => true,
            ]);
            $table->addColumn("day_joined", "date", [
                "notnull" => true,
            ]);
            $table->addColumn("communist_party_joined", "date", [
                "notnull" => false,
            ]);
			$table->addColumn("unit_id", "string", [
                "notnull" => true,
                "length" => 64,
            ]);

            $table->setPrimaryKey(["qlcb_uid"]);
            $table->addUniqueIndex(["phone"], "unique_phone");
            $table->addUniqueIndex(["id_number"], "unique_id_number");
            $table->addUniqueIndex(["email"], "unique_email");
            $table->addIndex(['position_id'], 'position_index');
            $table->addIndex(['unit_id'], 'unit_index');
        }

        //qlcb_relation

        if (!$schema->hasTable("qlcb_relation")) {
            $table = $schema->createTable("qlcb_relation");
            $table->addColumn("relation_id", "integer", [
                "notnull" => true,
                "unsigned" => true,
                "autoincrement" => true,
            ]);
            $table->addColumn("qlcb_uid", "string", [
                "notnull" => true,
                "length" => 64,
            ]);
            $table->addColumn("full_name", "string", [
                "notnull" => true,
                "length" => 64,
            ]);
            $table->addColumn("date_of_birth", "date", [
                "notnull" => false,
            ]);
            $table->addColumn("phone", "string", [
                "notnull" => false,
                "length" => 10,
            ]);
            $table->addColumn("address", "string", [
                "notnull" => false,
                "length" => 255,
            ]);
            $table->addColumn("relationship", "smallint", [
                "notnull" => true,
                "unsigned" => true,
            ]);
            $table->setPrimaryKey(["relation_id"]);
            $table->addIndex(['qlcb_uid'], 'uid_index');
        }

        //qlcb_education

        if (!$schema->hasTable("qlcb_education")) {
            $table = $schema->createTable("qlcb_education");
            $table->addColumn("education_id", "integer", [
                "notnull" => true,
                "unsigned" => true,
                "autoincrement" => true,
            ]);
            $table->addColumn("qlcb_uid", "string", [
                "notnull" => true,
                "length" => 64,
            ]);
            $table->addColumn("start_date", "date", [
                "notnull" => true,
            ]);
            $table->addColumn("end_date", "date", [
                "notnull" => true,
            ]);
            $table->addColumn("training_unit", "string", [
                "notnull" => true,
                "length" => 64,
            ]);
            $table->addColumn("specialization", "string", [
                "notnull" => true,
                "length" => 64,
            ]);
            $table->addColumn("diploma_id", "smallint", [
                "notnull" => true,
                "unsigned" => true,
            ]);
            $table->addColumn("result", "string", [
                "notnull" => true,
                "length" => 64,
            ]);
            $table->setPrimaryKey(["education_id"]);
            $table->addIndex(['qlcb_uid'], 'uid_index');
            $table->addIndex(['diploma_id'], 'diploma_index');
        }

        //qlcb_business

        if (!$schema->hasTable("qlcb_business")) {
            $table = $schema->createTable("qlcb_business");
            $table->addColumn("business_id", "integer", [
                "notnull" => true,
                "unsigned" => true,
                "autoincrement" => true,
            ]);
            $table->addColumn("qlcb_uid", "string", [
                "notnull" => true,
                "length" => 64,
            ]);
            $table->addColumn("start_date", "date", [
                "notnull" => true,
            ]);
            $table->addColumn("end_date", "date", [
                "notnull" => true,
            ]);
            $table->addColumn("unit", "string", [
                "notnull" => true,
                "length" => 255,
            ]);
            $table->addColumn("position", "string", [
                "notnull" => true,
                "length" => 255,
            ]);
            $table->setPrimaryKey(["business_id"]);
            $table->addIndex(['qlcb_uid'], 'uid_index');
        }

        //qlcb_bonus

        if (!$schema->hasTable("qlcb_bonus")) {
            $table = $schema->createTable("qlcb_bonus");
            $table->addColumn("bonus_id", "integer", [
                "notnull" => true,
                "unsigned" => true,
                "autoincrement" => true,
            ]);
            $table->addColumn("qlcb_uid", "string", [
                "notnull" => true,
                "length" => 64,
            ]);
            $table->addColumn("type", "boolean", [
                "notnull" => false,
            ]);
            $table->addColumn("form", "string", [
                "notnull" => true,
                "length" => 64,
            ]);
            $table->addColumn("time", "date", [
                "notnull" => true,
            ]);
            $table->addColumn("reason", "string", [
                "notnull" => true,
                "length" => 255,
            ]);
            $table->addColumn("number_decision", "string", [
                "notnull" => true,
                "length" => 64,
            ]);
            $table->addColumn("department_decision", "string", [
                "notnull" => true,
                "length" => 64,
            ]);

            $table->setPrimaryKey(["bonus_id"]);
            $table->addIndex(['qlcb_uid'], 'uid_index');
        }

        $unit = $schema->getTable('qlcb_unit');
        $position = $schema->getTable('qlcb_position');
        $user = $schema->getTable('qlcb_user');
		$user->addForeignKeyConstraint($unit, ['unit_id'], ['unit_id'], ['onDelete' => 'CASCADE']);
		$user->addForeignKeyConstraint($position, ['position_id'], ['position_id'], ['onDelete' => 'CASCADE']);

        $business = $schema->getTable('qlcb_business');
		$business->addForeignKeyConstraint($user, ['qlcb_uid'], ['qlcb_uid'], ['onDelete' => 'CASCADE']);

        $diploma = $schema->getTable('qlcb_diploma');
        $education = $schema->getTable('qlcb_education');
		$education->addForeignKeyConstraint($user, ['qlcb_uid'], ['qlcb_uid'], ['onDelete' => 'CASCADE']);
        $education->addForeignKeyConstraint($diploma, ['diploma_id'], ['diploma_id'], ['onDelete' => 'CASCADE']);

        $relation = $schema->getTable('qlcb_relation');
		$relation->addForeignKeyConstraint($user, ['qlcb_uid'], ['qlcb_uid'], ['onDelete' => 'CASCADE']);

        $bonus = $schema->getTable('qlcb_bonus');
		$bonus->addForeignKeyConstraint($user, ['qlcb_uid'], ['qlcb_uid'], ['onDelete' => 'CASCADE']);
        return $schema;
    }
}
