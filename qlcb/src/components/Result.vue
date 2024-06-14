<template>
    <div v-if="users.length == 0">
        <NcEmptyContent>
            <template #title>
                <h1 class="empty-content__title">
                    Không tìm thấy
                </h1>
            </template>
            <template #action>
                <NcButton ariaLabel="A" @click="close" type="primary">
                    Trở lại
                </NcButton>
            </template>
        </NcEmptyContent>
    </div>
    <div v-else>
        <h2>{{ title }}</h2>
        <div class="buttons">
            <NcButton aria-label="Example text" @click="close" type="tertiary">
                <template #icon>
                    <ArrowLeft :size="20" />
                </template>
            </NcButton>
            <NcButton aria-label="Example text" type="tertiary" @click="exportExcel">
                <template #icon>
                    <Download :size="20" />
                </template>
            </NcButton>
        </div>
        <vue-good-table :columns="columns" :rows="users" :select-options="false" :pagination-options="{
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
    </div>
</template>
<script>
import VueGoodTablePlugin from 'vue-good-table';
import 'vue-good-table/dist/vue-good-table.css';
import axios from "@nextcloud/axios";
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess } from '@nextcloud/dialogs'
import { NcAppSidebar, NcAppSidebarTab, NcButton, NcEmptyContent } from "@nextcloud/vue";
import ArrowLeft from 'vue-material-design-icons/ArrowLeft.vue';
import Download from 'vue-material-design-icons/Download.vue';
import * as XLSX from 'xlsx';


