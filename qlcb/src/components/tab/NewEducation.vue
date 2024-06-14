<template>
    <NcModal v-if="modal" @close="closeModal" size="large" :canClose="false">
        <div class="modal__content">
            <div class="grid-item full-width">
                <h2 class="modal-title">{{ getTitle }}</h2>
            </div>
            <div class="grid-item">
                <label>Ngày bắt đầu (*)</label>
                <NcDatetimePicker v-if="isEdit" ref="start_date" format="DD/MM/YYYY" class="nc-picker" id="start_date"
                    v-model="formatStartDate" />
                <NcDatetimePicker v-else ref="start_date" format="DD/MM/YYYY" class="nc-picker" id="start_date"
                    v-model="startDate" />
                <div class="validation-error-container">
                    <span class="validation-error"
                        v-if="!isEdit && touchedFields.start_date && !validation.requiredObject(startDate)">
                        Không được để trống
                    </span>
                    <span class="validation-error" v-if="isEdit && !validation.requiredObject(formatStartDate)">
                        Không được để trống
                    </span>
                </div>
            </div>

            <div class="grid-item">
                <label>Ngày kết thúc (*)</label>
                <NcDatetimePicker v-if="isEdit" ref="end_date" format="DD/MM/YYYY" class="nc-picker" id="end_date"
                    v-model="formatEndDate" />
                <NcDatetimePicker v-else ref="end_date" format="DD/MM/YYYY" class="nc-picker" id="end_date"
                    v-model="endDate" />
                <div class="validation-error-container">
                    <span class="validation-error"
                        v-if="!isEdit && touchedFields.end_date && !validation.requiredObject(endDate)">
                        Không được để trống
                    </span>
                    <span class="validation-error" v-if="isEdit && !validation.requiredObject(formatEndDate)">
                        Không được để trống
                    </span>
                    <span class="validation-error" v-if="!isValidDate">
                        Ngày kết thúc phải sau ngày bắt đầu
                    </span>
                </div>
            </div>

            <div class="grid-item">
                <label>Văn bằng (*)</label>
                <Multiselect id="diploma_id" v-model="diplomaId" :options-list="formatDiplomas" :id="diplomaId"
                    :allow-delete="isEdit" type="diploma" @blur="handleFieldFocus('diploma_id')" />
                <div class="validation-error-container">
                    <span class="validation-error"
                        v-if="!isEdit && touchedFields.diploma_id && !validation.requiredString(diplomaId)">
                        Không được để trống
                    </span>
                    <span class="validation-error" v-if="isEdit && !validation.requiredString(diplomaId)">
                        Không được để trống
                    </span>
                </div>
            </div>

            <div class="grid-item">
                <label>Chuyên ngành (*)</label>
                <input id="specialization" type="text" v-model="specialization"
                    @blur="handleFieldFocus('specialization')" />
                <div class="validation-error-container">
                    <span class="validation-error"
                        v-if="!isEdit && touchedFields.specialization && !validation.requiredString(specialization)">
                        Không được để trống
                    </span>
                    <span class="validation-error" v-if="isEdit && !validation.requiredString(specialization)">
                        Không được để trống
                    </span>
                </div>
            </div>

            <div class="grid-item">
                <label>Đơn vị (*)</label>
                <input id="training_unit" type="text" v-model="trainingUnit"
                    @blur="handleFieldFocus('training_unit')" />
                <div class="validation-error-container">
                    <span class="validation-error"
                        v-if="!isEdit && touchedFields.training_unit && !validation.requiredString(trainingUnit)">
                        Không được để trống
                    </span>
                    <span class="validation-error" v-if="isEdit && !validation.requiredString(trainingUnit)">
                        Không được để trống
                    </span>
                </div>
            </div>

            <div class="grid-item">
                <label>Kết quả (*)</label>
                <input id="result" type="text" v-model="result" @blur="handleFieldFocus('result')" />
                <div class="validation-error-container">
                    <span class="validation-error"
                        v-if="!isEdit && touchedFields.result && !validation.requiredString(result)">
                        Không được để trống
                    </span>
                    <span class="validation-error" v-if="isEdit && !validation.requiredString(result)">
                        Không được để trống
                    </span>
                </div>
            </div>

            <div class="grid-item full-width">
                <div class="button_actions_container">
                    <div class="button_actions">
                        <NcButton type="secondary" @click="closeModal" class="button">
                            Hủy
                        </NcButton>
                        <NcButton v-if="isEdit" @click="updateEducation" type="primary" :disabled="!isFormValid"
                            class="button">
                            Cập nhật
                        </NcButton>
                        <NcButton v-else @click="createEducation" type="primary" :disabled="!isFormValid"
                            class="button">
                            Thêm
                        </NcButton>
                    </div>
                </div>
            </div>
        </div>
    </NcModal>
