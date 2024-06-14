<template>
    <div class="custom-select-container" v-click-outside="closeDropdown">
        <div class="select-display" @click="toggleDropdown">
            {{ computedSelectedOption ? computedSelectedOption.text : 'Select option' }}
        </div>
        <div class="dropdown-menu" v-if="showDropdown">
            <div class="dropdown-item" @click.stop="openModalAdd">
                <div class="icon-container">
                    <Plus class="icon-inline" :size="20" />Add a new option
                </div>
            </div>
            <div v-for="(option, index) in optionsList" :key="option.value" class="dropdown-item"
                @click="selectOption(option)">
                {{ option.text }}
                <Close v-if="allowDelete" class="icon-right" @click.stop="openModalDelete(option)" :size="20" />
            </div>
        </div>
        <NcModal :show="deleteObject" @close="closeModal" style="z-index: 11000;" :canClose="false">
            <div class="modal__content_delete">
                <h3>Bạn chắc chắn không?</h3>
                <div class="modal__actions_delete">
                    <NcButton @click="closeModal" type="primary">
                        Hủy
                    </NcButton>
                    <NcButton @click="onDeleteOption" type="secondary">
                        Xóa
                    </NcButton>
                </div>
            </div>
        </NcModal>
        <NcModal :show="addUnit" @close="closeModal" style="z-index: 11000;" :canClose="false">
            <div class="modal__content">
                <h2>THÊM ĐƠN VỊ</h2>
                <NcTextField label="Mã đơn vị" :value.sync="newValue.id" />
                <NcTextField label="Tên đơn vị" :value.sync="newValue.name" />
                <div class="modal__actions">
                    <NcButton :wide="true" @click="closeModal" type="secondary">
                        Hủy
                    </NcButton>
                    <NcButton :wide="true" :disabled="!this.newValue.id || !this.newValue.name" @click="onAddNew"
                        type="primary">
                        Thêm
                    </NcButton>
                </div>
            </div>
        </NcModal>
        <NcModal :show="addPosition" @close="closeModal" style="z-index: 11000;" :canClose="false">
            <div class="modal__content">
                <h2>THÊM CHỨC VỤ</h2>
                <NcTextField label="Mã chức vụ" :value.sync="newValue.id" />
                <NcTextField label="Tên chức vụ" :value.sync="newValue.name" />
                <div class="modal__actions">
                    <NcButton :wide="true" @click="closeModal" type="secondary">
                        Hủy
                    </NcButton>
                    <NcButton :wide="true" :disabled="!this.newValue.id || !this.newValue.name" @click="onAddNew"
                        type="primary">
                        Thêm
                    </NcButton>
                </div>
            </div>
        </NcModal>
        <NcModal :show="addDiploma" @close="closeModal" style="z-index: 11000;" :canClose="false">
            <div class="modal__content">
                <h2>THÊM VĂN BẰNG</h2>
                <NcTextField label="Mã văn bằng" :value.sync="newValue.id" />
                <NcTextField label="Tên văn bằng" :value.sync="newValue.name" />
                <div class="modal__actions">
                    <NcButton :wide="true" @click="closeModal" type="secondary">
                        Hủy
                    </NcButton>
                    <NcButton :wide="true" :disabled="!this.newValue.id || !this.newValue.name" @click="onAddNew"
                        type="primary">
                        Thêm
                    </NcButton>
                </div>
            </div>
        </NcModal>
    </div>
</template>

<script>
import Plus from 'vue-material-design-icons/Plus.vue';
import Close from 'vue-material-design-icons/Close.vue';
import { generateUrl } from '@nextcloud/router'
import axios from "@nextcloud/axios";
import { showError, showSuccess } from '@nextcloud/dialogs'
import { NcButton, NcModal, NcTextField } from "@nextcloud/vue";

