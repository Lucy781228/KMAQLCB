<template>
    <div class="center-wrapper" v-if="!show">
        <div class="search-content">
            <label>Danh mục</label>
            <NcMultiselect class="nc-select" v-model="selectedOption" :options="options" label="text"
                track-by="value" />
            <div class="grid-container-first" v-if="selectedOption.value == 0">
                <div class="grid-item">
                    <label>Họ và tên</label>
                    <input type="text" v-model="userSearch.full_name" />
                </div>

                <div class="grid-item">
                    <label>Đơn vị</label>
                    <Multiselect v-model="userSearch.unit_id" :options-list="formatUnits" type="unit" :id="userSearch.unit_id"
                        :allow-delete="false" />
                </div>

                <div class="grid-item">
                    <label>Chức vụ</label>
                    <Multiselect v-model="userSearch.position_id" :options-list="formatPositions" type="position" :id="userSearch.position_id"
                        :allow-delete="false" />
                </div>

                <div class="grid-item">
                    <label>Ngày sinh từ</label>
                    <NcDatetimePicker format="DD/MM/YYYY" class="nc-picker" v-model="userSearch.start" />
                </div>

                <div class="grid-item">
                    <label>Đến ngày</label>
                    <NcDatetimePicker format="DD/MM/YYYY" class="nc-picker" v-model="userSearch.end" />
                </div>

                <div class="grid-item">
                    <label>Đảng viên</label>
                    <NcMultiselect class="nc-select" v-model="userSearch.is_community" :options="community" label="text"
                        track-by="value" />
                </div>
            </div>
            <div class="grid-container-second" v-else-if="selectedOption.value != -1">
                <div class="grid-item">
                    <label>Từ ngày</label>
                    <NcDatetimePicker format="DD/MM/YYYY" class="nc-picker" v-model="userSearch.start" />
                </div>

                <div class="grid-item">
                    <label>Đến ngày</label>
                    <NcDatetimePicker format="DD/MM/YYYY" class="nc-picker" v-model="userSearch.end" />
                </div>
            </div>
            <div class="combo-button" v-if="selectedOption.value != -1">
                <NcButton aria-label="Example text" type="secondary" @click="reset">
                    <template #icon>
                        <Restore :size="20" />
                    </template>
                    <template>Khôi phục</template>
                </NcButton>
                <NcButton aria-label="Example text" type="primary" @click="startSearch">
                    <template #icon>
                        <Magnify :size="20" />
                    </template>
                    <template>Tìm kiếm</template>
                </NcButton>
            </div>
        </div>
    </div>
    <Result v-else :show="show" :selected-option="selectedOption.value" :unit-id="userSearch.unit_id"
        :end="userSearch.end" :full-name="userSearch.full_name" :is-community="userSearch.is_community"
        :position-id="userSearch.position_id" :start="userSearch.start" @close="stopSearch" />
</template>

<script>
import VueGoodTablePlugin from 'vue-good-table';
import 'vue-good-table/dist/vue-good-table.css';
import axios from "@nextcloud/axios";
import { generateUrl } from '@nextcloud/router'
import { NcButton, NcModal, NcDatetimePicker, NcMultiselect } from "@nextcloud/vue";
import Multiselect from "./Multiselect.vue";
import Magnify from 'vue-material-design-icons/Magnify.vue';
import Restore from 'vue-material-design-icons/Restore'
import Result from "./Result.vue";

export default {
    name: 'Search',
    components: {
        NcMultiselect,
        Multiselect,
        Restore,
        Magnify,
        NcButton,
        NcDatetimePicker,
        Result
    },
    props: {
        reset: {
            type: Boolean,
            required: true
        },
    },
    data() {
        return {
            options: [
                { text: 'Cán bộ', value: 0 },
                { text: 'Quá trình công tác', value: 1 },
                { text: 'Quá trình đào tạo', value: 2 },
                { text: 'Quá trình khen thưởng', value: 3 },
                { text: 'Quá trình kỷ luật', value: 4 },
            ],
            community: [
                { text: 'Có', value: 0 },
                { text: 'Không', value: 1 }
            ],
            userSearch: {
                unit_id: "",
                position_id: "",
                full_name: "",
                start: "",
                end: "",
                is_community: ""
            },
            selectedOption: {
                value: -1
            },
            units: [],
            positions: [],
            isSearchUser: false,
            show: false
        }
    },
    computed: {
        formatUnits() {
            return this.units.map(unit => ({
                text: `${unit.unit_id} - ${unit.unit_name}`,
                value: unit.unit_id,
            }));
        },

        formatPositions() {
            return this.positions.map(position => ({
                text: `${position.position_id} - ${position.position_name}`,
                value: position.position_id,
            }));
        },

        isEqual() {
            return this.value == 0
        },

    },
    mounted() {
        this.getUnits()
        this.getPositions()
    },

    watch: {
        reset: {
            handler(newVal, oldVal) {
                if (newVal == true) {
                    this.stopSearch()
                    this.reset = false
                }
            },
            immediate: true
        }
    },

    methods: {
        startSearch() {
            this.show = true
        },

        stopSearch() {
            this.show = false
            this.reset()
            this.selectedOption = {
                value: -1
            }
        },

        reset() {
            this.userSearch.unit_id = ""
            this.userSearch.position_id = ""
            this.userSearch.end = ""
            this.userSearch.start = ""
            this.userSearch.full_name = ""
            this.userSearch.is_community = ""
        },

        async getUnits() {
            try {
                const response = await axios.get(generateUrl('apps/qlcb/units'))
                this.units = response.data.units
            } catch (e) {
                console.error(e)
            }
        },

        async getPositions() {
            try {
                const response = await axios.get(generateUrl('apps/qlcb/positions'))
                this.positions = response.data.positions
            } catch (e) {
                console.error(e)
            }
        },

        search() {
            console.log(this.value)
        }
    }
}
</script>

<style scoped>
.grid-container-first {
    height: 160px;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 16px;
    margin-top: 16px;
}

.grid-container-second {
    height: 80px;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 16px;
    margin-top: 16px;
}

.grid-item {
    display: flex;
    flex-direction: column;
}

.center-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 0;
    margin: 0;
}

.search-content {
    width: 800px;
    height: 400px
}

.combo-button {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

input {
    height: 44px !important;
    width: 100% !important
}

.multiselect {
    width: 100%;
}

.nc-select .multiselect__tags {
    border: 2px solid #949494 !important;
}

.nc-select .multiselect__tags:hover {
    border-color: #3287b5 !important;
}

::v-deep .nc-picker {
  width: 100% !important;
}

::v-deep .mx-input {
    height: 44px !important;
}

::v-deep .mx-datepicker {
    width: 100% !important;
}
</style>