</template>

<script>
import VueGoodTablePlugin from 'vue-good-table';
import 'vue-good-table/dist/vue-good-table.css';
import axios from "@nextcloud/axios";
import { generateUrl } from '@nextcloud/router'
import { NcButton, NcModal, NcDatetimePicker } from "@nextcloud/vue";
import { showError, showSuccess } from '@nextcloud/dialogs'
import validation from '../../validate.js';
import Multiselect from "../Multiselect.vue";

export default {
    name: 'NewEducation',
    components: {
        NcButton,
        NcModal,
        NcDatetimePicker,
        Multiselect
    },
    props: {
        modal: {
            type: Boolean,
            required: true
        },
        educationId: {
            type: String,
            default: ""
        },
        qlcbUid: {
            type: String,
            required: true
        },
        diplomaId: {
            type: String,
            default: ""
        },
        startDate: {
            type: String,
            default: null
        },
        endDate: {
            type: String,
            default: null
        },
        trainingUnit: {
            type: String,
            default: ""
        },
        specialization: {
            type: String,
            default: ""
        },
        result: {
            type: String,
            default: ""
        },
        isEdit: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            touchedFields: {
                diploma_id: false,
                start_date: false,
                end_date: false,
                training_unit: false,
                specialization: false,
                result: false,
            },
            diplomas: [],
            formatStartDate: null,
            formatEndDate: null,
            isValidDate: true
        };
    },

    watch: {
        modal(newVal) {
            if (newVal) {
                this.$nextTick(() => {
                    this.attachBlurListener(this.$refs.start_date, 'start_date');
                    this.attachBlurListener(this.$refs.end_date, 'end_date');
                });
                this.getDiplomas()
                this.formatEndDate = this.endDate ? new Date(this.endDate + '') : null
                this.formatStartDate = this.startDate ? new Date(this.startDate + '') : null
            }
        },

        endDate(newVal) {
            if (newVal) {
                if (this.startDate !== null) {
                    this.isValidDate = this.endDate > this.startDate
                }
            }
        },

        formatEndDate(newVal) {
            if (newVal) {
                if (this.formatStartDate !== null) {
                    this.isValidDate = this.formatEndDate > this.formatStartDate
                }
            }
        },

        startDate(newVal) {
            if (newVal) {
                if (this.endDate !== null) {
                    this.isValidDate = this.endDate > this.startDate
                }
            }
        },

        formatStartDate(newVal) {
            if (newVal) {
                if (this.formatEndDate !== null) {
                    this.isValidDate = this.formatEndDate > this.formatStartDate
                }
            }
        }
    },

    mounted() {
    },

    computed: {
        getTitle() {
            return this.isEdit ? 'CẬP NHẬT QUÁ TRÌNH ĐÀO TẠO' : 'THÊM QUÁ TRÌNH ĐÀO TẠO'
        },
        validation() {
            return validation;
        },

        formatDiplomas() {
            return this.diplomas.map(diploma => ({
                text: `${diploma.diploma_id} - ${diploma.diploma_name}`,
                value: diploma.diploma_id,
            }));
        },

        getDiplomaName() {
            const matchingDiploma = this.diplomas.find(diploma => diploma.diploma_id === this.diplomaId);
            return matchingDiploma ? matchingDiploma.diploma_name : '';
        },

        isFormValid() {
            return this.isValidDate &&
                this.validation.requiredObject(this.isEdit ? this.formatStartDate : this.startDate) &&
                this.validation.requiredObject(this.isEdit ? this.formatEndDate : this.endDate) &&
                this.validation.requiredString(this.diplomaId) &&
                this.validation.requiredString(this.specialization) &&
                this.validation.requiredString(this.trainingUnit) &&
                this.validation.requiredString(this.result);
        }
    },

    methods: {

        closeModal() {
            this.$emit('close');
            this.touchedFields.diploma_id = false,
                this.touchedFields.end_date = false,
                this.touchedFields.start_date = false,
                this.touchedFields.result = false,
                this.touchedFields.specialization = false,
                this.touchedFields.training_unit = false,
                this.isValidDate = true,
                this.startDate = null,
                this.endDate = null,
                this.trainingUnit = "",
                this.specialization = "",
                this.result = "",
                this.diplomaId = ""
        },

        handleFieldFocus(fieldName) {
            this.touchedFields[fieldName] = true;
        },

        attachBlurListener(componentRef, fieldName) {
            if (componentRef && componentRef.$el) {
                const input = componentRef.$el.querySelector('input');
                if (input) {
                    input.addEventListener('blur', () => {
                        this.handleFieldFocus(fieldName);
                    });
                }
            }
        },

        async getDiplomas() {
            try {
                const response = await axios.get(generateUrl('apps/qlcb/diplomas'))
                this.diplomas = response.data.diplomas
            } catch (e) {
                console.error(e)
            }
        },

        mysqlDateFormatter(date) {
            if (!date) return '';
            const year = date.getFullYear();
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            return `${year}-${month}-${day}`;
        },

        async createEducation() {
            try {
                const response = await axios.post('/apps/qlcb/create_education', {
                    diploma_id: this.diplomaId,
                    start_date: this.mysqlDateFormatter(this.startDate),
                    end_date: this.mysqlDateFormatter(this.endDate),
                    training_unit: this.trainingUnit,
                    specialization: this.specialization,
                    result: this.result,
                    qlcb_uid: this.qlcbUid,
                });
                console.log(this.diplomaId)
                showSuccess(t('qlcb', 'Thêm thành công'));
                this.closeModal()
            } catch (e) {
                console.error(e)
            }
        },

        async updateEducation() {
            try {
                const response = await axios.put('/apps/qlcb/update_education', {
                    diploma_id: this.diplomaId,
                    start_date: this.mysqlDateFormatter(this.formatStartDate),
                    end_date: this.mysqlDateFormatter(this.formatEndDate),
                    training_unit: this.trainingUnit,
                    specialization: this.specialization,
                    result: this.result,
                    qlcb_uid: this.qlcbUid,
                    education_id: this.educationId
                });
                showSuccess(t('qlcb', 'Cập nhật thành công'));
                this.closeModal()
            } catch (e) {
                console.log('Before conversion:', this.educationId, typeof this.educationId);
                console.log('After conversion:', education_id, typeof +this.educationId);
                console.log(this.diplomaId)
                console.error(e)
            }
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
    margin: 50px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
}

.validation-error-container {
    height: 10px;
}

.validation-error {
    color: red;
    font-size: 0.8em;
}

.grid-item input,
.grid-item .nc-datetime-picker,
.grid-item .nc-multiselect {
    width: 100%;
}

::v-deep .nc-picker {
    width: 100% !important;
}

input {
    height: 44px !important
}

::v-deep .mx-input {
    height: 44px !important;
}

.full-width {
    grid-column: 1 / -1;
    text-align: center;
}

.button_actions_container {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
}

.button_actions {
    display: flex;
    gap: 40px;
}

.button {
    width: 150px;
}
</style>