export default {
    name: 'Result',
    props: {
        show: {
            type: Boolean,
            default: false
        },
        selectedOption: {
            type: Number,
            default: -1
        },
        unitId: {
            type: String,
            default: ""
        },
        positionId: {
            type: String,
            default: ""
        },
        fullName: {
            type: String,
            default: ""
        },
        start: {
            type: String,
            default: ""
        },
        end: {
            type: String,
            default: ""
        },
        isCommunity: {
            type: String,
            default: ""
        },
    },
    components: {
        NcButton,
        Download,
        ArrowLeft,
        NcEmptyContent
    },
    data() {
        return {
            userColumns: [
                {
                    label: 'Họ và tên',
                    field: 'full_name',
                    sortable: true,
                    width: '25%'
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
                    width: '25%'
                },
            ],

            educationColumns: [
                {
                    label: 'Họ và tên',
                    field: 'full_name',
                    sortable: true,
                    width: '15%',
                },
                {
                    label: 'Tên người dùng',
                    field: 'qlcb_uid',
                    sortable: false,
                    width: '15%',
                },
                {
                    label: 'Ngày bắt đầu',
                    field: 'start_date',
                    sortable: false,
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
                    width: '10%',
                },
                {
                    label: 'Văn bằng',
                    field: 'diploma_name',
                    sortable: false,
                    width: '10%'
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
                    width: '15%'
                }
            ],
            businessColumns: [
                {
                    label: 'Họ và tên',
                    field: 'full_name',
                    sortable: true,
                    width: '15%',
                },
                {
                    label: 'Tên người dùng',
                    field: 'qlcb_uid',
                    sortable: false,
                    width: '15%',
                },
                {
                    label: 'Ngày bắt đầu',
                    field: 'start_date',
                    sortable: true,
                    width: '15%',
                    type: 'date',
                    dateInputFormat: 'yyyy-MM-dd',
                    dateOutputFormat: 'dd/MM/yyyy'
                },
                {
                    label: 'Ngày kết thúc',
                    field: 'end_date',
                    sortable: false,
                    width: '15%',
                    type: 'date',
                    dateInputFormat: 'yyyy-MM-dd',
                    dateOutputFormat: 'dd/MM/yyyy'
                },
                {
                    label: 'Đơn vị',
                    field: 'unit',
                    sortable: false,
                    width: '20%'
                },
                {
                    label: 'Chức vụ',
                    field: 'position',
                    sortable: false,
                    width: '20%'
                }
            ],
            bonusColumns: [
                {
                    label: 'Họ và tên',
                    field: 'full_name',
                    sortable: true,
                    width: '15%',
                },
                {
                    label: 'Tên người dùng',
                    field: 'qlcb_uid',
                    sortable: false,
                    width: '15%',
                },
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
                    width: '10%'
                },
                {
                    label: 'Cơ quan quyết định',
                    field: 'department_decision',
                    sortable: false,
                    width: '15%'
                },
                {
                    label: 'Lý do',
                    field: 'reason',
                    sortable: false,
                    width: '20%'
                }
            ],
            users: [],
        }
    },

    computed: {
        columns() {
            if (this.selectedOption == 0) {
                return this.userColumns;
            } else if (this.selectedOption == 1) {
                return this.businessColumns;
            } else if (this.selectedOption == 2) {
                return this.educationColumns;
            } else return this.bonusColumns;
        },
        title() {
            if (this.selectedOption == 0) {
                return 'DANH SÁCH CÁN BỘ'
            } else if (this.selectedOption == 1) {
                return 'DANH SÁCH QUÁ TRÌNH CÔNG TÁC'
            } else if (this.selectedOption == 2) {
                return 'DANH SÁCH QUÁ TRÌNH ĐÀO TẠO'
            } else if (this.selectedOption == 3) {
                return 'DANH SÁCH QUÁ TRÌNH KHEN THƯỞNG'
            } else return 'DANH SÁCH QUÁ TRÌNH KỶ LUẬT'
        }
    },
    watch: {
        show: {
            handler(newVal, oldVal) {
                if (newVal != oldVal) {
                    this.setData()
                }
            },
            immediate: true
        }
    },

    mounted() {

    },

    methods: {
        close() {
            this.$emit('close');
        },

        formatGender(value) {
            return value === 0 ? 'Nam' : 'Nữ';
        },

        setData() {
            if (this.selectedOption == 0) {
                this.getUsers();
            } else if (this.selectedOption == 2) {
                this.getEducations();
            } else if (this.selectedOption == 1) {
                this.getBusinesses();
            } else this.getBonuses()
        },

        async getUsers() {
            try {
                let response = await axios.get(generateUrl('apps/qlcb/all_users'))
                let array = response.data.users

                if (this.unitId) {
                    array = array.filter(user => user.unit_id == this.unitId);
                }

                if (this.positionId) {
                    array = array.filter(user => user.position_id == this.positionId);
                }

                if (this.fullName) {
                    array = array.filter(user => user.full_name.includes(this.fullName));
                }

                if (this.start) {
                    array = array.filter(user => new Date(user.date_of_birth) >= new Date(this.start));
                }

                if (this.end) {
                    array = array.filter(user => new Date(user.date_of_birth) <= new Date(this.end));
                }

                this.users = array;
            } catch (e) {
                console.error(e)
            }
        },

        async getEducations() {
            try {
                const response = await axios.get(generateUrl('apps/qlcb/all_educations'))
                let array = response.data.educations

                if (this.start) {
                    array = array.filter(user => new Date(user.start_date) >= new Date(this.start));
                }

                if (this.end) {
                    array = array.filter(user => new Date(user.end_date) <= new Date(this.end));
                }

                this.users = array;
            } catch (e) {
                console.error(e)
            }
        },

        formatDateToDDMMYYYY(inputDate) {
            const datePart = inputDate.split("-")
            return `${datePart[2]}-${datePart[1]}-${datePart[0]}`
        },


        async getBusinesses() {
            try {
                const response = await axios.get(generateUrl('apps/qlcb/all_businesses'))
                let array = response.data.businesses

                if (this.start) {
                    array = array.filter(user => new Date(user.start_date) >= new Date(this.start));
                }

                if (this.end) {
                    array = array.filter(user => new Date(user.end_date) <= new Date(this.end));
                }

                this.users = array;
            } catch (e) {
                console.error(e)
            }
        },

        async getBonuses() {
            try {
                let type = this.selectedOption == 3 ? 1 : 0
                const response = await axios.get(generateUrl(`apps/qlcb/all_bonuses+${type}`))
                let array = response.data.bonuses

                if (this.start) {
                    array = array.filter(user => new Date(user.time) >= new Date(this.start));
                }

                if (this.end) {
                    array = array.filter(user => new Date(user.time) <= new Date(this.end));
                }

                this.users = array;
            } catch (e) {
                console.error(e)
            }
        },

        exportExcel() {
            const data = this.users.map((item) => {
                switch (this.selectedOption) {
                    case 0:

                        return {
                            'Họ và tên': item.full_name,
                            'Ngày sinh': this.formatDateToDDMMYYYY(item.date_of_birth),
                            'Giới tính': item.gender ? 'Nữ' : 'Nam',
                            'Đơn vị': item.unit_name,
                            'Chức vụ': item.position_name,
                            'Tên người dùng': item.qlcb_uid,
                            'Email': item.email
                        };
                        break;
                    case 1:

                        return {
                            'Họ và tên': item.full_name,
                            'Tên người dùng': item.qlcb_uid,
                            'Ngày bắt đầu': this.formatDateToDDMMYYYY(item.start_date),
                            'Ngày kết thúc': this.formatDateToDDMMYYYY(item.end_date),
                            'Đơn vị': item.unit,
                            'Chức vụ': item.position
                        };
                        break;

                    case 2:

                        return {
                            'Họ và tên': item.full_name,
                            'Tên người dùng': item.qlcb_uid,
                            'Ngày bắt đầu': this.formatDateToDDMMYYYY(item.start_date),
                            'Ngày kết thúc': this.formatDateToDDMMYYYY(item.end_date),
                            'Chuyên ngành': item.specialization,
                            'Văn bằng': item.diploma_name,
                            'Đơn vị': item.training_unit,
                            'Kết quả': item.result
                        };
                        break;

                    case 3:

                        return {
                            'Họ và tên': item.full_name,
                            'Tên người dùng': item.qlcb_uid,
                            'Thời gian': this.formatDateToDDMMYYYY(item.time),
                            'Hình thức': item.form,
                            'Số quyết định': item.number_decision,
                            'Cơ quan quyết định': item.department_decision,
                            'Lý do': item.reason,
                        };
                        break;

                }
            });

            const workbook = XLSX.utils.book_new();
            const worksheet = XLSX.utils.json_to_sheet(data);

            const colWidths = Object.keys(data[0]).map(key => {
                return { wch: Math.max(...data.map(row => row[key] ? row[key].toString().length : 0), key.length) };
            });

            worksheet['!cols'] = colWidths;
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Users');

            const filename = 'KetQua.xlsx';
            XLSX.writeFile(workbook, filename);
        },
    }
}
</script>

<style scoped>
.buttons {
    display: flex;
    justify-content: flex-end;
    gap: 10px;

    h2 {
        text-align: center;
    }
}
</style>