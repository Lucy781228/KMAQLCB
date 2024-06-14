<template>
  <div class="content">
    <div class="grid-container">
      <div class="grid-item">
        <h2>THÔNG TIN CƠ QUAN</h2>
      </div>
      <div class="grid-item">
      </div>

      <div class="grid-item">
        <h2>THÔNG TIN CÁ NHÂN</h2>
      </div>
      <div class="grid-item">
      </div>

      <div class="grid-item">
        <label>Tên người dùng (*)</label>
        <NcMultiselect ref="qlcb_uid" class="nc-select" v-model="user.qlcb_uid" id="qlcb_uid" :options="formatUsers" placeholder="Chọn một tùy chọn"
          label="userId" track-by="userId" :user-select="true">
          <template #singleLabel="{ option }">
            <NcListItemIcon v-bind="option" :title="option.userId" :avatar-size="24" :no-margin="true" />
          </template>
        </NcMultiselect>
        <div class="validation-error-container">
          <span class="validation-error" v-if="touchedFields.qlcb_uid && !validation.requiredObject(user.qlcb_uid)">
            {{ validationMessages['required'] }}
          </span>
        </div>
      </div>

      <div class="grid-item">
        <label>Ngày tham gia (*)</label>
        <NcDatetimePicker ref="day_joined" format="DD/MM/YYYY" class="nc-picker" id="day_joined" :clearable="true" placeholder="Chọn một ngày"
          v-model="user.day_joined" />
        <div class="validation-error-container">
          <span class="validation-error" v-if="touchedFields.day_joined && !validation.requiredObject(user.day_joined)">
            {{ validationMessages['required'] }}
          </span>
          <span class="validation-error" v-else-if="touchedFields.date_of_birth && !isDayJoinedValid">
            {{ validationMessages['day_joined'] }}
          </span>
        </div>
      </div>

      <div class="grid-item">
        <label>Họ và tên (*)</label>
        <input id="full_name" type="text" v-model="user.full_name" @blur="handleFieldFocus('full_name')" />
        <div class="validation-error-container">
          <span class="validation-error" v-if="touchedFields.full_name && !validation.requiredString(user.full_name)">
            {{ validationMessages['required'] }}
          </span>
        </div>
      </div>

      <div class="grid-item">
        <label>Ngày sinh (*)</label>
        <NcDatetimePicker ref="date_of_birth" format="DD/MM/YYYY" class="nc-picker" id="date_of_birth" :clearable="true" placeholder="Chọn một ngày"
          v-model="user.date_of_birth" />
        <div class="validation-error-container">
          <span class="validation-error"
            v-if="touchedFields.date_of_birth && !validation.requiredObject(user.date_of_birth)">
            {{ validationMessages['required'] }}
          </span>
          <span class="validation-error"
            v-else-if="touchedFields.date_of_birth && !isDateOfBirthValid">
            {{ validationMessages['date_of_birth'] }}
          </span>
        </div>
      </div>

      <div class="grid-item">
        <label>Chức vụ (*)</label>
        <Multiselect ref="position_id" id="position_id" v-model="user.position_id" :options-list="formatPositions"
          :id="user.position_id" @blur="handleFieldFocus('position_id')" type="position" />
        <div class="validation-error-container">
          <span class="validation-error"
            v-if="touchedFields.position_id && !validation.requiredString(user.position_id)">
            {{ validationMessages['required'] }}
          </span>
        </div>
      </div>

      <div class="grid-item">
        <label>Đơn vị (*)</label>
        <Multiselect ref="unit_id" id="unit_id" v-model="user.unit_id" :options-list="formatUnits" type="unit"
          :id="user.unit_id" @blur="handleFieldFocus('unit_id')" />
        <div class="validation-error-container">
          <span class="validation-error" v-if="touchedFields.unit_id && !validation.requiredString(user.unit_id)">
            {{ validationMessages['required'] }}
          </span>
        </div>
      </div>

      <div class="grid-item">
        <label>Giới tính (*)</label>
        <NcMultiselect ref="gender" class="nc-select" id="gender" v-model="user.gender" :options="genders" label="text" placeholder="Chọn một tùy chọn"
          track-by="value" />
        <div class="validation-error-container">
          <span class="validation-error" v-if="touchedFields.gender && !validation.requiredObject(user.gender)">
            {{ validationMessages['required'] }}
          </span>
        </div>
      </div>

      <div class="grid-item">
        <label>Số điện thoại (*)</label>
        <input id="phone" type="text" v-model="user.phone" @blur="handleFieldFocus('phone')" />
        <div class="validation-error-container">
          <span class="validation-error" v-if="touchedFields.phone && !validation.requiredString(user.phone)">
            {{ validationMessages['required'] }}
          </span>
          <span class="validation-error" v-if="!validation.phone(user.phone)">
            {{ validationMessages['phone'] }}
          </span>
        </div>
      </div>

      <div class="grid-item">
        <label>Hệ số lương (*)</label>
        <input id="coefficients_salary" type="text" v-model="user.coefficients_salary"
          @blur="handleFieldFocus('coefficients_salary')" />
        <div class="validation-error-container">
          <span class="validation-error"
            v-if="touchedFields.coefficients_salary && !validation.requiredString(user.coefficients_salary)">
            {{ validationMessages['required'] }}
          </span>
          <span class="validation-error" v-if="!validation.decimal(user.coefficients_salary)">
            {{ validationMessages['decimal'] }}
          </span>
        </div>
      </div>

      <div class="grid-item">
        <label>Bậc thuế (*)</label>
        <input id="tax" type="text" v-model="user.tax" @blur="handleFieldFocus('tax')" />
        <div class="validation-error-container">
          <span class="validation-error" v-if="touchedFields.tax && !validation.requiredString(user.tax)">
            {{ validationMessages['required'] }}
          </span>
          <span class="validation-error" v-if="!validation.number(user.tax)">
            {{ validationMessages['number'] }}
          </span>
        </div>
      </div>

      <div class="grid-item">
        <label>Email (*)</label>
        <input id="email" type="text" v-model="user.email" @blur="handleFieldFocus('email')" />
        <div class="validation-error-container">
          <span class="validation-error" v-if="touchedFields.email && !validation.requiredString(user.email)">
            {{ validationMessages['required'] }}
          </span>
          <span class="validation-error" v-if="!validation.email(user.email)">
            {{ validationMessages['email'] }}
          </span>
        </div>
      </div>

      <div class="grid-item">
        <label>CCCD/CMND (*)</label>
        <input id="id_number" type="text" v-model="user.id_number" @blur="handleFieldFocus('id_number')" />
        <div class="validation-error-container">
          <span class="validation-error" v-if="touchedFields.id_number && !validation.requiredString(user.id_number)">
            {{ validationMessages['required'] }}
          </span>
          <span class="validation-error" v-if="!validation.idNumber(user.id_number)">
            {{ validationMessages['id_number'] }}
          </span>
        </div>
      </div>

      <div class="grid-item">
      </div>

      <div class="grid-item">
      </div>

      <div class="grid-item">
        <label>Địa chỉ (*)</label>
        <input id="address" type="text" v-model="user.address" @blur="handleFieldFocus('address')" />
        <div class="validation-error-container">
          <span class="validation-error" v-if="touchedFields.address && !validation.requiredString(user.address)">
            {{ validationMessages['required'] }}
          </span>
        </div>
      </div>

      <div class="grid-item">
        <label>Ngày vào Đảng</label>
        <NcDatetimePicker format="DD/MM/YYYY" class="nc-picker" id="communist_party_joined" :clearable="true" placeholder="Chọn một ngày"
          v-model="user.communist_party_joined" />
        <div class="validation-error-container">
          <span class="validation-error" v-if="!isCommunistPartyJoinedValid">
            {{ validationMessages['communist_party_joined'] }}
          </span>
        </div>
      </div>

      <div class="grid-item full-width">
        <div class="button_actions_container">
          <div class="button_actions">
            <NcButton to="/files" type="secondary" class="button">
              Hủy
            </NcButton>
            <NcButton @click="createUser" type="primary" class="button" :disabled="!isFormValid">
              Thêm
            </NcButton>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import { NcMultiselect, NcDatetimePicker, NcListItemIcon, NcButton } from "@nextcloud/vue";