export default {
    name: 'Multiselect',
    components: {
        Plus,
        Close,
        NcModal,
        NcButton,
        NcTextField
    },
    props: {
        optionsList: {
            type: Array,
            required: true
        },
        type: {
            type: String,
            required: true
        },
        id: {
            type: String,
            default: ''
        },
        allowDelete: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            showDropdown: false,
            deleteOption: null,
            deleteObject: false,
            addUnit: false,
            addPosition: false,
            addDiploma: false,
            newValue: {
                id: "",
                name: ""
            }
        };
    },
    // mounted() {
    //     this.$nextTick(() => {
    //     if (this.id !== '') {
    //         const matchingOption = this.optionsList.find(option => option.value === this.id);
    //         this.computedSelectedOption = matchingOption;
    //     } else {
    //         this.computedSelectedOption = null;
    //     }
    // });
    // },

    computed: {
    computedSelectedOption() {
        if (this.id !== '') {
            return this.optionsList.find(option => option.value === this.id);
        }
        return null;
    }
},

    methods: {
        openModalDelete(option) {
            this.deleteOption = option
            this.deleteObject = true
        },

        openModalAdd() {
            if (this.type === 'unit') this.addUnit = true
            else if (this.type === 'position') this.addPosition = true
            else this.addDiploma = true
        },

        closeModal() {
            this.deleteObject = false
            this.addPosition = false
            this.addUnit = false
            this.addDiploma = false
            this.newValue.id = ""
            this.newValue.name = ""
        },

        closeDropdown() {
            this.showDropdown = false
        },

        toggleDropdown() {
            this.showDropdown = !this.showDropdown;
            if (this.showDropdown)
                this.$emit('blur');
        },
        selectOption(option) {
            this.computedSelectedOption = option;
            this.showDropdown = false;
            this.$emit('input', option.value);
        },
        onAddNew() {
            this.addUnit = false
            this.addPosition = false
            this.addDiploma = false
            if (this.type === 'unit') this.createUnit()
            else if (this.type === 'position') this.createPosition()
            else this.createDiploma()
            this.optionsList.push({
                text: `${this.newValue.id} - ${this.newValue.name}`,
                value: this.newValue.id
            });
            showSuccess(t('qlcb', 'Thêm thành công'));
        },
        onDeleteOption() {
            this.deleteObject = false
            if (this.type === 'unit') this.deleteUnit(this.deleteOption.value)
            else if (this.type === 'diploma') this.deleteDiploma(this.deleteOption.value)
            else this.deletePosition(this.deleteOption.value)
            const index = this.optionsList.indexOf(this.deleteOption);
            if (index > -1) {
                let newList = [...this.optionsList];
                newList.splice(index, 1);
                this.optionsList = newList;
            }
            showSuccess(t('qlcb', 'Xóa thành công'));
        },
        async deleteUnit(id) {
            try {
                const response = await axios.delete(generateUrl('apps/qlcb/delete_unit/' + id))
            } catch (e) {
                console.error(e)
            }
        },
        async deletePosition(id) {
            try {
                const response = await axios.delete(generateUrl('apps/qlcb/delete_position/' + id))
            } catch (e) {
                console.error(e)
            }
        },
        async deleteDiploma(id) {
            try {
                const response = await axios.delete(generateUrl('apps/qlcb/delete_diploma/' + id))
            } catch (e) {
                console.error(e)
            }
        },

        async createUnit() {
            try {
                const response = await axios.post('/apps/qlcb/create_unit', {
                    unit_id: this.newValue.id,
                    unit_name: this.newValue.name,
                });
            } catch (e) {
                console.error(e)
            }
        },

        async createPosition() {
            try {
                const response = await axios.post('/apps/qlcb/create_position', {
                    position_id: this.newValue.id,
                    position_name: this.newValue.name,
                });
            } catch (e) {
                console.error(e)
            }
        },

        async createDiploma() {
            try {
                const response = await axios.post('/apps/qlcb/create_diploma', {
                    diploma_id: this.newValue.id,
                    diploma_name: this.newValue.name,
                });
            } catch (e) {
                console.error(e)
            }
        },
    },
};
</script>

<style scoped>
.custom-select-container {
    position: relative;
    display: inline-block;
    width: 100%;
    user-select: none;
    border: 2px solid #949494;
    border-radius: 10px;
    height: 44px
}

.custom-select-container:hover {
    border-color: #3287b5;
}

.select-display {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    padding: 0 10px;
    height: 100%
}

.dropdown-menu {
    position: absolute;
    width: 100%;
    border: 1px solid #ccc;
    z-index: 100;
    background-color: #fff;
    box-sizing: border-box;
}

.dropdown-item {
    padding: 10px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background-color 0.2s;
}

.dropdown-item:hover {
    background-color: #f2f2f2;
}

.icon-container {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    cursor: pointer;
    padding-right: 8px;
}

.icon-inline {
    padding-right: 5px;
    cursor: pointer;
}

.icon-right {
    margin-left: auto;
    padding: 5px;
    border-radius: 50%;
    transition: background-color 0.2s, border 0.2s;
}

.icon-right:hover {
    background-color: #e6e6e6;
    cursor: pointer;
}

.modal__content {
    margin: 50px;
    text-align: center;
}

.modal__actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-top: 20px;
}

.modal__content_delete {
    margin: 20px;
    text-align: center;
}

.modal__actions_delete {
    display: flex;
    justify-content: flex-end;
    gap: 20px;
    margin-top: 20px;
}

.input-field {
    margin: 12px 0px;
}
</style>