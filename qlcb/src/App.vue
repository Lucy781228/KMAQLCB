<template>
	<NcEmptyContent v-if="!user.isAdmin" title="Bạn không có quyền truy cập" />
	<NcContent v-else id="content" app-name="qlcb">
		<NcAppNavigation>
			<div class="nav">
				<NcAppNavigationItem id="files" :exact="true" :title="t('qlcb', 'Quản lý hồ sơ')" to="/files">
					<template #icon>
						<NoteMultiple :size="20" />
					</template>
				</NcAppNavigationItem>
				<NcAppNavigationItem id="search" :exact="true" :title="t('qlcb', 'Tìm kiếm')" to="/search">
					<template #icon>
						<Magnify :size="20" />
					</template>
				</NcAppNavigationItem>
				<!-- <NcAppNavigationItem id="analyst" :exact="true" :title="t('qlcb', 'Thống kê và báo cáo')" to="/analyst">
					<template #icon>
						<Poll :size="20" />
					</template>
				</NcAppNavigationItem> -->
			</div>
		</NcAppNavigation>

		<NcAppContent class="parent">
			<router-view />
		</NcAppContent>
	</NcContent>
</template>

<script>
import { NcAppContent, NcActions, NcActionButton, NcButton } from "@nextcloud/vue";
import { NcAppNavigation, NcEmptyContent } from "@nextcloud/vue";
import { NcAppNavigationItem } from "@nextcloud/vue";
import { NcContent } from "@nextcloud/vue";
import NoteMultiple from 'vue-material-design-icons/NoteMultiple'
import Magnify from 'vue-material-design-icons/Magnify'
import Poll from 'vue-material-design-icons/Poll'
import axios from "@nextcloud/axios";
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess } from '@nextcloud/dialogs'
import { getCurrentUser } from '@nextcloud/auth'
export default {
	name: "App",
	components: {
		NcAppContent,
		NcAppNavigation,
		NcAppNavigationItem,
		NcContent,
		NoteMultiple,
		Magnify,
		NcActionButton,
		NcActions,
		NcButton,
		NcEmptyContent,
		Poll
	},

	data() {
		return {
			user: getCurrentUser()
		};
	},
};

</script>

<style scoped>
.nav {
	margin: 10px;
}

.parent {
	position: relative;
	padding: 20px
}
</style>