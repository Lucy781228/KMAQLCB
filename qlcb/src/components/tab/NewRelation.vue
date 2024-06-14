<template>
    <NcModal v-if="modal" @close="closeModal" size="large" :canClose="false">
        <div class="modal__content">
            <div class="grid-item full-width">
                <h2 class="modal-title">{{ getTitle }}</h2>
            </div>

            <div class="grid-item">
                <label>Họ và tên (*)</label>
                <input id="full_name" type="text" v-model="fullName" @blur="handleFieldFocus('full_name')" />
                <div class="validation-error-container">
                    <span class="validation-error"
                        v-if="!isEdit && touchedFields.full_name && !validation.requiredString(fullName)">
                        Không được để trống
                    </span>
                    <span class="validation-error" v-if="isEdit && !validation.requiredString(fullName)">
                        Không được để trống
                    </span>
                </div>
            </div>

            <div class="grid-item">
                <label>Mối quan hệ (*)</label>
                <NcMultiselect ref="relationship" class="nc-picker" id="relationship" v-model="relationship"
                    :options="types" label="text" track-by="value" />
                <div class="validation-error-container">
                    <span class="validation-error"
                        v-if="touchedFields.relationship && !validation.requiredObject(relationship)">
                        Không được để trống
                    </span>
                </div>
            </div>

            <div class="grid-item">
                <label>Ngày sinh</label>
                <NcDatetimePicker v-if="isEdit" ref="date_of_birth" format="DD/MM/YYYY" class="nc-picker"
                    id="date_of_birth" v-model="formatDateOfBirth" />
                <NcDatetimePicker v-else ref="date_of_birth" format="DD/MM/YYYY" class="nc-picker" id="date_of_birth"
                    v-model="dateOfBirth" />
                <div class="validation-error-container">
                </div>
            </div>

            <div class="grid-item">
                <label>Số điện thoại</label>
                <input id="phone" type="text" v-model="phone" />
                <div class="validation-error-container">
                    <span class="validation-error" v-if="!isEdit && !validation.phone(phone)">
                        Gồm 10 số
                    </span>
                    <span class="validation-error" v-if="isEdit && !validation.phone(phone)">
                        Gồm 10 số
                    </span>
                </div>
            </div>

            <div class="grid-item address">
                <label>Địa chỉ</label>
                <input id="address" type="text" v-model="address" />
                <div class="validation-error-container">
                </div>
            </div>

            <div class="grid-item full-width">
                <div class="button_actions_container">
                    <div class="button_actions">
                        <NcButton type="secondary" @click="closeModal" class="button">
                            Hủy
                        </NcButton>
                        <NcButton v-if="isEdit" @click="updateRelation" type="primary" :disabled="!isFormValid"
                            class="button">
                            Cập nhật
                        </NcButton>
                        <NcButton v-else @click="createRelation" type="primary" :disabled="!isFormValid" class="button">
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
import { NcButton, NcModal, NcDatetimePicker, NcMultiselect } from "@nextcloud/vue";
import { showError, showSuccess } from '@nextcloud/dialogs'
import validation from '../../validate.js';
import Multiselect from "../Multiselect.vue";

export default {
    name: 'NewRelation',
    components: {
        NcButton,
        NcModal,
        NcDatetimePicker,
        Multiselect,
        NcMultiselect
    },
    props: {
        modal: {
            type: Boolean,
            required: true
        },
        relationId: {
            type: String,
            default: ""
        },
        qlcbUid: {
            type: String,
            required: true
        },
        fullName: {
            type: String,
            default: ""
        },
        dateOfBirth: {
            type: String,
            default: null
        },
        address: {
            type: String,
            default: ""
        },
        phone: {
            type: String,
            default: ""
        },
        relationship: {
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
                full_name: false,
                relationship: false
            },
            formatDateOfBirth: null,
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
            typesRelationship: []
        };
    },

    watch: {
        modal(newVal) {
            if (newVal) {
                this.$nextTick(() => {
                    this.attachBlurListener(this.$refs.date_of_birth, 'date_of_birth');
                    this.attachBlurListener(this.$refs.relationship, 'relationship');
                });
                this.formatDateOfBirth = this.dateOfBirth ? new Date(this.dateOfBirth + '') : null
                this.getTypes();
            }
        }
    },

    mounted() {
    },

    computed: {
        getTitle() {
            return this.isEdit ? 'CẬP NHẬT THÂN NHÂN' : 'THÊM THÂN NHÂN'
        },
        validation() {
            return validation;
        },

        isFormValid() {
            return this.validation.phone(this.phone) &&
                this.validation.requiredString(this.relationship);
        }
    },

    methods: {

        closeModal() {
            this.$emit('close');
            this.touchedFields.relationship = false,
                this.touchedFields.full_name = false,
                this.dateOfBirth = null,
                this.address = "",
                this.phone = "",
                this.relationship = "",
                this.fullName = ""
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

        async createRelation() {
            try {
                const response = await axios.post('/apps/qlcb/create_relation', {
                    full_name: this.fullName,
                    date_of_birth: this.mysqlDateFormatter(this.dateOfBirth),
                    address: this.address,
                    phone: this.phone,
                    relationship: this.relationship.value,
                    qlcb_uid: this.qlcbUid,
                });
                showSuccess(t('qlcb', 'Thêm thành công'));
                this.getTypes();
                this.closeModal()
            } catch (e) {
                console.error(e)
            }
        },

        async updateRelation() {
            try {
                const response = await axios.put('/apps/qlcb/update_relation', {
                    full_name: this.fullName,
                    date_of_birth: this.mysqlDateFormatter(this.formatDateOfBirth),
                    address: this.address,
                    phone: this.phone,
                    relationship: this.relationship.value,
                    qlcb_uid: this.qlcbUid,
                    relation_id: this.relationId
                });

                showSuccess(t('qlcb', 'Cập nhật thành công'));
                this.closeModal()
            } catch (e) {
                console.error(e)
            }
        },

        async getTypes() {
            try {
                const response = await axios.get(generateUrl(`/apps/qlcb/relation_types/${this.qlcbUid}`))
                this.typesRelationship = response.data.types
                let copyTypes = [...this.types];
                copyTypes = copyTypes.filter(type => {
                    const matchingType = this.typesRelationship.find(tr => tr.relationship == type.value);
                    if (matchingType && type.is_single) {
                        return false;
                    }
                    return true;
                });

                this.types = copyTypes;
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
    text-align: center;
}

.address {
    grid-column: 1 / -1;
}

::v-deep .multiselect__tags {
    border: 2px solid #949494 !important;
}

::v-deep .multiselect__tags:hover {
    border-color: #3287b5 !important;
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