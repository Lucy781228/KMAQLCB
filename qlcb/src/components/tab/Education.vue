<template>
    <div v-if="!educations.length">
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
        <NewEducation :modal="isAdd" :qlcb-uid="userId" @close="stopModal" />
    </div>
    <div v-else>
        <div class="table-actions">
            <NcButton type="tertiary" ariaLabel="A" @click="startCreating">
                <template #icon>
                    <Plus :size="20" />
                </template>
            </NcButton>
            <NcButton type="tertiary" ariaLabel="B" @click="deleteEducations">
                <template #icon>
                    <Delete :size="20" />
                </template>
            </NcButton>
        </div>
        <vue-good-table :columns="columns" :rows="educations" :select-options="selectOptions"
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
                <span v-if="props.column.field === 'action'" @click="editEducation(props.row)">
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
                    <NcButton @click="onDeleteEducations" type="secondary">
                        Xóa
                    </NcButton>
                </div>
            </div>
        </NcModal>
        <NewEducation :modal="isAdd" :qlcb-uid="userId" @close="stopModal" />
        <NewEducation :modal="isEdit" :qlcb-uid="userId" @close="stopModal" :diploma-id="diplomaId" :end-date="endDate"
            :result="result" :specialization="specialization" :start-date="startDate" :training-unit="trainingUnit"
            :is-edit="true" :education-id="educationId" />
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
import NewEducation from './NewEducation.vue'

export default {
    name: 'Education',
    components: {
        VueGoodTablePlugin,
        Plus,
        Delete,
        Download,
        NcButton,
        NcModal,
        NcEmptyContent,
        NewEducation,
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
                    label: 'Ngày bắt đầu',
                    field: 'start_date',
                    sortable: true,
                    width: '10%',
                    type: 'date',
                    dateInputFormat: 'yyyy-MM-dd',
                    dateOutputFormat: 'dd/MM/yyyy'
                },
                {
                    label: 'Ngày kết thúc',
                    field: 'end_date',
                    sortable: false,
                    width: '10%',
                    type: 'date',
                    dateInputFormat: 'yyyy-MM-dd',
                    dateOutputFormat: 'dd/MM/yyyy'
                },
                {
                    label: 'Chuyên ngành',
                    field: 'specialization',
                    sortable: false,
                    width: '15%',
                },
                {
                    label: 'Văn bằng',
                    field: 'diploma_name',
                    sortable: false,
                    width: '15%'
                },
                {
                    label: 'Đơn vị',
                    field: 'training_unit',
                    sortable: false,
                    width: '15%'
                },
                {
                    label: 'Kết quả',
                    field: 'result',
                    sortable: false,
                    width: '30%'
                },
                {
                    label: 'Hành động',
                    field: 'action',
                    sortable: false,
                    width: '5%'
                },
            ],
            educations: [],
            selectOptions: {
                enabled: true,
                disableSelectInfo: true,
                selectOnCheckboxOnly: true,
            },
            selectedEducations: null,
            modal: false,
            diplomaId: "",
            startDate: null,
            endDate: null,
            trainingUnit: "",
            specialization: "",
            result: "",
            educationId: ""
        };
    },

    mounted() {
        this.getEducations()
    },

    methods: {
        editEducation(row) {
            this.diplomaId = row.diploma_id;
            this.startDate = row.start_date;
            this.endDate = row.end_date;
            this.trainingUnit = row.training_unit;
            this.specialization = row.specialization;
            this.result = row.result;
            this.educationId = row.education_id;

            this.isEdit = true;
        },
        startCreating() {
            this.isAdd = true
            console.log(this.isAdd)
        },

        stopModal() {
            this.isAdd = false
            this.isEdit = false
            this.getEducations()
        },

        closeModal() {
            this.modal = false
        },

        deleteEducations() {
            if (!this.selectedEducations)
                showError(t('qlcb', 'Không có dòng nào được chọn'));
            else this.modal = true

        },

        async getEducations() {
            try {
                const response = await axios.get(generateUrl(`/apps/qlcb/educations/${this.userId}`));
                this.educations = response.data.educations
                console.log(this.educations.length)

            } catch (e) {
                console.error(e)
            }
        },

        async onDeleteEducations() {
            try {
                const deletePromises = this.selectedEducations.map(education =>
                    axios.delete(generateUrl(`apps/qlcb/delete_education/${education.education_id}`))
                );
                await Promise.all(deletePromises);
                await this.getEducations();
                showSuccess(t('qlcb', 'Xóa thành công'));
                this.modal = false
            } catch (e) {
                console.error('Error deleting selected educations:', e);
            }
        },

        selectionChanged({ selectedRows }) {
            if (selectedRows.length)
                this.selectedEducations = selectedRows
            else this.selectedEducations = null
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