<template>
    <NcModal v-if="modal" @close="closeModal" size="large" :canClose="false">
        <div class="modal__content">
            <div class="grid-item title">
                <h2 class="modal-title">{{ getTitle }}</h2>
            </div>
            <div class="grid-item">
                <label>Thời gian (*)</label>
                <NcDatetimePicker v-if="isEdit" ref="time" format="DD/MM/YYYY" class="nc-picker" id="time"
                    v-model="formatTime" />
                <NcDatetimePicker v-else ref="time" format="DD/MM/YYYY" class="nc-picker" id="time" v-model="time" />
                <div class="validation-error-container">
                    <span class="validation-error"
                        v-if="!isEdit && touchedFields.time && !validation.requiredObject(time)">
                        Không được để trống
                    </span>
                    <span class="validation-error" v-if="isEdit && !validation.requiredObject(formatTime)">
                        Không được để trống
                    </span>
                </div>
            </div>

            <div class="grid-item">
                <label>Hình thức (*)</label>
                <input id="form" type="text" v-model="form" @blur="handleFieldFocus('form')" />
                <div class="validation-error-container">
                    <span class="validation-error"
                        v-if="!isEdit && touchedFields.form && !validation.requiredString(form)">
                        Không được để trống
                    </span>
                    <span class="validation-error" v-if="isEdit && !validation.requiredString(form)">
                        Không được để trống
                    </span>
                </div>
            </div>

            <div class="grid-item">
                <label>Số quyết định (*)</label>
                <input id="number_decision" type="text" v-model="numberDecision"
                    @blur="handleFieldFocus('number_decision')" />
                <div class="validation-error-container">
                    <span class="validation-error"
                        v-if="!isEdit && touchedFields.number_decision && !validation.requiredString(numberDecision)">
                        Không được để trống
                    </span>
                    <span class="validation-error" v-if="isEdit && !validation.requiredString(numberDecision)">
                        Không được để trống
                    </span>
                </div>
            </div>

            <div class="grid-item">
                <label>Cơ quan quyết định (*)</label>
                <input id="department_decision" type="text" v-model="departmentDecision"
                    @blur="handleFieldFocus('department_decision')" />
                <div class="validation-error-container">
                    <span class="validation-error"
                        v-if="!isEdit && touchedFields.department_decision && !validation.requiredString(departmentDecision)">
                        Không được để trống
                    </span>
                    <span class="validation-error" v-if="isEdit && !validation.requiredString(departmentDecision)">
                        Không được để trống
                    </span>
                </div>
            </div>

            <div class="grid-item full-width">
                <label>Lý do (*)</label>
                <input id="reason" type="text" v-model="reason" @blur="handleFieldFocus('reason')" />
                <div class="validation-error-container">
                    <span class="validation-error"
                        v-if="!isEdit && touchedFields.reason && !validation.requiredString(reason)">
                        Không được để trống
                    </span>
                    <span class="validation-error" v-if="isEdit && !validation.requiredString(reason)">
                        Không được để trống
                    </span>
                </div>
            </div>

            <div class="grid-item full-width">
                <div class="button_actions_container">
                    <div class="button_actions">
                        <NcButton type="secondary" class="button" @click="closeModal">
                            Hủy
                        </NcButton>
                        <NcButton v-if="isEdit" @click="updateBonus" type="primary" class="button"
                            :disabled="!isFormValid">
                            Cập nhật
                        </NcButton>
                        <NcButton v-else @click="createBonus" type="primary" :disabled="!isFormValid" class="button">
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
        bonusType: {
            type: Boolean,
            required: true
        },
        modal: {
            type: Boolean,
            required: true
        },
        bonusId: {
            type: String,
            default: ""
        },
        qlcbUid: {
            type: String,
            required: true
        },
        time: {
            type: String,
            default: null
        },
        numberDecision: {
            type: String,
            default: ""
        },
        form: {
            type: String,
            default: ""
        },
        departmentDecision: {
            type: String,
            default: ""
        },
        reason: {
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
                time: false,
                reason: false,
                number_decision: false,
                form: false,
                department_decision: false,
            },
            formatTime: null
        };
    },

    watch: {
        modal(newVal) {
            if (newVal) {
                this.$nextTick(() => {
                    this.attachBlurListener(this.$refs.time, 'time');
                });
                this.formatTime = this.time ? new Date(this.time + '') : null
            }
        }
    },

    mounted() {
    },

    computed: {
        getTitle() {
            if (this.isEdit) {
                if (this.bonusType) return 'CẬP NHẬT QUÁ TRÌNH KHEN THƯỞNG';
                else return 'CẬP NHẬT QUÁ TRÌNH KỶ LUẬT';
            }
            else {
                if (this.bonusType) return 'THÊM QUÁ TRÌNH KHEN THƯỞNG';
                else return 'THÊM QUÁ TRÌNH KỶ LUẬT';
            }
        },
        validation() {
            return validation;
        },

        isFormValid() {
            return this.validation.requiredObject(this.isEdit ? this.formatTime : this.time) &&
                this.validation.requiredString(this.reason) &&
                this.validation.requiredString(this.form) &&
                this.validation.requiredString(this.numberDecision) &&
                this.validation.requiredString(this.departmentDecision);
        }
    },

    methods: {

        closeModal() {
            this.$emit('close');
            this.touchedFields.time = false,
                this.touchedFields.department_decision = false,
                this.touchedFields.form = false,
                this.touchedFields.number_decision = false,
                this.touchedFields.reason = false,
                this.time = null,
                this.numberDecision = "",
                this.form = "",
                this.departmentDecision = "",
                this.reason = ""
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

        mysqlDateFormatter(date) {
            if (!date) return '';
            const year = date.getFullYear();
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            return `${year}-${month}-${day}`;
        },

        async createBonus() {
            try {
                const response = await axios.post('/apps/qlcb/create_bonus', {
                    time: this.mysqlDateFormatter(this.time),
                    number_decision: this.numberDecision,
                    form: this.form,
                    department_decision: this.departmentDecision,
                    reason: this.reason,
                    qlcb_uid: this.qlcbUid,
                    type: this.bonusType ? 1 : 0
                });
                showSuccess(t('qlcb', 'Thêm thành công'));
                this.closeModal()
            } catch (e) {
                console.error(e)
            }
        },

        async updateBonus() {
            try {
                const response = await axios.put('/apps/qlcb/update_bonus', {
                    time: this.mysqlDateFormatter(this.time),
                    number_decision: this.numberDecision,
                    form: this.form,
                    department_decision: this.departmentDecision,
                    reason: this.reason,
                    qlcb_uid: this.qlcbUid,
                    type: this.bonusType ? 1 : 0,
                    bonus_id: this.bonusId
                });
                showSuccess(t('qlcb', 'Cập nhật thành công'));
                this.closeModal()
            } catch (e) {
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
}

.title {
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