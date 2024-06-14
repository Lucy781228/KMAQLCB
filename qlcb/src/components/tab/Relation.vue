<template>
    <div v-if="!relations.length">
        <NcEmptyContent>
            <template #title>
                <h1 class="empty-content__title">
                    Không có dữ liệu
                </h1>
            </template>
            <template #action>
                <NcButton ariaLabel="A" @click="startCreating" type="primary">
                    Thêm dữ liệu
                </NcButton>
            </template>
        </NcEmptyContent>
        <NewRelation :modal="isAdd" :qlcb-uid="userId" @close="stopModal" />
    </div>
    <div v-else>
        <div class="table-actions">
            <NcButton type="tertiary" ariaLabel="A" @click="startCreating">
                <template #icon>
                    <Plus :size="20" />
                </template>
            </NcButton>
            <NcButton type="tertiary" ariaLabel="B" @click="deleteRelations">
                <template #icon>
                    <Delete :size="20" />
                </template>
            </NcButton>
        </div>
        <vue-good-table :columns="columns" :rows="relations" :select-options="selectOptions"
            @on-selected-rows-change="selectionChanged" :pagination-options="{
        enabled: true,
        mode: 'records',
        perPage: 5,
        position: 'bottom',
        perPageDropdownEnabled: false,
        nextLabel: 'Sau',
        prevLabel: 'Trước',
        ofLabel: 'trên',
    }">
            <template slot="table-row" slot-scope="props">
                <span v-if="props.column.field === 'action'" @click="editRelation(props.row)">
                    <Pencil :size="20" class="icon" />
                </span>
            </template>
        </vue-good-table>
        <NcModal :show="modal" @close="closeModal" :canClose="false">
            <div class="modal__content">
                <h3>Bạn chắc chắn không?</h3>
                <div class="modal__actions">
                    <NcButton @click="closeModal" type="primary">
                        Hủy
                    </NcButton>
                    <NcButton @click="onDeleteRelations" type="secondary">
                        Xóa
                    </NcButton>
                </div>
            </div>
        </NcModal>
        <NewRelation :modal="isAdd" :qlcb-uid="userId" @close="stopModal" />
        <NewRelation :modal="isEdit" :qlcb-uid="userId" @close="stopModal" :full-name="fullName" :date-of-birth="dateOfBirth"
            :phone="phone" :address="address" :relationship="relationship"
            :is-edit="true" :relation-id="relationId" />
    </div>
</template>

<script>
import VueGoodTablePlugin from 'vue-good-table';
import 'vue-good-table/dist/vue-good-table.css';
import axios from "@nextcloud/axios";
import { generateUrl } from '@nextcloud/router'
import { NcButton, NcEmptyContent, NcModal } from "@nextcloud/vue";
import Plus from 'vue-material-design-icons/Plus'
import Pencil from 'vue-material-design-icons/Pencil'
import Download from 'vue-material-design-icons/Download'
import Delete from 'vue-material-design-icons/Delete'
import { showError, showSuccess } from '@nextcloud/dialogs'
import NewRelation from './NewRelation.vue'

export default {
    name: 'Relation',
    components: {
        VueGoodTablePlugin,
        Plus,
        Delete,
        Download,
        NcButton,
        NcModal,
        NcEmptyContent,
        NewRelation,
        Pencil
    },
    props: {
        userId: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            isAdd: false,
            isEdit: false,
            columns: [
                {
                    label: 'Họ và tên',
                    field: 'full_name',
                    sortable: true,
                    width: '20%'
                },
                {
                    label: 'Ngày sinh',
                    field: 'date_of_birth',
                    sortable: false,
                    width: '10%',
                    type: 'date',
                    dateInputFormat: 'yyyy-MM-dd',
                    dateOutputFormat: 'dd/MM/yyyy'
                },
                {
                    label: 'Số điện thoại',
                    field: 'phone',
                    sortable: false,
                    width: '10%',
                },
                {
                    label: 'Địa chỉ',
                    field: 'address',
                    sortable: false,
                    width: '35%'
                },
                {
                    label: 'Mối quan hệ',
                    field: 'relationship',
                    sortable: false,
                    width: '20%',
                    formatFn: this.formatType
                },
                {
                    label: 'Hành động',
                    field: 'action',
                    sortable: false,
                    width: '5%'
                },
            ],
            relations: [],
            selectOptions: {
                enabled: true,
                disableSelectInfo: true,
                selectOnCheckboxOnly: true,
            },
            selectedRelations: null,
            modal: false,
            fullName: "",
            dateOfBirth: null,
            phone: "",
            address: "",
            relationship: "",
            relationId: "",
            types: [
                { text: 'Bố ruột', value: 0, is_single: true },
                { text: 'Mẹ ruột', value: 1, is_single: true },
                { text: 'Bố ruột của vợ/chồng', value: 2, is_single: true },
                { text: 'Mẹ ruột của vợ/chồng', value: 3, is_single: true },
                { text: 'Người nuôi dưỡng hợp pháp', value: 4, is_single: true },
                { text: 'Người nuôi dưỡng hợp pháp của vợ/chồng', value: 5, is_single: true },
                { text: 'Vợ/Chồng', value: 6, is_single: true },
                { text: 'Con ruột', value: 7, is_single: false },
                { text: 'Con nuôi hợp pháp', value: 8, is_single: false }
            ],
        };
    },

    mounted() {
        this.getRelations()
    },

    methods: {
        formatType(value) {
            const typeObj = this.types.find(type => type.value === value);
            return typeObj.text;
        },
        editRelation(row) {
            this.fullName = row.full_name;
            this.dateOfBirth = row.date_of_birth;
            this.phone = row.phone;
            this.address = row.address;
            this.relationship = row.relationship;
            this.relationId = row.relation_id;

            this.isEdit = true;
        },
        startCreating() {
            this.isAdd = true
            console.log(this.isAdd)
        },

        stopModal() {
            this.isAdd = false
            this.isEdit = false
            this.getRelations()
        },

        closeModal() {
            this.modal = false
        },

        deleteRelations() {
            if (!this.selectedRelations)
                showError(t('qlcb', 'Không có dòng nào được chọn'));
            else this.modal = true

        },

        async getRelations() {
            try {
                const response = await axios.get(generateUrl(`/apps/qlcb/relations/${this.userId}`));
                this.relations = response.data.relations
                console.log(this.relations.length)

            } catch (e) {
                console.error(e)
            }
        },

        async onDeleteRelations() {
            try {
                const deletePromises = this.selectedRelations.map(relation =>
                    axios.delete(generateUrl(`apps/qlcb/delete_relation/${relation.relation_id}`))
                );
                await Promise.all(deletePromises);
                await this.getRelations();
                showSuccess(t('qlcb', 'Xóa thành công'));
                this.modal = false
            } catch (e) {
                console.error('Error deleting selected Relations:', e);
            }
        },

        selectionChanged({ selectedRows }) {
            if (selectedRows.length)
                this.selectedRelations = selectedRows
            else this.selectedRelations = null
            console.log(selectedRows);
        },
    }
};
</script>

<style scoped>
.table-actions {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 10px;
}

.modal__content {
    margin: 20px;
    text-align: center;
}

.modal__actions {
    display: flex;
    justify-content: flex-end;
    gap: 20px;
    margin-top: 20px;
}

.icon:hover {
    cursor: pointer;
}
</style>