import { generateUrl } from '@nextcloud/router'
import axios from "@nextcloud/axios";
import Multiselect from "./Multiselect.vue";
import { showError, showSuccess } from '@nextcloud/dialogs'
import validation from '../validate.js';

export default {
  name: 'NewUser',
  components: {
    NcDatetimePicker,
    NcMultiselect,
    Multiselect,
    NcListItemIcon,
    NcButton
  },
  data() {
    const today = new Date();
    const minDate = new Date(today.getFullYear() - 60, today.getMonth(), today.getDate());
    const maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
    return {
      touchedFields: {
        phone: false,
        qlcb_uid: false,
        full_name: false,
        date_of_birth: false,
        gender: false,
        address: false,
        id_number: false,
        email: false,
        position_id: false,
        coefficients_salary: false,
        tax: false,
        unit_id: false,
        day_joined: false
      },
      user: {
        qlcb_uid: null,
        full_name: "",
        date_of_birth: null,
        gender: null,
        phone: "",
        address: "",
        id_number: "",
        email: "",
        position_id: "",
        coefficients_salary: "",
        tax: "",
        communist_party_joined: null,
        unit_id: "",
        day_joined: null
      },
      units: [],
      positions: [],
      nc_users: [],
      genders: [
        { text: 'Nam', value: 0 },
        { text: 'Nữ', value: 1 }
      ],
      validationMessages: {
        'required': 'Không được để trống',
        'email': 'Phải là email',
        'phone': 'Gồm 10 số',
        'id_number': 'Gồm 9 hoặc 12 số',
        'full_name': 'Không chứa số hay ký tự đặc biệt',
        'number': 'Phải là số tự nhiên',
        'decimal': 'Phải là số',
        'date_of_birth': `Từ ${this.formatDateToDDMMYYYY(minDate)} đến ${this.formatDateToDDMMYYYY(maxDate)}`,
        'day_joined': 'Phải sau ngày sinh',
        'communist_party_joined': 'Phải sau ngày sinh'
      },
      minDate: minDate,
      maxDate: maxDate,
    };
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

    formatUsers() {
      const usersArray = Object.values(this.nc_users);
      return usersArray.map(user => {
        return {
          userId: user.user_id,
          subtitle: user.display_name,
          icon: 'icon-user'
        };
      });
    },
    validation() {
      return validation;
    },
    isFormValid() {
      return (
        this.validation.requiredObject(this.user.qlcb_uid) &&
        this.validation.requiredObject(this.user.day_joined) &&
        this.validation.requiredString(this.user.full_name) &&
        this.validation.requiredObject(this.user.date_of_birth) &&
        this.isDateOfBirthValid &&
        this.validation.requiredString(this.user.position_id) &&
        this.validation.requiredString(this.user.unit_id) &&
        this.validation.requiredObject(this.user.gender) &&
        this.validation.requiredString(this.user.phone) &&
        this.validation.phone(this.user.phone) &&
        this.validation.requiredString(this.user.coefficients_salary) &&
        this.validation.decimal(this.user.coefficients_salary) &&
        this.validation.requiredString(this.user.tax) &&
        this.validation.number(this.user.tax) &&
        this.validation.requiredString(this.user.email) &&
        this.validation.email(this.user.email) &&
        this.validation.requiredString(this.user.id_number) &&
        this.validation.idNumber(this.user.id_number) &&
        this.validation.requiredString(this.user.address) &&
        this.isDayJoinedValid &&
        this.isCommunistPartyJoinedValid
      );
    },

    isDateOfBirthValid() {
    if (!this.user.date_of_birth) return false;
    const dateObj = new Date(this.user.date_of_birth);
    return dateObj >= this.minDate && dateObj <= this.maxDate;
  },

  isDayJoinedValid() {
    if (!this.user.day_joined || !this.user.date_of_birth) return true;
    const dateObj = new Date(this.user.day_joined);
    const dobObj = new Date(this.user.date_of_birth);
    return dateObj > dobObj;
  },

  isCommunistPartyJoinedValid() {
    if (!this.user.communist_party_joined || !this.user.date_of_birth) return true;
    const dateObj = new Date(this.user.communist_party_joined);
    const dobObj = new Date(this.user.date_of_birth);
    return dateObj > dobObj;
  },
  },

  methods: {
    formatDateToDDMMYYYY(date) {
      const day = String(date.getDate()).padStart(2, '0');
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const year = date.getFullYear();
      return `${day}/${month}/${year}`;
    },

    handleFieldFocus(fieldName) {
      this.touchedFields[fieldName] = true;
    },

    mysqlDateFormatter(date) {
      if (!date) return '';
      const year = date.getFullYear();
      const month = (date.getMonth() + 1).toString().padStart(2, '0');
      const day = date.getDate().toString().padStart(2, '0');
      return `${year}-${month}-${day}`;
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

    async getNCUsers() {
      try {
        const response = await axios.get(generateUrl('apps/qlcb/nc_users'))
        this.nc_users = response.data.nc_users
      } catch (e) {
        console.error(e)
      }
    },

    async createUser() {
      try {
        console.log(this.user)
        const response = await axios.post('/apps/qlcb/create_user', {
          qlcb_uid: this.user.qlcb_uid.userId,
          full_name: this.user.full_name,
          date_of_birth: this.mysqlDateFormatter(this.user.date_of_birth),
          gender: this.user.gender.value,
          phone: this.user.phone,
          address: this.user.address,
          id_number: this.user.id_number,
          email: this.user.email,
          position_id: this.user.position_id,
          coefficients_salary: this.user.coefficients_salary,
          tax: this.user.tax,
          communist_party_joined: this.user.communist_party_joined ? this.mysqlDateFormatter(this.user.communist_party_joined) : null,
          unit_id: this.user.unit_id,
          day_joined: this.mysqlDateFormatter(this.user.day_joined)
        });
        showSuccess(t('qlcb', 'Thêm thành công'));
        this.reset()
      } catch (e) {
        console.log(e)
        if (e.response && e.response.data) {
          showError(e.response.data.message);
        } else {
          console.error(e);
          showError('Lỗi không xác định.');
        }
      }
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

    reset() {
      this.touchedFields.phone = false
      this.touchedFields.qlcb_uid = false
      this.touchedFields.full_name = false
      this.touchedFields.date_of_birth = false
      this.touchedFields.gender = false
      this.touchedFields.address = false
      this.touchedFields.id_number = false
      this.touchedFields.email = false
      this.touchedFields.position_id = false
      this.touchedFields.coefficients_salary = false
      this.touchedFields.tax = false
      this.touchedFields.unit_id = false
      this.touchedFields.day_joined = false
      this.user.qlcb_uid = null
      this.user.full_name = ""
      this.user.date_of_birth = null
      this.user.gender = null
      this.user.phone = ""
      this.user.address = ""
      this.user.id_number = ""
      this.user.email = ""
      this.user.position_id = ""
      this.user.coefficients_salary = ""
      this.user.tax = ""
      this.user.communist_party_joined = null
      this.user.unit_id = ""
      this.user.day_joined = null
      this.getUnits()
      this.getPositions()
      this.getNCUsers()
    }
  },

  mounted() {
    this.getUnits()
    this.getPositions()
    this.getNCUsers()
    this.attachBlurListener(this.$refs.qlcb_uid, 'qlcb_uid');
    this.attachBlurListener(this.$refs.gender, 'gender');
    this.attachBlurListener(this.$refs.date_of_birth, 'date_of_birth');
    this.attachBlurListener(this.$refs.day_joined, 'day_joined');
  },
}
</script>

<style scoped>
.content {
  display: flex;
  align-items: center;
  flex-direction: column;
  justify-content: center;
  height: 100%;
}

.grid-container {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  grid-column-gap: 40px;
  grid-row-gap: 25px;
  padding: 15px;
  width: 100%;
  margin: auto;
}

.grid-item {
  display: flex;
  flex-direction: column;
}

.grid-item label {
  margin-bottom: 4px;
}

.grid-item input,
.grid-item .nc-datetime-picker,
.grid-item .nc-multiselect {
  width: 100%;
}

.validation-error {
  color: red;
  font-size: 0.8em;
}

input {
  height: 44px !important
}

::v-deep .nc-picker {
  width: 100% !important;
}

::v-deep .multiselect__tags {
  border: 2px solid #949494 !important;
}

::v-deep .multiselect__tags:hover {
  border-color: #3287b5 !important;
}

::v-deep .mx-input {
  height: 44px !important;
}

.validation-error-container {
  height: 10px;
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

.full-width {
  grid-column: 1 / -1;
}
</style>