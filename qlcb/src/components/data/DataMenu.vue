<template>
    <div class="wrapper">
        <div class="menu">
            <NcMultiselect class="nc-select" v-model="selectedOption" :options="options" label="text" id="option"
                ref="option" placeholder="Chọn một tùy chọn" track-by="value" />
            Tham gia từ
            <NcDatetimePicker format="DD/MM/YYYY" class="nc-picker" :clearable="true" placeholder="Chọn một ngày" :disabled="!selectedOption"
                v-model="startDate" />
            đến
            <NcDatetimePicker format="DD/MM/YYYY" class="nc-picker" :clearable="true" placeholder="Chọn một ngày" :disabled="!selectedOption"
                v-model="endDate" />
            <NcButton aria-label="Example text" type="primary" @click="showChart" :disabled="!selectedOption">
                Áp dụng
            </NcButton>
        </div>
        <div class="content">
            <UserChart v-if="selectedOption.value == 0" :start-date="mysqlDateFormatter(start)" :end-date="mysqlDateFormatter(end)" />
        </div>
        <div class="content">
            <EducationBusinessChart v-if="selectedOption.value == 1" :start-date="mysqlDateFormatter(start)" :end-date="mysqlDateFormatter(end)" />
        </div>
    </div>
</template>

<script>
import axios from "@nextcloud/axios";
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess } from '@nextcloud/dialogs'
import { NcButton, NcDatetimePicker, NcMultiselect } from "@nextcloud/vue";
import UserChart from './UserChart.vue'
import { options } from "linkifyjs";
import EducationBusinessChart from "./EducationBusinessChart.vue";

export default {
    name: 'DataMenu',
    components: {
        NcButton,
        NcDatetimePicker,
        NcMultiselect,
        UserChart,
        EducationBusinessChart
    },
    data() {
        return {
            start: null,
            end: null,
            selectedOption: { text: 'Cán bộ', value: 0 },
            startDate: null,
            endDate: null,
            options: [
                { text: 'Cán bộ', value: 0 },
                { text: 'Công tác - Đào tạo', value: 1 },
                { text: 'Khen thưởng - Kỷ luật', value: 2 },
            ],
        }
    },
    methods: {
        showChart() {
            this.start = this.startDate
            this.end = this.endDate
        },

        mysqlDateFormatter(date) {
            if (!date) return '';
            const year = date.getFullYear();
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            return `${year}-${month}-${day}`;
        },
    },
}
</script>

<style scoped>
.wrapper {
    padding-left: 30px;
    padding-bottom: 10px;
    padding-right: 30px;
    width: 100%;
    height: 100%
}

::v-deep .nc-picker {
    width: 150px !important;
}

.menu {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 10px;
    margin-bottom: 10px
}

.menu NcButton {
    display: inline-block;
}

.menu NcButton:hover {
    cursor: pointer;
}

.nc-select {
    width: 100px
}
</style>