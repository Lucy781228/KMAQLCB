<template>
    <div v-if="showEmpty">
        <NcLoadingIcon />
    </div>
    <NcEmptyContent v-else-if="!usersLength">
        <template #title>
            <h1 class="empty-content__title">
                Không có dữ liệu
            </h1>
        </template>
        <template #action>
            <NcButton ariaLabel="A" to="/files/newuser" type="primary">
                Thêm dữ liệu
            </NcButton>
        </template>
    </NcEmptyContent>
    <div v-else>
        <div class="table-actions">
            <NcButton type="tertiary" ariaLabel="A" to="/files/newuser">
                <template #icon>
                    <Plus :size="20" />
                </template>
            </NcButton>
            <NcButton type="tertiary" ariaLabel="B" @click="deleteUsers">
                <template #icon>
                    <Delete :size="20" />
                </template>
            </NcButton>
            <NcButton type="tertiary" ariaLabel="C" @click="exportExcel">
                <template #icon>
                    <ArrowCollapseDown :size="20" />
                </template>
            </NcButton>
        </div>
        <vue-good-table @on-cell-click="onCellClick" :columns="columns" :rows="usersFilter"
            :select-options="selectOptions" @on-selected-rows-change="selectionChanged" :pagination-options="{
        enabled: true,
        mode: 'records',
        perPage: 10,
        position: 'bottom',
        perPageDropdownEnabled: false,
        nextLabel: 'Sau',
        prevLabel: 'Trước',
        ofLabel: 'trên',
    }">
        </vue-good-table>
        <NcModal :show="modal" @close="closeModal" :canClose="false">
            <div class="modal__content">
                <h3>Bạn chắc chắn không?</h3>
                <div class="modal__actions">
                    <NcButton @click="closeModal" type="primary">
                        Hủy
                    </NcButton>
                    <NcButton @click="onDeleteUsers" type="secondary">
                        Xóa
                    </NcButton>
                </div>
            </div>
        </NcModal>
    </div>
</template>

<script>
import VueGoodTablePlugin from 'vue-good-table';
import 'vue-good-table/dist/vue-good-table.css';
import axios from "@nextcloud/axios";
import { generateUrl } from '@nextcloud/router'
import { NcButton, NcActions, NcActionCheckbox, NcActionButton, NcEmptyContent, NcModal, NcLoadingIcon } from "@nextcloud/vue";
import Plus from 'vue-material-design-icons/Plus'
import FilterVariant from 'vue-material-design-icons/FilterVariant'
import ArrowCollapseDown from 'vue-material-design-icons/ArrowCollapseDown'
import Delete from 'vue-material-design-icons/Delete'
import { showError, showSuccess } from '@nextcloud/dialogs'
import * as XLSX from 'xlsx';

export default {
    name: 'TableContent',
    components: {
        VueGoodTablePlugin,
        FilterVariant,
        Plus,
        Delete,
        ArrowCollapseDown,
        NcButton,
        NcActionButton,
        NcActionCheckbox,
        NcActions,
        NcModal,
        NcEmptyContent,
        NcLoadingIcon
    },
    data() {
        return {
            columns: [
                {
                    label: 'Họ và tên',
                    field: 'full_name',
                    sortable: true,
                    width: '20%'
                },
                {
                    label: 'Ngày sinh',
                    field: 'date_of_birth',
                    sortable: false,
                    width: '10%',
                    type: 'date',
                    dateInputFormat: 'yyyy-MM-dd',
                    dateOutputFormat: 'dd/MM/yyyy'
                },
                {
                    label: 'Giới tính',
                    field: 'gender',
                    sortable: false,
                    width: '5%',
                    formatFn: this.formatGender
                },
                {
                    label: 'Đơn vị',
                    field: 'unit_name',
                    sortable: false,
                    width: '15%'
                },
                {
                    label: 'Chức vụ',
                    field: 'position_name',
                    sortable: false,
                    width: '15%'
                },
                {
                    label: 'Tên người dùng',
                    field: 'qlcb_uid',
                    sortable: false,
                    width: '15%'
                },
                {
                    label: 'Email',
                    field: 'email',
                    sortable: false,
                    width: '20%'
                },
            ],
            users: [],
            selectOptions: {
                enabled: true,
                disableSelectInfo: true,
                selectOnCheckboxOnly: true,
            },
            usersFilter: [],
            types: [],
            selectedUsers: null,
            modal: false,
            showEmpty: true
        };
    },

    mounted() {
        this.getUsers()
    },

    computed: {
        usersLength() {
            return this.users.length
        }
    },

    methods: {
        closeModal() {
            this.modal = false
        },

        deleteUsers() {
            if (!this.selectedUsers)
                showError(t('qlcb', 'Không có dòng nào được chọn'));
            else this.modal = true

        },

        onCellClick(params) {
            if (params.column.field == 'gender' || params.column.field == 'full_name' || params.column.field == 'date_of_birth'
                || params.column.field == 'email' || params.column.field == 'unit_name'
                || params.column.field == 'position_name' || params.column.field == 'qlcb_uid') {
                const userId = params.row.qlcb_uid;
                this.$router.push({ name: 'Details', params: { user_id: userId } });
            }
        },
        formatGender(value) {
            return value === 0 ? 'Nam' : 'Nữ';
        },

        async getUsers() {
            try {
                const response = await axios.get(generateUrl('apps/qlcb/all_users'))
                this.users = response.data.users
                this.usersFilter = this.users
                console.log(this.users.length)
                this.showEmpty = false
            } catch (e) {
                console.error(e)
            }
        },

        async onDeleteUsers() {
            try {
                const deletePromises = this.selectedUsers.map(user =>
                    axios.delete(generateUrl(`apps/qlcb/delete_user/${user.qlcb_uid}`))
                );
                await Promise.all(deletePromises);
                await this.getUsers();
                showSuccess(t('qlcb', 'Xóa thành công'));
                this.modal = false
            } catch (e) {
                console.error('Error deleting selected users:', e);
            }
        },

        selectionChanged({ selectedRows }) {
            if (selectedRows.length)
                this.selectedUsers = selectedRows
            else this.selectedUsers = null
            console.log(selectedRows);
        },
        formatDateToDDMMYYYY(inputDate) {
            const datePart = inputDate.split("-")
            return `${datePart[2]}-${datePart[1]}-${datePart[0]}`
        },

        exportExcel() {
            const data = this.usersFilter.map((item) => {
                return {
                    'Họ và tên': item.full_name,
                    'Ngày sinh': this.formatDateToDDMMYYYY(item.date_of_birth),
                    'Giới tính': item.gender ? 'Nữ' : 'Nam',
                    'Đơn vị': item.unit_name,
                    'Chức vụ': item.position_name,
                    'Tên người dùng': item.qlcb_uid,
                    'Email': item.email
                };
            });

            const workbook = XLSX.utils.book_new();
            const worksheet = XLSX.utils.json_to_sheet(data);

            const colWidths = Object.keys(data[0]).map(key => {
                return { wch: Math.max(...data.map(row => row[key] ? row[key].toString().length : 0), key.length) };
            });

            worksheet['!cols'] = colWidths;
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Users');

            const filename = 'CanBo.xlsx';
            XLSX.writeFile(workbook, filename);
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
</style>