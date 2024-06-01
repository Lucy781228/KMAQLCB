<template>
    <div v-if="!bonuses.length">
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
        <NewBonus :modal="isAdd" :qlcb-uid="userId" @close="stopModal" :bonus-type="bonusType"/>
    </div>
    <div v-else>
        <div class="table-actions">
            <NcButton type="tertiary" ariaLabel="A" @click="startCreating">
                <template #icon>
                    <Plus :size="20" />
                </template>
            </NcButton>
            <NcButton type="tertiary" ariaLabel="B" @click="deleteBonuses">
                <template #icon>
                    <Delete :size="20" />
                </template>
            </NcButton>
        </div>
        <vue-good-table :columns="columns" :rows="bonuses" :select-options="selectOptions"
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
                <span v-if="props.column.field === 'action'" @click="editBonus(props.row)">
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
                    <NcButton @click="onDeleteBonuses" type="secondary">
                        Xóa
                    </NcButton>
                </div>
            </div>
        </NcModal>
        <NewBonus :modal="isAdd" :qlcb-uid="userId" @close="stopModal" :bonus-type="bonusType"/>
        <NewBonus :modal="isEdit" :qlcb-uid="userId" @close="stopModal" :time="time" :reason="reason"
            :department-decision="departmentDecision" :number-decision="numberDecision" :form="form" :is-edit="true"
            :bonus-id="bonusId" :bonus-type="bonusType" />
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
import NewBonus from './NewBonus.vue'

export default {
    name: 'Bonus',
    components: {
        VueGoodTablePlugin,
        Plus,
        Delete,
        Download,
        NcButton,
        NcModal,
        NcEmptyContent,
        NewBonus,
        Pencil
    },
    props: {
        userId: {
            type: String,
            required: true
        },

        bonusType: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            isAdd: false,
            isEdit: false,
            columns: [
                {
                    label: 'Thời gian',
                    field: 'time',
                    sortable: true,
                    width: '10%',
                    type: 'date',
                    dateInputFormat: 'yyyy-MM-dd',
                    dateOutputFormat: 'dd/MM/yyyy'
                },
                {
                    label: 'Hình thức',
                    field: 'form',
                    sortable: false,
                    width: '15%',
                },
                {
                    label: 'Số quyết định',
                    field: 'number_decision',
                    sortable: false,
                    width: '20%'
                },
                {
                    label: 'Cơ quan quyết định',
                    field: 'department_decision',
                    sortable: false,
                    width: '20%'
                },
                {
                    label: 'Lý do',
                    field: 'reason',
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
            bonuses: [],
            selectOptions: {
                enabled: true,
                disableSelectInfo: true,
                selectOnCheckboxOnly: true,
            },
            selectedBonuses: null,
            modal: false,
            time: null,
            form: "",
            numberDecision: "",
            departmentDecision: "",
            reason: "",
            bonusId: ""
        };
    },

    mounted() {
        this.getBonuses()
    },

    methods: {
        editBonus(row) {
            this.time = row.time;
            this.departmentDecision = row.department_decision;
            this.numberDecision = row.number_decision;
            this.reason = row.reason;
            this.bonusId = row.bonus_id;
            this.form = row.form;

            this.isEdit = true;
        },
        startCreating() {
            this.isAdd = true
        },

        stopModal() {
            this.isAdd = false
            this.isEdit = false
            this.getBonuses()
        },

        closeModal() {
            this.modal = false
        },

        deleteBonuses() {
            if (!this.selectedBonuses)
                showError(t('qlcb', 'Không có dòng nào được chọn'));
            else this.modal = true

        },

        async getBonuses() {
            try {
                let value = this.bonusType ? 1 : 0
                const response = await axios.get(generateUrl(`/apps/qlcb/bonuses/${this.userId}/${value}`));
                this.bonuses = response.data.bonuses

            } catch (e) {
                console.error(e)
            }
        },

        async onDeleteBonuses() {
            try {
                const deletePromises = this.selectedBonuses.map(bonus =>
                    axios.delete(generateUrl(`apps/qlcb/delete_bonus/${bonus.bonus_id}`))
                );
                await Promise.all(deletePromises);
                await this.getBonuses();
                showSuccess(t('qlcb', 'Xóa thành công'));
                this.modal = false
            } catch (e) {
                console.error('Error deleting selected bonuses:', e);
            }
        },

        selectionChanged({ selectedRows }) {
            if (selectedRows.length)
                this.selectedBonuses = selectedRows
            else this.selectedBonuses = null
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